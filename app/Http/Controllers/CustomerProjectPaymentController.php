<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerProjectPaymentRequest;
use App\Models\CustomerProject;
use App\Models\CustomerProjectPayment;
use Illuminate\Http\RedirectResponse;

class CustomerProjectPaymentController extends Controller
{
    public function store(StoreCustomerProjectPaymentRequest $request, CustomerProject $project): RedirectResponse
    {
        if ($project->status === 'anulado') {
            return back()->with('error', 'No puedes registrar abonos en un proyecto anulado.');
        }

        CustomerProjectPayment::create([
            ...$request->validated(),
            'customer_project_id' => $project->id,
            'received_by_user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Abono registrado correctamente.');
    }
}