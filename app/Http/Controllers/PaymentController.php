<?php

namespace App\Http\Controllers;

use App\Services\MobilPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

/**
 * PaymentController - Controller pentru procesarea plăților
 * 
 * Acest controller gestionează:
 * - Inițierea plăților MobilPay
 * - Procesarea răspunsurilor IPN
 * - Redirecționarea clientului după plată
 */
class PaymentController extends Controller
{
    /**
     * MobilPayService instance
     * 
     * @var MobilPayService
     */
    protected $mobilPayService;

    /**
     * Constructor
     * 
     * @param MobilPayService $mobilPayService
     */
    public function __construct(MobilPayService $mobilPayService)
    {
        $this->mobilPayService = $mobilPayService;
    }

    /**
     * Afișează pagina de plată MobilPay
     * 
     * @param int $orderId
     * @return \Illuminate\View\View
     */
    public function showMobilPayPayment($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Verifică dacă comanda aparține utilizatorului curent
        if (auth()->check() && $order->user_id !== auth()->id()) {
            abort(403, 'Nu aveți permisiunea să accesați această comandă.');
        }

        return view('frontend.payment.mobilpay', compact('order'));
    }

    /**
     * Inițiază o plată cu MobilPay
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startMobilPayPayment(Request $request)
    {
        // Verifică dacă MobilPay este configurat
        if (!$this->mobilPayService->isConfigured()) {
            return back()->with('error', 'Plățile cu cardul nu sunt configurate momentan.');
        }

        // Validează datele plății
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:3',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        try {
            // Generează URL-ul de plată
            $paymentUrl = $this->mobilPayService->generatePaymentUrl([
                'amount' => $validated['amount'],
                'currency' => $validated['currency'],
                'order_id' => $validated['order_id'],
                'description' => 'Comanda #' . $validated['order_id'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? '',
                'address' => $validated['address'] ?? '',
                'city' => $validated['city'] ?? '',
                'country' => $validated['country'] ?? 'RO',
            ]);

            // Redirecționează către pagina de plată MobilPay
            return redirect()->away($paymentUrl);

        } catch (\Exception $e) {
            Log::error('MobilPay payment initiation failed', [
                'order_id' => $validated['order_id'],
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'A apărut o eroare la inițierea plății. Vă rugăm să încercați din nou.');
        }
    }

    /**
     * Procesează răspunsul IPN de la MobilPay
     * 
     * Acest endpoint este apelat de MobilPay pentru confirmarea plății
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function mobilPayConfirm(Request $request)
    {
        Log::info('MobilPay IPN received', $request->all());

        try {
            // Procesează IPN-ul
            $result = $this->mobilPayService->processIpn($request->all());

            if (!$result['success']) {
                Log::error('MobilPay IPN processing failed', ['message' => $result['message']]);
                return response('ERROR: ' . $result['message'], 400);
            }

            // Actualizează statusul comenzii
            $order = Order::find($result['order_id']);
            if ($order) {
                $order->payment_status = $result['status'];
                $order->save();

                Log::info('Order payment status updated', [
                    'order_id' => $order->id,
                    'status' => $result['status'],
                ]);
            } else {
                Log::error('Order not found for IPN', ['order_id' => $result['order_id']]);
            }

            // Returnează răspunsul de succes către MobilPay
            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('MobilPay IPN processing exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response('ERROR: Internal server error', 500);
        }
    }

    /**
     * Procesează redirecționarea clientului după plată
     * 
     * Acest endpoint este apelat când clientul este redirecționat înapoi din pagina MobilPay
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function mobilPayReturn(Request $request)
    {
        Log::info('MobilPay return received', $request->all());

        try {
            $orderId = $request->input('order_id');
            $status = $request->input('action', 'pending');

            // Mapează statusul
            $statusMap = [
                'confirmed' => 'paid',
                'confirmed_pending' => 'pending',
                'paid_pending' => 'pending',
                'paid' => 'paid',
                'canceled' => 'cancelled',
                'credit' => 'refunded',
            ];

            $paymentStatus = $statusMap[$status] ?? 'pending';

            // Actualizează statusul comenzii dacă este necesar
            $order = Order::find($orderId);
            if ($order) {
                $order->payment_status = $paymentStatus;
                $order->save();
            }

            // Redirecționează către pagina comenzii cu mesaj corespunzător
            if ($paymentStatus === 'paid') {
                return redirect()->route('orders.show', $orderId)
                    ->with('success', 'Plata a fost procesată cu succes!');
            } elseif ($paymentStatus === 'cancelled') {
                return redirect()->route('orders.show', $orderId)
                    ->with('error', 'Plata a fost anulată.');
            } else {
                return redirect()->route('orders.show', $orderId)
                    ->with('info', 'Plata este în procesare.');
            }

        } catch (\Exception $e) {
            Log::error('MobilPay return processing exception', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('home')
                ->with('error', 'A apărut o eroare la procesarea răspunsului de plată.');
        }
    }
}
