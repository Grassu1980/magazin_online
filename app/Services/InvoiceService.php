<?php

namespace App\Services;

use App\Helpers\InvoiceHelper;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Serviciu pentru generarea facturilor PDF
 */
class InvoiceService
{
    /**
     * Generează o factură pentru o comandă
     *
     * @param Order $order
     * @param string $invoiceType
     * @return Invoice
     */
    public function generateInvoice(Order $order, $invoiceType = 'individual')
    {
        // Obține setările de facturare
        $settings = InvoiceHelper::getInvoiceSettings();

        // Generează numărul facturii
        $invoiceNumber = Invoice::generateInvoiceNumber(
            $settings['invoice_prefix'],
            $settings['invoice_start_number']
        );

        // Calculează totalul și TVA per produs
        $itemsWithVat = [];
        $subtotal = 0;
        $totalVat = 0;

        foreach ($order->items as $item) {
            $product = $item->product;
            $vatRate = $product->vat_rate ?? 21;
            $priceWithoutVat = $product->price_without_vat ?? ($item->price / (1 + $vatRate / 100));
            $vatValue = $priceWithoutVat * ($vatRate / 100);
            $lineTotal = $priceWithoutVat + $vatValue;
            $lineSubtotal = $priceWithoutVat * $item->quantity;
            $lineVat = $vatValue * $item->quantity;
            $lineTotalWithQuantity = $lineTotal * $item->quantity;

            $itemsWithVat[] = [
                'product' => $product,
                'quantity' => $item->quantity,
                'price_without_vat' => $priceWithoutVat,
                'vat_rate' => $vatRate,
                'vat_value' => $vatValue,
                'line_total' => $lineTotal,
                'line_subtotal' => $lineSubtotal,
                'line_vat' => $lineVat,
                'line_total_with_quantity' => $lineTotalWithQuantity,
            ];

            $subtotal += $lineSubtotal;
            $totalVat += $lineVat;
        }

        $total = $subtotal + $totalVat;

        // Pregătește datele clientului
        $clientData = [
            'name' => $this->removeDiacritics($order->customer_name),
            'email' => $order->customer_email,
            'phone' => $order->customer_phone,
            'address' => $this->removeDiacritics($order->shipping_address),
            'city' => $this->removeDiacritics($order->shipping_city),
        ];

        // Adaugă datele firmei dacă este persoană juridică
        $companyData = [];
        $user = $order->user;

        // Debug logging pentru a vedea ce se întâmplă
        \Log::info('Invoice generation start', [
            'invoice_type' => $invoiceType,
            'order_id' => $order->id,
            'order_user_id' => $order->user_id,
            'customer_email' => $order->customer_email,
            'user_exists' => $user ? true : false,
            'user_is_company' => $user ? $user->is_company : false,
        ]);

        // Dacă comanda nu are utilizator sau utilizatorul nu este firmă, caută utilizatorul după email
        if ($invoiceType === 'company' && (!$user || !$user->is_company)) {
            $user = \App\Models\User::where('email', $order->customer_email)->where('is_company', true)->first();
            \Log::info('Searched for company user by email', [
                'email' => $order->customer_email,
                'found_user' => $user ? true : false,
                'found_user_is_company' => $user ? $user->is_company : false,
            ]);
        }

        if ($invoiceType === 'company' && $user && $user->is_company) {
            $companyData = [
                'name' => $this->removeDiacritics($user->company_name),
                'cui' => $user->company_cui,
                'reg' => $user->company_reg,
                'address' => $this->removeDiacritics($user->company_address),
                'iban' => $user->company_iban,
            ];

            // Actualizează datele clientului cu datele firmei pentru afișare
            $clientData = [
                'name' => $this->removeDiacritics($user->company_name),
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'address' => $this->removeDiacritics($user->company_address ?? $order->shipping_address),
                'city' => $this->removeDiacritics($order->shipping_city),
            ];

            // Debug logging
            \Log::info('Company invoice data', [
                'invoice_type' => $invoiceType,
                'user_id' => $user->id,
                'is_company' => $user->is_company,
                'company_name' => $user->company_name,
                'company_cui' => $user->company_cui,
                'company_reg' => $user->company_reg,
                'company_address' => $user->company_address,
                'company_data' => $companyData,
                'client_data' => $clientData,
            ]);
        }

        // Generează PDF-ul
        $pdf = $this->generatePdf($order, $settings, $invoiceNumber, $subtotal, $totalVat, $total, $itemsWithVat, $clientData, $companyData);

        // Salvează PDF-ul
        $filename = 'invoice_' . $invoiceNumber . '.pdf';
        $pdfPath = 'invoices/' . $filename;
        Storage::disk('public')->put($pdfPath, $pdf->output());

        // Salvează factura în baza de date
        $invoiceData = [
            'invoice_number' => $invoiceNumber,
            'order_id' => $order->id,
            'client_name' => ($invoiceType === 'company' && $user && $user->is_company) ? $user->company_name : $order->customer_name,
            'client_email' => $order->customer_email,
            'subtotal' => $subtotal,
            'tva_amount' => $totalVat,
            'tva_rate' => null, // Multiple VAT rates possible now
            'total' => $total,
            'pdf_path' => $pdfPath,
            'invoice_type' => $invoiceType,
        ];

        // Adaugă datele firmei în factura dacă este persoană juridică
        if ($invoiceType === 'company' && $user && $user->is_company) {
            $invoiceData['company_name'] = $user->company_name;
            $invoiceData['company_cui'] = $user->company_cui;
            $invoiceData['company_reg'] = $user->company_reg;
            $invoiceData['company_address'] = $user->company_address;
            $invoiceData['company_iban'] = $user->company_iban;
        }

        $invoice = Invoice::create($invoiceData);

        return $invoice;
    }
    
