<?php

use App\Models\Setting;

function setting($key, $default = null)
{
    return Setting::where('key', $key)->value('value') ?? $default;
}

function getMailConfig()
{
    $smtpHost = setting('smtp_host');
    $smtpPort = setting('smtp_port');
    $smtpUsername = setting('smtp_username');
    $smtpPassword = setting('smtp_password');
    $smtpEncryption = setting('smtp_encryption');
    $smtpFromEmail = setting('smtp_from_email');

    // If SMTP settings are configured, use them
    if ($smtpHost && $smtpPort && $smtpUsername && $smtpPassword) {
        return [
            'transport' => 'smtp',
            'host' => $smtpHost,
            'port' => $smtpPort,
            'encryption' => $smtpEncryption,
            'username' => $smtpUsername,
            'password' => decrypt($smtpPassword),
            'from' => [
                'address' => $smtpFromEmail ?? config('mail.from.address'),
                'name' => config('mail.from.name'),
            ],
        ];
    }

    // Fallback to default config
    return [
        'transport' => config('mail.default', 'smtp'),
        'host' => config('mail.mailers.smtp.host'),
        'port' => config('mail.mailers.smtp.port'),
        'encryption' => config('mail.mailers.smtp.encryption'),
        'username' => config('mail.mailers.smtp.username'),
        'password' => config('mail.mailers.smtp.password'),
        'from' => [
            'address' => config('mail.from.address'),
            'name' => config('mail.from.name'),
        ],
    ];
}

/**
 * getMobilPayConfig - Returnează configurația MobilPay din baza de date
 * 
 * Această funcție citește toate setările MobilPay din baza de date
 * și le returnează într-un array pentru utilizare în aplicație.
 * Returnează setările active în funcție de modul selectat (sandbox sau live).
 * 
 * @return array Configurația MobilPay
 */
function getMobilPayConfig()
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
 * getImageSettings - Returnează setările de procesare a imaginilor
 * 
 * Această funcție citește setările de procesare a imaginilor din baza de date
 * și le returnează într-un array pentru utilizare în aplicație.
 * 
 * @return array Setările de procesare a imaginilor
 */
function getImageSettings()
{
    return [
        'max_size' => (int) setting('image_max_size', 800),
        'quality' => (int) setting('image_quality', 80),
        'format' => setting('image_format', 'webp'),
        'background_color' => setting('image_background_color', '#ffffff'),
    ];
}
