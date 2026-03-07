<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = trim((string) request('search', ''));

        $products = Product::with(['category', 'supplier'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('products', 'public');
        }

        unset($data['photo']);

        $product = Product::create($data);

        $this->auditLogService->log(
            action: 'PRODUCT_CREATED',
            auditableType: Product::class,
            auditableId: $product->id,
            oldValues: null,
            newValues: $product->toArray(),
            userId: Auth::id(),
            request: $request
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $oldValues = $product->getOriginal();
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if (!empty($product->photo_path) && Storage::disk('public')->exists($product->photo_path)) {
                Storage::disk('public')->delete($product->photo_path);
            }

            $data['photo_path'] = $request->file('photo')->store('products', 'public');
        }

        unset($data['photo']);

        $product->update($data);

        $this->auditLogService->log(
            action: 'PRODUCT_UPDATED',
            auditableType: Product::class,
            auditableId: $product->id,
            oldValues: $oldValues,
            newValues: $product->fresh()->toArray(),
            userId: Auth::id(),
            request: $request
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $oldValues = $product->toArray();
        $productId = $product->id;

        if (!empty($product->photo_path) && Storage::disk('public')->exists($product->photo_path)) {
            Storage::disk('public')->delete($product->photo_path);
        }

        $product->delete();

        $this->auditLogService->log(
            action: 'PRODUCT_DELETED',
            auditableType: Product::class,
            auditableId: $productId,
            oldValues: $oldValues,
            newValues: null,
            userId: Auth::id(),
            request: request()
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}