    /**
     * Generează PDF-ul factură
     *
     * @param Order $order
     * @param array $settings
     * @param string $invoiceNumber
     * @param float $subtotal
     * @param float $tvaAmount
     * @param float $total
     * @param array $itemsWithVat
     * @param array $clientData
     * @param array $companyData
     * @return \Barryvdh\DomPDF\PDF
     */
    private function generatePdf(Order $order, array $settings, string $invoiceNumber, float $subtotal, float $tvaAmount, float $total, array $itemsWithVat, array $clientData, array $companyData)
    {
        $data = [
            'invoice_number' => $invoiceNumber,
            'invoice_date' => now()->format('d.m.Y'),
            'due_date' => now()->addDays(30)->format('d.m.Y'),
            'company' => $this->removeDiacritics($settings),
            'client' => $clientData,
            'company_data' => $companyData,
            'items' => $itemsWithVat,
            'subtotal' => $subtotal,
            'tva_amount' => $tvaAmount,
            'total' => $total,
            'order_number' => $order->order_number,
        ];

        return Pdf::loadView('backend.invoices.pdf', $data);
    }

    /**
     * Elimină diacriticele din text
     * 
     * @param mixed $data
     * @return mixed
     */
    private function removeDiacritics($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'removeDiacritics'], $data);
        }
        
        if (is_string($data)) {
            $diacritics = [
                'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ț' => 't',
                'Ă' => 'A', 'Â' => 'A', 'Î' => 'I', 'Ș' => 'S', 'Ț' => 'T',
            ];
            
            return str_replace(array_keys($diacritics), array_values($diacritics), $data);
        }
        
        return $data;
    }
    
    /**
     * Descarcă o factură existentă
     * 
     * @param Invoice $invoice
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadInvoice(Invoice $invoice)
    {
        $filePath = storage_path('app/public/' . $invoice->pdf_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Factura nu a fost găsită.');
        }
        
        return response()->download($filePath, $invoice->invoice_number . '.pdf');
    }
}
