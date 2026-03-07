<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminGeneralApproval
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session('admin_general_approved') === true) {
            return $next($request);
        }

        session(['admin_general_intended_url' => $request->fullUrl()]);

        return redirect()->route('admin-approval.form')
            ->with('warning', 'Esta acción requiere autorización del Administrador General.');
    }
}