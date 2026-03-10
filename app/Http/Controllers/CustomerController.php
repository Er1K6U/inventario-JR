<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::query()
            ->withCount('projects')
            ->orderBy('name')
            ->paginate(20);

        return view('customers.index', compact('customers'));
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        Customer::create($request->validated());

        return back()->with('success', 'Cliente creado correctamente.');
    }
}