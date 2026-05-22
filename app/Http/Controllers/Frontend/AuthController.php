<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('frontend.auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('success', 'Bine ai venit în panoul de administrare!');
            }
            
            return redirect()->intended(route('home'))->with('success', 'Te-ai autentificat cu succes!');
        }

        return back()->with('error', 'Email sau parolă incorectă!')->withInput();
    }

    public function showRegisterForm()
    {
        return view('frontend.auth.register');
    }

    public function register(Request $request)
    {
        $isCompany = $request->input('is_company') === '1';

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100'
        ];

        // Add conditional validation for company fields
        if ($isCompany) {
            $rules['company_name'] = 'required|string|max:255';
            $rules['company_cui'] = 'required|string|max:50';
            $rules['company_reg'] = 'required|string|max:50';
            $rules['company_address'] = 'required|string|max:500';
            $rules['company_iban'] = 'nullable|string|max:50';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $userRole = Role::firstWhere('name', 'user');

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'role_id' => $userRole?->id,
            'is_active' => true,
            'is_company' => $isCompany
        ];

        // Add company data if is_company is checked
        if ($isCompany) {
            $userData['company_name'] = $request->company_name;
            $userData['company_cui'] = $request->company_cui;
            $userData['company_reg'] = $request->company_reg;
            $userData['company_address'] = $request->company_address;
            $userData['company_iban'] = $request->company_iban;
        }

        // Debug logging
        \Log::info('Registration attempt', [
            'is_company' => $isCompany,
            'is_company_input' => $request->input('is_company'),
            'company_name' => $request->company_name,
            'company_cui' => $request->company_cui,
            'user_data' => $userData
        ]);

        $user = User::create($userData);

        \Log::info('User created', [
            'user_id' => $user->id,
            'is_company' => $user->is_company,
            'company_name' => $user->company_name
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Contul tău a fost creat cu succes!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Te-ai deconectat cu succes!');
    }
}