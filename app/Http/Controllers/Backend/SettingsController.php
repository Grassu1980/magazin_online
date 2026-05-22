<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function editContact()
    {
        $settings = [
            'phone' => setting('phone'),
            'email' => setting('email'),
            'facebook' => setting('facebook'),
            'instagram' => setting('instagram'),
            'youtube' => setting('youtube'),
            'whatsapp' => setting('whatsapp'),
            'address' => setting('address'),
            'schedule' => setting('schedule'),
            'map' => setting('map'),
            'contact_text' => setting('contact_text'),
        ];

        return view('backend.settings.contact', compact('settings'));
    }

    public function updateContact(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'schedule' => 'nullable|string|max:500',
            'map' => 'nullable|string',
            'contact_text' => 'nullable|string',
        ]);

        $fields = [
            'phone', 'email', 'facebook', 'instagram', 'youtube', 'whatsapp',
            'address', 'schedule', 'map', 'contact_text'
        ];

        foreach ($fields as $field) {
            Setting::updateOrCreate(
                ['key' => $field],
                ['value' => $request->$field]
            );
        }

        return back()->with('success', 'Setările de contact au fost actualizate cu succes!');
    }

    public function editGeneral()
    {
        $settings = [
            'site_name' => setting('site_name'),
            'meta_description' => setting('meta_description'),
            'welcome_title' => setting('welcome_title'),
            'welcome_description' => setting('welcome_description'),
            'primary_color' => setting('primary_color'),
            'secondary_color' => setting('secondary_color'),
            'logo' => setting('logo'),
            'logo_size' => setting('logo_size'),
            'favicon' => setting('favicon'),
            'smtp_from_email' => setting('smtp_from_email'),
            'smtp_host' => setting('smtp_host'),
            'smtp_port' => setting('smtp_port'),
            'smtp_username' => setting('smtp_username'),
            'smtp_password' => setting('smtp_password'),
            'smtp_encryption' => setting('smtp_encryption'),
            'mobilpay_mode' => setting('mobilpay_mode'),
            'mobilpay_signature_sandbox' => setting('mobilpay_signature_sandbox'),
            'mobilpay_signature_live' => setting('mobilpay_signature_live'),
            'mobilpay_private_key_sandbox' => setting('mobilpay_private_key_sandbox'),
            'mobilpay_private_key_live' => setting('mobilpay_private_key_live'),
            'mobilpay_public_key_sandbox' => setting('mobilpay_public_key_sandbox'),
            'mobilpay_public_key_live' => setting('mobilpay_public_key_live'),
            'mobilpay_confirm_url' => setting('mobilpay_confirm_url'),
            'mobilpay_return_url' => setting('mobilpay_return_url'),
            'image_max_size' => setting('image_max_size'),
            'image_max_file_size' => setting('image_max_file_size'),
            'image_quality' => setting('image_quality'),
            'image_format' => setting('image_format'),
            'image_background_color' => setting('image_background_color'),
            'company_name' => setting('company_name'),
            'company_reg_number' => setting('company_reg_number'),
            'company_trade_number' => setting('company_trade_number'),
            'company_address' => setting('company_address'),
            'company_iban' => setting('company_iban'),
            'company_bank' => setting('company_bank'),
            'company_email' => setting('company_email'),
            'company_phone' => setting('company_phone'),
            'invoice_prefix' => setting('invoice_prefix'),
            'invoice_start_number' => setting('invoice_start_number'),
            'invoice_footer_text' => setting('invoice_footer_text'),
            'efactura_api_key' => setting('efactura_api_key'),
            'efactura_client_id' => setting('efactura_client_id'),
            'efactura_client_secret' => setting('efactura_client_secret'),
            'efactura_environment' => setting('efactura_environment'),
        ];
        
        return view('backend.settings.general', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'welcome_title' => 'nullable|string|max:255',
            'welcome_description' => 'nullable|string|max:500',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'logo_size' => 'nullable|in:h-8,h-12,h-16,h-24',
            'favicon' => 'nullable|image|mimes:png,ico,jpg,jpeg,webp,svg|max:1024',
            'smtp_from_email' => 'nullable|email|max:255',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|in:,tls,ssl',
            'mobilpay_mode' => 'nullable|in:sandbox,live',
            'mobilpay_signature_sandbox' => 'nullable|string|max:255',
            'mobilpay_signature_live' => 'nullable|string|max:255',
            'mobilpay_private_key_sandbox' => 'nullable|file|mimes:key',
            'mobilpay_private_key_live' => 'nullable|file|mimes:key',
            'mobilpay_public_key_sandbox' => 'nullable|file|mimes:cer',
            'mobilpay_public_key_live' => 'nullable|file|mimes:cer',
            'mobilpay_confirm_url' => 'nullable|url|max:500',
            'mobilpay_return_url' => 'nullable|url|max:500',
            'image_max_size' => 'nullable|integer|min:100|max:2000',
            'image_max_file_size' => 'nullable|integer|min:500|max:10240',
            'image_quality' => 'nullable|integer|min:50|max:100',
            'image_format' => 'nullable|in:webp,jpg',
            'image_background_color' => 'nullable|string|max:7',
            // Invoice Settings
            'company_name' => 'nullable|string|max:255',
            'company_reg_number' => 'nullable|string|max:50',
            'company_trade_number' => 'nullable|string|max:50',
            'company_address' => 'nullable|string|max:500',
            'company_iban' => 'nullable|string|max:50',
            'company_bank' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'invoice_prefix' => 'nullable|string|max:10',
            'invoice_start_number' => 'nullable|integer|min:1',
            'invoice_footer_text' => 'nullable|string|max:500',
            // EFactura Settings
            'efactura_api_key' => 'nullable|string|max:255',
            'efactura_client_id' => 'nullable|string|max:255',
            'efactura_client_secret' => 'nullable|string|max:255',
            'efactura_environment' => 'nullable|in:sandbox,live',
        ]);

        Setting::updateOrCreate(['key' => 'site_name'], ['value' => $request->site_name]);
        Setting::updateOrCreate(['key' => 'meta_description'], ['value' => $request->meta_description]);
        Setting::updateOrCreate(['key' => 'welcome_title'], ['value' => $request->welcome_title]);
        Setting::updateOrCreate(['key' => 'welcome_description'], ['value' => $request->welcome_description]);
        Setting::updateOrCreate(['key' => 'primary_color'], ['value' => $request->primary_color ?? '#2563eb']);
        Setting::updateOrCreate(['key' => 'secondary_color'], ['value' => $request->secondary_color ?? '#1e40af']);
        Setting::updateOrCreate(['key' => 'logo_size'], ['value' => $request->logo_size ?? 'h-8']);

        // SMTP Settings
        Setting::updateOrCreate(['key' => 'smtp_from_email'], ['value' => $request->smtp_from_email]);
        Setting::updateOrCreate(['key' => 'smtp_host'], ['value' => $request->smtp_host]);
        Setting::updateOrCreate(['key' => 'smtp_port'], ['value' => $request->smtp_port]);
        Setting::updateOrCreate(['key' => 'smtp_username'], ['value' => $request->smtp_username]);

        // Encrypt password if provided
        if ($request->filled('smtp_password')) {
            Setting::updateOrCreate(['key' => 'smtp_password'], ['value' => encrypt($request->smtp_password)]);
        }

        Setting::updateOrCreate(['key' => 'smtp_encryption'], ['value' => $request->smtp_encryption]);

        // MobilPay Settings
        Setting::updateOrCreate(['key' => 'mobilpay_mode'], ['value' => $request->mobilpay_mode ?? 'sandbox']);
        Setting::updateOrCreate(['key' => 'mobilpay_signature_sandbox'], ['value' => $request->mobilpay_signature_sandbox]);
        Setting::updateOrCreate(['key' => 'mobilpay_signature_live'], ['value' => $request->mobilpay_signature_live]);
        Setting::updateOrCreate(['key' => 'mobilpay_confirm_url'], ['value' => $request->mobilpay_confirm_url]);
        Setting::updateOrCreate(['key' => 'mobilpay_return_url'], ['value' => $request->mobilpay_return_url]);

        // Handle file uploads for keys
        if ($request->hasFile('mobilpay_private_key_sandbox')) {
            $old = setting('mobilpay_private_key_sandbox');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $file = $request->file('mobilpay_private_key_sandbox');
            $name = 'mobilpay_private_sandbox_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('mobilpay', $name, 'public');
            Setting::updateOrCreate(['key' => 'mobilpay_private_key_sandbox'], ['value' => $path]);
        }

        if ($request->hasFile('mobilpay_private_key_live')) {
            $old = setting('mobilpay_private_key_live');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $file = $request->file('mobilpay_private_key_live');
            $name = 'mobilpay_private_live_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('mobilpay', $name, 'public');
            Setting::updateOrCreate(['key' => 'mobilpay_private_key_live'], ['value' => $path]);
        }

        if ($request->hasFile('mobilpay_public_key_sandbox')) {
            $old = setting('mobilpay_public_key_sandbox');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $file = $request->file('mobilpay_public_key_sandbox');
            $name = 'mobilpay_public_sandbox_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('mobilpay', $name, 'public');
            Setting::updateOrCreate(['key' => 'mobilpay_public_key_sandbox'], ['value' => $path]);
        }

        if ($request->hasFile('mobilpay_public_key_live')) {
            $old = setting('mobilpay_public_key_live');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $file = $request->file('mobilpay_public_key_live');
            $name = 'mobilpay_public_live_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('mobilpay', $name, 'public');
            Setting::updateOrCreate(['key' => 'mobilpay_public_key_live'], ['value' => $path]);
        }

        // Image Processing Settings
        Setting::updateOrCreate(['key' => 'image_max_size'], ['value' => $request->image_max_size ?? 800]);
        Setting::updateOrCreate(['key' => 'image_max_file_size'], ['value' => $request->image_max_file_size ?? 2048]);
        Setting::updateOrCreate(['key' => 'image_quality'], ['value' => $request->image_quality ?? 80]);
        Setting::updateOrCreate(['key' => 'image_format'], ['value' => $request->image_format ?? 'webp']);
        Setting::updateOrCreate(['key' => 'image_background_color'], ['value' => $request->image_background_color ?? '#ffffff']);

        // Invoice Settings
        Setting::updateOrCreate(['key' => 'company_name'], ['value' => $request->company_name]);
        Setting::updateOrCreate(['key' => 'company_reg_number'], ['value' => $request->company_reg_number]);
        Setting::updateOrCreate(['key' => 'company_trade_number'], ['value' => $request->company_trade_number]);
        Setting::updateOrCreate(['key' => 'company_address'], ['value' => $request->company_address]);
        Setting::updateOrCreate(['key' => 'company_iban'], ['value' => $request->company_iban]);
        Setting::updateOrCreate(['key' => 'company_bank'], ['value' => $request->company_bank]);
        Setting::updateOrCreate(['key' => 'company_email'], ['value' => $request->company_email]);
        Setting::updateOrCreate(['key' => 'company_phone'], ['value' => $request->company_phone]);
        Setting::updateOrCreate(['key' => 'invoice_prefix'], ['value' => $request->invoice_prefix ?? 'INV-']);
        Setting::updateOrCreate(['key' => 'invoice_start_number'], ['value' => $request->invoice_start_number ?? 1001]);
        Setting::updateOrCreate(['key' => 'invoice_footer_text'], ['value' => $request->invoice_footer_text]);

        // EFactura Settings
        Setting::updateOrCreate(['key' => 'efactura_api_key'], ['value' => $request->efactura_api_key]);
        Setting::updateOrCreate(['key' => 'efactura_client_id'], ['value' => $request->efactura_client_id]);
        Setting::updateOrCreate(['key' => 'efactura_client_secret'], ['value' => $request->efactura_client_secret]);
        Setting::updateOrCreate(['key' => 'efactura_environment'], ['value' => $request->efactura_environment ?? 'sandbox']);

        if ($request->hasFile('logo')) {
            $old = setting('logo');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $file = $request->file('logo');
            $name = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('settings', $name, 'public');
            Setting::updateOrCreate(['key' => 'logo'], ['value' => $path]);
        }

        if ($request->hasFile('favicon')) {
            $old = setting('favicon');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $file = $request->file('favicon');
            $name = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('settings', $name, 'public');
            Setting::updateOrCreate(['key' => 'favicon'], ['value' => $path]);
        }

        return back()->with('success', 'Setările generale au fost actualizate cu succes!');
    }
}
