<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Stock;
use App\Models\StockHistory;
use Illuminate\Support\Facades\DB;

class ReceiptService
{
    /**
     * Calculează TVA pentru un preț cu TVA
     *
     * @param float $priceWithVat
     * @param int $vatRate
     * @return array
     */
    public function calculateVat($priceWithVat, $vatRate)
    {
        $priceWithoutVat = $priceWithVat / (1 + $vatRate / 100);
        $vatValue = $priceWithoutVat * ($vatRate / 100);

        return [
            'price_without_vat' => round($priceWithoutVat, 2),
            'vat_value' => round($vatValue, 2),
            'price_with_vat' => round($priceWithVat, 2),
        ];
    }

    /**
     * Calculează Prețul Mediu Ponderat (PMP)
     *
     * @param Product $product
     * @param float $newQuantity
     * @param float $newPrice
     * @return float
     */
    public function calculatePMP(Product $product, $newQuantity, $newPrice)
    {
        $stock = $product->stockRecord;
        
        if ($stock && $stock->quantity > 0) {
            $oldQuantity = $stock->quantity;
            $oldPrice = $stock->cost_price ?? 0;
            
            $pmp = (($oldQuantity * $oldPrice) + ($newQuantity * $newPrice)) / ($oldQuantity + $newQuantity);
        } else {
            $pmp = $newPrice;
        }

        return round($pmp, 2);
    }

    /**
     * Actualizează stocul produsului
     *
     * @param Product $product
     * @param float $quantity
     * @param float $purchasePrice
     * @param Receipt $receipt
     * @return void
     */
    public function updateStock(Product $product, $quantity, $purchasePrice, Receipt $receipt)
    {
        DB::beginTransaction();
        
        try {
            $stock = $product->stockRecord ?? new Stock();
            $stock->product_id = $product->id;
            
            $oldQuantity = $stock->quantity ?? 0;
            $oldPrice = $stock->cost_price ?? 0;
            
            $newQuantity = $oldQuantity + $quantity;
            $newPrice = $this->calculatePMP($product, $quantity, $purchasePrice);
            
            $stock->quantity = $newQuantity;
            $stock->cost_price = $newPrice;
            $stock->save();
            
            // Înregistrează în StockHistory
            StockHistory::create([
                'stock_id' => $stock->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'type' => 'IN',
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'cost_price' => $purchasePrice,
                'receipt_id' => $receipt->id,
            ]);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Creează o recepție nouă
     *
     * @param array $data
     * @return Receipt
     */
    public function createReceipt(array $data)
    {
        DB::beginTransaction();
        
        try {
            $receipt = Receipt::create([
                'supplier_id' => $data['supplier_id'],
                'invoice_number' => $data['invoice_number'],
                'invoice_date' => $data['invoice_date'],
                'receipt_date' => $data['receipt_date'],
                'total_without_vat' => 0,
                'total_vat' => 0,
                'total_with_vat' => 0,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $totalWithoutVat = 0;
            $totalVat = 0;

            foreach ($data['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $vatRate = $itemData['vat_rate'];
                $priceWithVat = $itemData['purchase_price_with_vat'];
                
                $vatCalculation = $this->calculateVat($priceWithVat, $vatRate);
                $quantity = $itemData['quantity'];
                
                $lineTotalWithoutVat = $vatCalculation['price_without_vat'] * $quantity;
                $lineTotalVat = $vatCalculation['vat_value'] * $quantity;
                $lineTotalWithVat = $vatCalculation['price_with_vat'] * $quantity;

                ReceiptItem::create([
                    'receipt_id' => $receipt->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'purchase_price_without_vat' => $vatCalculation['price_without_vat'],
                    'vat_rate' => $vatRate,
                    'vat_value' => $vatCalculation['vat_value'],
                    'purchase_price_with_vat' => $vatCalculation['price_with_vat'],
                    'line_total_without_vat' => $lineTotalWithoutVat,
                    'line_total_vat' => $lineTotalVat,
                    'line_total_with_vat' => $lineTotalWithVat,
                ]);

                // Actualizează prețul de achiziție al produsului
                $product->update([
                    'purchase_price_without_vat' => $vatCalculation['price_without_vat'],
                ]);

                // Actualizează stocul
                $this->updateStock($product, $quantity, $vatCalculation['price_without_vat'], $receipt);

                $totalWithoutVat += $lineTotalWithoutVat;
                $totalVat += $lineTotalVat;
            }

            $receipt->update([
                'total_without_vat' => $totalWithoutVat,
                'total_vat' => $totalVat,
                'total_with_vat' => $totalWithoutVat + $totalVat,
            ]);

            DB::commit();

            return $receipt->load('items.product', 'supplier');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Generează numărul NIR
     *
     * @return string
     */
    public function generateReceiptNumber()
    {
        $lastReceipt = Receipt::orderBy('id', 'desc')->first();
        
        if ($lastReceipt) {
            $lastNumber = (int) str_replace('NIR-', '', $lastReceipt->id);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'NIR-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
