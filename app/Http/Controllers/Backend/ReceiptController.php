<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\ReceiptService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Afișează lista de recepții
     */
    public function index(Request $request)
    {
        $query = Receipt::with(['items.product', 'supplier']);

        if ($request->has('search') && $request->search) {
            $query->where('id', 'like', "%{$request->search}%")
                  ->orWhereHas('supplier', function($q) use ($request) {
                      $q->where('name', 'like', "%{$request->search}%");
                  });
        }

        if ($request->has('supplier_id') && $request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('receipt_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('receipt_date', '<=', $request->date_to);
        }

        $receipts = $query->latest()->paginate(20)->appends($request->query());
        $suppliers = Supplier::all();
        
        return view('backend.receipts.index', compact('receipts', 'suppliers'));
    }

    /**
     * Afișează formularul pentru creare recepție
     */
    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        
        return view('backend.receipts.create', compact('products', 'suppliers'));
    }

    /**
     * Salvează o nouă recepție
     */
    public function store(Request $request, ReceiptService $receiptService)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|max:50',
            'invoice_date' => 'required|date',
            'receipt_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.purchase_price_with_vat' => 'required|numeric|min:0',
            'items.*.vat_rate' => 'required|integer|in:0,11,21',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $receipt = $receiptService->createReceipt($request->all());
            
            return redirect()->route('admin.receipts.show', $receipt->id)
                ->with('success', 'Recepția a fost creată cu succes!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Eroare la crearea recepției: ' . $e->getMessage());
        }
    }

    /**
     * Afișează detaliile unei recepții
     */
    public function show(Receipt $receipt)
    {
        $receipt->load('items.product', 'supplier', 'creator');
        return view('backend.receipts.show', compact('receipt'));
    }

    /**
     * Descarcă PDF-ul NIR
     */
    public function downloadPdf(Receipt $receipt)
    {
        $receipt->load('items.product', 'supplier', 'creator');
        
        $data = [
            'receipt' => $receipt,
            'company' => [
                'name' => setting('company_name', 'Nume Firmă'),
                'cui' => setting('company_reg_number', ''),
                'reg_com' => setting('company_trade_number', ''),
                'address' => setting('company_address', ''),
                'iban' => setting('company_iban', ''),
                'bank' => setting('company_bank', ''),
                'email' => setting('company_email', ''),
                'phone' => setting('company_phone', ''),
            ],
        ];

        $pdf = Pdf::loadView('backend.receipts.pdf', $data);
        
        return $pdf->download('NIR-' . $receipt->id . '.pdf');
    }

    /**
     * Șterge o recepție
     */
    public function destroy(Receipt $receipt)
    {
        try {
            // Într-o implementare completă, ar trebui să scădem stocul
            // dar pentru moment doar ștergem recepția
            $receipt->delete();
            
            return redirect()->route('admin.receipts.index')
                ->with('success', 'Recepția a fost ștearsă cu succes!');
        } catch (\Exception $e) {
            return back()->with('error', 'Eroare la ștergerea recepției: ' . $e->getMessage());
        }
    }
}
