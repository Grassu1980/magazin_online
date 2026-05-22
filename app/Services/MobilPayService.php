<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * MobilPayService - Serviciu pentru integrarea cu MobilPay
 * 
 * Acest serviciu gestionează:
 * - Generarea cererilor de plată
 * - Semnarea cererilor cu cheia privată
 * - Procesarea răspunsurilor IPN
 * - Validarea semnăturilor
 */
class MobilPayService
{
    /**
     * Obține configurația MobilPay din baza de date
     * 
     * @return array
     */
    public function getConfig()
    {
        $mode = setting('mobilpay_mode', 'sandbox');
        $isSandbox = $mode === 'sandbox';

        return [
            'mode' => $mode,
            'is_sandbox' => $isSandbox,
            'signature' => $isSandbox 
                ? setting('mobilpay_signature_sandbox') 
                : setting('mobilpay_signature_live'),
            'confirm_url' => setting('mobilpay_confirm_url'),
            'return_url' => setting('mobilpay_return_url'),
            'private_key_path' => $isSandbox 
                ? setting('mobilpay_private_key_sandbox') 
                : setting('mobilpay_private_key_live'),
            'public_key_path' => $isSandbox 
                ? setting('mobilpay_public_key_sandbox') 
                : setting('mobilpay_public_key_live'),
        ];
    }

    /**
     * Generează URL-ul de plată MobilPay
     * 
     * @param array $paymentData Datele plății (amount, currency, order_id, description)
     * @return string URL-ul de plată
     */
    public function generatePaymentUrl(array $paymentData)
    {
        $config = $this->getConfig();
        
        // URL-ul MobilPay în funcție de mediul (sandbox/production)
        $baseUrl = $config['is_sandbox'] 
            ? 'https://sandboxsecure.mobilpay.ro' 
            : 'https://secure.mobilpay.ro';
        
        // Citește cheia privată din fișier
        $privateKey = $this->readKeyFile($config['private_key_path']);
        
        // Generează parametrii cererii
        $params = [
            'signature' => $config['signature'],
            'amount' => $paymentData['amount'],
            'currency' => $paymentData['currency'] ?? 'RON',
            'order_id' => $paymentData['order_id'],
            'description' => $paymentData['description'] ?? 'Comanda #' . $paymentData['order_id'],
            'confirm_url' => $config['confirm_url'],
            'return_url' => $config['return_url'],
            'first_name' => $paymentData['first_name'] ?? '',
            'last_name' => $paymentData['last_name'] ?? '',
            'email' => $paymentData['email'] ?? '',
            'phone' => $paymentData['phone'] ?? '',
            'address' => $paymentData['address'] ?? '',
            'city' => $paymentData['city'] ?? '',
            'country' => $paymentData['country'] ?? 'RO',
        ];
        
        // Generează semnătura
        $params['signature'] = $this->generateSignature($params, $privateKey);
        
        // Construiește URL-ul
        $url = $baseUrl . '/payment?' . http_build_query($params);
        
        Log::info('MobilPay payment URL generated', [
            'order_id' => $paymentData['order_id'],
            'mode' => $config['mode'],
        ]);
        
        return $url;
    }

    /**
     * Citește cheia din fișier
     * 
     * @param string $path Calea către fișierul cheie
     * @return string|null Conținutul cheii sau null dacă fișierul nu există
     */
    private function readKeyFile($path)
    {
        if (!$path) {
            return null;
        }

        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) {
            Log::error('MobilPay key file not found', ['path' => $fullPath]);
            return null;
        }

