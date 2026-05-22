<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\EFacturaService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        if ($request->has('search') && $request->search) {
            $query->where('order_number', 'like', "%{$request->search}%")
                  ->orWhere('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_email', 'like', "%{$request->search}%");
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20)->appends($request->query());
        return view('backend.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user');
        $invoice = Invoice::where('order_id', $order->id)->first();
        return view('backend.orders.show', compact('order', 'invoice'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        if ($request->expectsJson() || $request->isJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Status actualizat.']);
        }

        return back()->with('success', 'Statusul comenzii a fost actualizat!');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded'
        ]);

        $order->update(['payment_status' => $request->payment_status]);

        if ($request->expectsJson() || $request->isJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Status plată actualizat.']);
        }

        return back()->with('success', 'Statusul plății a fost actualizat!');
    }

    /**
     * Generează o factură pentru comandă
     */
    public function generateInvoice(Order $order, Request $request, InvoiceService $invoiceService)
    {
        // Verifică dacă există deja o factură
        $existingInvoice = Invoice::where('order_id', $order->id)->first();
        if ($existingInvoice) {
            return back()->with('error', 'Există deja o factură pentru această comandă.');
        }

        try {
            $invoiceType = $request->input('invoice_type', 'individual');
            $invoice = $invoiceService->generateInvoice($order, $invoiceType);
            return back()->with('success', 'Factura a fost generată cu succes!');
        } catch (\Exception $e) {
            return back()->with('error', 'Eroare la generarea facturii: ' . $e->getMessage());
        }
    }

    /**
     * Descarcă o factură
     */
    public function downloadInvoice(Invoice $invoice, InvoiceService $invoiceService)
    {
        return $invoiceService->downloadInvoice($invoice);
    }

    /**
     * Trimite factura către eFactura ANAF
     */
    public function sendToEFactura(Invoice $invoice, EFacturaService $eFacturaService)
    {
        try {
            $result = $eFacturaService->sendToAnaf($invoice);
            
            if ($result['success']) {
                return back()->with('success', $result['message']);
            } else {
                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Eroare la trimiterea facturii: ' . $e->getMessage());
        }
    }

    /**
     * Descarcă XML-ul facturii
     */
    public function downloadXml(Invoice $invoice, EFacturaService $eFacturaService)
    {
        return $eFacturaService->downloadXml($invoice);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Comandă ștearsă cu succes!');
    }
}