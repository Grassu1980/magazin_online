@extends('layouts.admin')

@section('title', 'Nou Furnizor - Admin Panel')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.suppliers.index') }}" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-1"></i>Înapoi la furnizori
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Nou Furnizor</h1>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <p class="font-bold">Erori:</p>
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.suppliers.store') }}" method="POST" id="supplierForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">CUI *</label>
                    <div class="flex gap-2">
                        <input type="text" name="cui" id="cui" required
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Codul de identificare fiscală">
                        <button type="button" onclick="searchAnaf()" id="searchAnafBtn"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-search mr-2"></i>Caută în ANAF
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Introdu CUI-ul și apasă "Caută în ANAF" pentru a completa automat datele firmei</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nume Furnizor *</label>
                    <input type="text" name="name" id="name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Numele furnizorului">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reg. Com.</label>
                    <input type="text" name="reg_com" id="reg_com"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Numărul de înregistrare comerț">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status TVA</label>
                    <input type="text" name="tva_status" id="tva_status" readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none"
                           placeholder="Se completează automat">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adresă</label>
                    <input type="text" name="address" id="address"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Adresa completă">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Oraș</label>
                    <input type="text" name="city"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Orașul">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                    <input type="text" name="phone" id="phone"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Numărul de telefon">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Adresa de email">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Persoană de Contact</label>
                    <input type="text" name="contact_person"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Numele persoanei de contact">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1" selected>Activ</option>
                        <option value="0">Inactiv</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valid TVA de la</label>
                    <input type="date" name="tva_valid_from" id="tva_valid_from" readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valid TVA până la</label>
                    <input type="date" name="tva_valid_to" id="tva_valid_to" readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.suppliers.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Anulează
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Salvează Furnizorul
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function searchAnaf() {
    const cui = document.getElementById('cui').value.trim();
    const searchBtn = document.getElementById('searchAnafBtn');
    
    if (!cui) {
        alert('Te rugăm să introduci CUI-ul');
        return;
    }

    // Validare CUI numeric
    if (!/^[0-9]+$/.test(cui)) {
        alert('CUI-ul trebuie să conțină doar cifre');
        return;
    }

    searchBtn.disabled = true;
    searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Se caută...';

    fetch('{{ route('admin.anaf.cui') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ cui: cui })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Completează câmpurile cu datele de la ANAF
            if (data.data.name) document.getElementById('name').value = data.data.name;
            if (data.data.address) document.getElementById('address').value = data.data.address;
            if (data.data.reg_com) document.getElementById('reg_com').value = data.data.reg_com;
            if (data.data.phone) document.getElementById('phone').value = data.data.phone;
            if (data.data.email) document.getElementById('email').value = data.data.email;
            if (data.data.tva_status) document.getElementById('tva_status').value = data.data.tva_status;
            if (data.data.tva_valid_from) document.getElementById('tva_valid_from').value = data.data.tva_valid_from;
            if (data.data.tva_valid_to) document.getElementById('tva_valid_to').value = data.data.tva_valid_to;
            
            // Verifică dacă numele a fost completat
            if (data.data.name) {
                alert('Datele firmei au fost completate cu succes! Furnizorul a fost salvat automat.');
                // Trimite formularul automat
                document.getElementById('supplierForm').submit();
            } else {
                alert('Datele au fost completate dar numele firmei nu a fost găsit. Te rugăm să completezi manual numele și să salvezi.');
            }
        } else {
            alert('Eroare: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('A apărut o eroare la căutarea în ANAF. Te rugăm să încerci din nou.');
    })
    .finally(() => {
        searchBtn.disabled = false;
        searchBtn.innerHTML = '<i class="fas fa-search mr-2"></i>Caută în ANAF';
    });
}
</script>
@endsection
