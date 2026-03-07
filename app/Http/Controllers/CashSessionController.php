<?php

namespace App\Http\Controllers;

use App\Models\CashSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashSessionController extends Controller
{
    public function index()
    {
        $activeSession = CashSession::where('status', 'OPEN')->latest('opened_at')->first();
        $lastSessions = CashSession::with(['opener', 'closer'])->latest('opened_at')->take(10)->get();

        return view('cash-sessions.index', compact('activeSession', 'lastSessions'));
    }

    public function open(Request $request): RedirectResponse
    {
        $request->validate([
            'opening_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $alreadyOpen = CashSession::where('status', 'OPEN')->exists();
        if ($alreadyOpen) {
            return back()->with('error', 'Ya existe un día de ventas abierto.');
        }

        CashSession::create([
            'opened_by' => Auth::id(),
            'opened_at' => now(),
            'opening_note' => $request->input('opening_note'),
            'status' => 'OPEN',
        ]);

        return back()->with('success', 'Día de ventas abierto correctamente.');
    }

    public function close(Request $request): RedirectResponse
    {
        $request->validate([
            'closing_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $session = CashSession::where('status', 'OPEN')->latest('opened_at')->first();

        if (!$session) {
            return back()->with('error', 'No hay un día de ventas abierto para cerrar.');
        }

        $session->update([
            'closed_by' => Auth::id(),
            'closed_at' => now(),
            'closing_note' => $request->input('closing_note'),
            'status' => 'CLOSED',
        ]);

        return back()->with('success', 'Día de ventas cerrado correctamente.');
    }
}