        return file_get_contents($fullPath);
    }

    /**
     * Generează semnătura pentru cererea de plată
     * 
     * @param array $params Parametrii cererii
     * @param string $privateKey Cheia privată
     * @return string Semnătura generată
     */
    private function generateSignature(array $params, $privateKey)
    {
        // Sortează parametrii alfabetic
        ksort($params);
        
        // Construiește string-ul pentru semnătură
        $signatureString = '';
        foreach ($params as $key => $value) {
            if ($key !== 'signature') {
                $signatureString .= $key . '=' . $value . '&';
            }
        }
        $signatureString = rtrim($signatureString, '&');
        
        // Generează semnătura folosind cheia privată
        // Notă: Aceasta este o implementare simplificată
        // În producție, folosiți metoda oficială MobilPay de semnare
        return hash_hmac('sha256', $signatureString, $privateKey);
    }

    /**
     * Procesează răspunsul IPN de la MobilPay
     * 
     * Acest endpoint este apelat de MobilPay pentru confirmarea plății
     * 
     * @param array $ipnData Datele IPN
     * @return array Rezultatul procesării
     */
    public function processIpn(array $ipnData)
    {
        $config = $this->getConfig();
        
        // Citește cheia publică din fișier
        $publicKey = $this->readKeyFile($config['public_key_path']);
        
        if (!$publicKey) {
            Log::error('MobilPay IPN: Public key file not found');
            return [
                'success' => false,
                'message' => 'Cheia publică nu a fost găsită',
            ];
        }
        
        // Validează semnătura
        if (!$this->validateSignature($ipnData, $publicKey)) {
            Log::error('MobilPay IPN signature validation failed', ['ipnData' => $ipnData]);
            return [
                'success' => false,
                'message' => 'Semnătură invalidă',
            ];
        }
        
        // Extrage statusul plății
        $status = $ipnData['action'] ?? '';
        $orderId = $ipnData['order_id'] ?? '';
        
        Log::info('MobilPay IPN received', [
            'order_id' => $orderId,
            'status' => $status,
            'mode' => $config['mode'],
        ]);
        
        // Mapează statusul MobilPay la statusul intern
        $paymentStatus = $this->mapStatus($status);
        
        return [
            'success' => true,
            'order_id' => $orderId,
            'status' => $paymentStatus,
            'raw_status' => $status,
            'message' => 'IPN procesat cu succes',
        ];
    }

    /**
     * Validează semnătura din răspunsul IPN
     * 
     * @param array $ipnData Datele IPN
     * @param string $publicKey Cheia publică
     * @return bool
     */
    private function validateSignature(array $ipnData, $publicKey)
    {
        // Extrage semnătura primită
        $receivedSignature = $ipnData['signature'] ?? '';
        
        // Generează semnătura pentru validare
        $params = $ipnData;
        unset($params['signature']);
        ksort($params);
        
        $signatureString = '';
        foreach ($params as $key => $value) {
            $signatureString .= $key . '=' . $value . '&';
        }
        $signatureString = rtrim($signatureString, '&');
        
        $calculatedSignature = hash_hmac('sha256', $signatureString, $publicKey);
        
        return hash_equals($calculatedSignature, $receivedSignature);
    }

    /**
     * Mapează statusul MobilPay la statusul intern
     * 
     * @param string $mobilpayStatus Statusul MobilPay
     * @return string Statusul intern
     */
    private function mapStatus($mobilpayStatus)
    {
        $statusMap = [
            'confirmed' => 'paid',
            'confirmed_pending' => 'pending',
            'paid_pending' => 'pending',
            'paid' => 'paid',
            'canceled' => 'cancelled',
            'credit' => 'refunded',
        ];
        
        return $statusMap[$mobilpayStatus] ?? 'pending';
    }

    /**
     * Verifică dacă integrarea MobilPay este configurată
     * 
     * @return bool
     */
    public function isConfigured()
    {
        $config = $this->getConfig();
        
        return !empty($config['signature']) 
            && !empty($config['private_key_path']) 
            && !empty($config['public_key_path'])
            && file_exists(storage_path('app/public/' . $config['private_key_path']))
            && file_exists(storage_path('app/public/' . $config['public_key_path']));
    }
}
