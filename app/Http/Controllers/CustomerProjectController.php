<?php

namespace App\Http\Controllers;

use App\Http\Requests\CloseCustomerProjectRequest;
use App\Http\Requests\StoreCustomerProjectRequest;
use App\Models\Customer;
use App\Models\CustomerProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerProjectController extends Controller
{
    public function index(Customer $customer): View
    {
        $projects = $customer->projects()
            ->with(['items.product', 'payments'])
            ->latest('id')
            ->paginate(20);

        return view('customer-projects.index', compact('customer', 'projects'));
    }

    public function store(StoreCustomerProjectRequest $request): RedirectResponse
    {
        CustomerProject::create([
            ...$request->validated(),
            'opened_by_user_id' => auth()->id(),
            'status' => 'abierto',
        ]);

        return back()->with('success', 'Proyecto creado correctamente.');
    }

    public function show(CustomerProject $project): View
    {
        $project->load(['customer', 'items.product', 'payments.user']);

        return view('customer-projects.show', compact('project'));
    }

    public function close(CloseCustomerProjectRequest $request, CustomerProject $project): RedirectResponse
    {
        if ($project->status !== 'abierto') {
            return back()->with('error', 'Solo se pueden cerrar proyectos abiertos.');
        }

        DB::transaction(function () use ($project) {
            $project->update([
                'status' => 'cerrado',
                'closed_by_user_id' => auth()->id(),
                'closed_at' => now(),
            ]);
        });

        return back()->with('success', 'Proyecto cerrado correctamente.');
    }
}