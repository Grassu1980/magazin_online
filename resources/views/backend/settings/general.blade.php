@extends('layouts.admin')

@section('title', 'Setări generale - Admin Panel')
@section('page_title', 'Setări generale')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
    <span class="mx-1">/</span>
    <span>Setări generale</span>
@endsection

@section('content')
<div class="max-w-4xl">
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.general.update') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nume site</label>
            <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Meta description</label>
            <textarea name="meta_description" rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-lg">{{ old('meta_description', $settings['meta_description']) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Titlu bun venit (homepage)</label>
            <input type="text" name="welcome_title" value="{{ old('welcome_title', $settings['welcome_title']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="Bine ai venit la MagazinOnline">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Descriere bun venit (homepage)</label>
            <textarea name="welcome_description" rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="Descoperă cele mai bune produse la prețuri incredibile.">{{ old('welcome_description', $settings['welcome_description']) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Culoare primară</label>
                <input type="color" name="primary_color" value="{{ old('primary_color', $settings['primary_color'] ?? '#2563eb') }}" class="w-full h-10 px-4 py-2 border border-slate-200 rounded-lg">
                <p class="text-xs text-slate-500 mt-1">Culoare pentru butoane și elemente principale</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Culoare secundară</label>
                <input type="color" name="secondary_color" value="{{ old('secondary_color', $settings['secondary_color'] ?? '#1e40af') }}" class="w-full h-10 px-4 py-2 border border-slate-200 rounded-lg">
                <p class="text-xs text-slate-500 mt-1">Culoare pentru hover și accent</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Logo</label>
                <input type="file" name="logo" accept="image/*" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                @if($settings['logo'])
                    <div class="mt-3">
                        <img src="{{ Storage::url($settings['logo']) }}" alt="Logo" class="h-12">
                    </div>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Dimensiune logo</label>
                <select name="logo_size" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                    <option value="h-8" {{ old('logo_size', $settings['logo_size']) === 'h-8' ? 'selected' : '' }}>Mic (32px)</option>
                    <option value="h-12" {{ old('logo_size', $settings['logo_size']) === 'h-12' ? 'selected' : '' }}>Mediu (48px)</option>
                    <option value="h-16" {{ old('logo_size', $settings['logo_size']) === 'h-16' ? 'selected' : '' }}>Mare (64px)</option>
                    <option value="h-24" {{ old('logo_size', $settings['logo_size']) === 'h-24' ? 'selected' : '' }}>Foarte mare (96px)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Favicon</label>
                <input type="file" name="favicon" accept="image/*" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                @if($settings['favicon'])
                    <div class="mt-3">
                        <img src="{{ Storage::url($settings['favicon']) }}" alt="Favicon" class="h-10 w-10">
                    </div>
                @endif
            </div>
        </div>

        <!-- SMTP Configuration -->
        <div class="border-t pt-6 mt-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Configurare SMTP Email</h3>
            <p class="text-sm text-slate-500 mb-4">Configurează setările pentru trimiterea email-urilor de comenzi</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email expeditor comenzi</label>
                    <input type="email" name="smtp_from_email" value="{{ old('smtp_from_email', $settings['smtp_from_email']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="noreply@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">SMTP Host</label>
                    <input type="text" name="smtp_host" value="{{ old('smtp_host', $settings['smtp_host']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="smtp.gmail.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">SMTP Port</label>
                    <input type="number" name="smtp_port" value="{{ old('smtp_port', $settings['smtp_port']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="587">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">SMTP Username</label>
                    <input type="text" name="smtp_username" value="{{ old('smtp_username', $settings['smtp_username']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="email@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">SMTP Password</label>
                    <input type="password" name="smtp_password" value="{{ old('smtp_password') }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="••••••••">
                    @if($settings['smtp_password'])
                    <p class="text-xs text-slate-500 mt-1">Parola este salvată criptat. Lăsați gol pentru a păstra parola actuală.</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tip conexiune</label>
                    <select name="smtp_encryption" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                        <option value="">Niciuna</option>
                        <option value="tls" {{ old('smtp_encryption', $settings['smtp_encryption']) === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ old('smtp_encryption', $settings['smtp_encryption']) === 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- MobilPay Configuration -->
        <div class="border-t pt-6 mt-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Setări MobilPay</h3>
            <p class="text-sm text-slate-500 mb-4">Configurează setările pentru plățile cu cardul prin MobilPay</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mod MobilPay</label>
                    <select name="mobilpay_mode" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                        <option value="sandbox" {{ old('mobilpay_mode', $settings['mobilpay_mode']) === 'sandbox' ? 'selected' : '' }}>Sandbox (Test)</option>
                        <option value="live" {{ old('mobilpay_mode', $settings['mobilpay_mode']) === 'live' ? 'selected' : '' }}>Live (Producție)</option>
                    </select>
                </div>
            </div>

            <!-- Sandbox Settings -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-yellow-800 mb-3">Setări Sandbox (Test)</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Signature Sandbox</label>
                        <input type="text" name="mobilpay_signature_sandbox" value="{{ old('mobilpay_signature_sandbox', $settings['mobilpay_signature_sandbox']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="SIGNATURE_SANDBOX">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Private Key Sandbox (.key)</label>
                        <input type="file" name="mobilpay_private_key_sandbox" accept=".key" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                        @if($settings['mobilpay_private_key_sandbox'])
                        <p class="text-xs text-slate-500 mt-1">Fișier încărcat. Lăsați gol pentru a păstra fișierul actual.</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Public Key Sandbox (.cer)</label>
                        <input type="file" name="mobilpay_public_key_sandbox" accept=".cer" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                        @if($settings['mobilpay_public_key_sandbox'])
                        <p class="text-xs text-slate-500 mt-1">Fișier încărcat. Lăsați gol pentru a păstra fișierul actual.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Live Settings -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-green-800 mb-3">Setări Live (Producție)</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Signature Live</label>
                        <input type="text" name="mobilpay_signature_live" value="{{ old('mobilpay_signature_live', $settings['mobilpay_signature_live']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="SIGNATURE_LIVE">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Private Key Live (.key)</label>
                        <input type="file" name="mobilpay_private_key_live" accept=".key" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                        @if($settings['mobilpay_private_key_live'])
                        <p class="text-xs text-slate-500 mt-1">Fișier încărcat. Lăsați gol pentru a păstra fișierul actual.</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Public Key Live (.cer)</label>
                        <input type="file" name="mobilpay_public_key_live" accept=".cer" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                        @if($settings['mobilpay_public_key_live'])
                        <p class="text-xs text-slate-500 mt-1">Fișier încărcat. Lăsați gol pentru a păstra fișierul actual.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Common Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Confirm URL</label>
                    <input type="text" name="mobilpay_confirm_url" value="{{ old('mobilpay_confirm_url', $settings['mobilpay_confirm_url']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="{{ route('payment.mobilpay.confirm') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Return URL</label>
                    <input type="text" name="mobilpay_return_url" value="{{ old('mobilpay_return_url', $settings['mobilpay_return_url']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="{{ route('payment.mobilpay.return') }}">
                </div>
            </div>
        </div>

        <!-- Image Processing Settings -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Setări Imagini</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dimensiune maximă (px)</label>
                    <input type="number" name="image_max_size" value="{{ $settings['image_max_size'] ?? 800 }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           min="100" max="2000">
                    <p class="text-xs text-gray-500 mt-1">Dimensiunea maximă a imaginii (100-2000px)</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dimensiune maximă fișier (KB)</label>
                    <input type="number" name="image_max_file_size" value="{{ $settings['image_max_file_size'] ?? 2048 }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           min="500" max="10240">
                    <p class="text-xs text-gray-500 mt-1">Dimensiunea maximă a fișierului (500-10240 KB)</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Calitate (%)</label>
                    <input type="number" name="image_quality" value="{{ $settings['image_quality'] ?? 80 }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           min="50" max="100">
                    <p class="text-xs text-gray-500 mt-1">Calitatea compresiei (50-100%)</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                    <select name="image_format" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="webp" {{ ($settings['image_format'] ?? 'webp') === 'webp' ? 'selected' : '' }}>WebP</option>
                        <option value="jpg" {{ ($settings['image_format'] ?? 'webp') === 'jpg' ? 'selected' : '' }}>JPG</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Formatul de salvare a imaginii</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Culoare fundal</label>
                    <input type="color" name="image_background_color" value="{{ $settings['image_background_color'] ?? '#ffffff' }}" 
                           class="w-full h-10 px-1 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Culoarea de fundal pentru padding</p>
                </div>
            </div>
        </div>

        <!-- Invoice Settings -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Setări Facturare</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nume Firmă</label>
                    <input type="text" name="company_name" value="{{ $settings['company_name'] ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ex: SC Exemplu SRL">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CUI</label>
                    <input type="text" name="company_reg_number" value="{{ $settings['company_reg_number'] ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ex: RO12345678">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nr. Reg. Comerț</label>
                    <input type="text" name="company_trade_number" value="{{ $settings['company_trade_number'] ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ex: J40/1234/2024">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresă Firmă</label>
                    <input type="text" name="company_address" value="{{ $settings['company_address'] ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ex: Str. Exemplu nr. 1, București">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">IBAN</label>
                    <input type="text" name="company_iban" value="{{ $settings['company_iban'] ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ex: RO00BANK00001234567890">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bancă</label>
                    <input type="text" name="company_bank" value="{{ $settings['company_bank'] ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ex: Banca Transilvania">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Firmă</label>
                    <input type="email" name="company_email" value="{{ $settings['company_email'] ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="exemplu@firma.ro">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefon Firmă</label>
                    <input type="text" name="company_phone" value="{{ $settings['company_phone'] ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ex: +40 700 123 456">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prefix Factură</label>
                    <input type="text" name="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'INV-' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ex: INV-">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Număr Start Factură</label>
                    <input type="number" name="invoice_start_number" value="{{ $settings['invoice_start_number'] ?? 1001 }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           min="1" placeholder="Ex: 1001">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Text Footer Factură</label>
                    <textarea name="invoice_footer_text" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Text care apare la finalul facturii">{{ $settings['invoice_footer_text'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <!-- EFactura Settings -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Setări eFactura ANAF</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                    <input type="text" name="efactura_api_key" value="{{ $settings['efactura_api_key'] ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Cheie API eFactura">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
                    <input type="text" name="efactura_client_id" value="{{ $settings['efactura_client_id'] ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="ID Client eFactura">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Secret</label>
                    <input type="password" name="efactura_client_secret" value="{{ $settings['efactura_client_secret'] ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Secret Client eFactura">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mediu</label>
                    <select name="efactura_environment" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="sandbox" {{ ($settings['efactura_environment'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox (Test)</option>
                        <option value="live" {{ ($settings['efactura_environment'] ?? 'sandbox') === 'live' ? 'selected' : '' }}>Live (Producție)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Salvează</button>
        </div>
    </form>
</div>
@endsection

