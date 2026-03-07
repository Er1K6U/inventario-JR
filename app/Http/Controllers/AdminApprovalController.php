<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminApprovalController extends Controller
{
    public function form()
    {
        return view('auth.admin-approval');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = User::where('email', $request->input('email'))->first();

        if (!$admin || !Hash::check($request->input('password'), $admin->password) || !$admin->hasRole('Administrador')) {
            return back()->withErrors(['email' => 'Credenciales inválidas o sin rol Administrador.'])->withInput();
        }

        session(['admin_general_approved' => true]);

        $intended = session('admin_general_intended_url', route('products.index'));
        session()->forget('admin_general_intended_url');

        return redirect()->to($intended)->with('success', 'Autorización de Administrador confirmada.');
    }

    public function clear(): RedirectResponse
    {
        session()->forget(['admin_general_approved', 'admin_general_intended_url']);

        return back()->with('success', 'Autorización de Administrador limpiada.');
    }
}