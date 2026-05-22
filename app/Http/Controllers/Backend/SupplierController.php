<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\ANAFService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Afișează lista de furnizori
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('cui', 'like', "%{$request->search}%");
        }

        $suppliers = $query->latest()->paginate(20)->appends($request->query());
        
        return view('backend.suppliers.index', compact('suppliers'));
    }

    /**
     * Afișează formularul pentru creare furnizor
     */
    public function create()
    {
        return view('backend.suppliers.create');
    }

    /**
     * Salvează un nou furnizor
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cui' => 'nullable|string|max:50',
            'reg_com' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'contact_person' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'tva_status' => 'nullable|string|max:50',
            'tva_valid_from' => 'nullable|date',
            'tva_valid_to' => 'nullable|date',
        ]);

        Supplier::create($request->all());
        
        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Furnizorul a fost creat cu succes!');
    }

    /**
     * Afișează detaliile unui furnizor
     */
    public function show(Supplier $supplier)
    {
        $supplier->load('receipts');
        return view('backend.suppliers.show', compact('supplier'));
    }

    /**
     * Afișează formularul pentru editare furnizor
     */
    public function edit(Supplier $supplier)
    {
        return view('backend.suppliers.edit', compact('supplier'));
    }

    /**
     * Actualizează un furnizor
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cui' => 'nullable|string|max:50',
            'reg_com' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'contact_person' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'tva_status' => 'nullable|string|max:50',
            'tva_valid_from' => 'nullable|date',
            'tva_valid_to' => 'nullable|date',
        ]);

        $supplier->update($request->all());
        
        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Furnizorul a fost actualizat cu succes!');
    }

    /**
     * Șterge un furnizor
     */
    public function destroy(Supplier $supplier)
    {
        if ($supplier->receipts()->count() > 0) {
            return back()->with('error', 'Nu puteți șterge acest furnizor deoarece are recepții asociate.');
        }

        $supplier->delete();
        
        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Furnizorul a fost șters cu succes!');
    }

    /**
     * Caută furnizor în baza de date ANAF
     */
    public function searchByCui(Request $request, ANAFService $anafService)
    {
        $request->validate([
            'cui' => 'required|string|max:50',
        ]);

        $cui = $request->input('cui');
        
        // Validare CUI numeric
        if (!preg_match('/^[0-9]+$/', $cui)) {
            return response()->json([
                'success' => false,
                'message' => 'CUI-ul trebuie să conțină doar cifre.'
            ], 400);
        }

        $companyData = $anafService->searchByCui($cui);

        if (!$companyData) {
            return response()->json([
                'success' => false,
                'message' => 'Nu s-a găsit nicio firmă cu acest CUI în baza de date ANAF.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $companyData
        ]);
    }
}
