<?php

use App\Http\Controllers\AdminApprovalController;
use App\Http\Controllers\CashSessionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesScannerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('products', ProductController::class)->except(['edit', 'update']);

    Route::get('products/{product}/edit', [ProductController::class, 'edit'])
        ->middleware('admin.general.approval')
        ->name('products.edit');

    Route::put('products/{product}', [ProductController::class, 'update'])
        ->middleware('admin.general.approval')
        ->name('products.update');

    Route::get('admin-approval', [AdminApprovalController::class, 'form'])->name('admin-approval.form');
    Route::post('admin-approval', [AdminApprovalController::class, 'verify'])->name('admin-approval.verify');
    Route::post('admin-approval/clear', [AdminApprovalController::class, 'clear'])->name('admin-approval.clear');

    Route::get('cash-sessions', [CashSessionController::class, 'index'])->name('cash-sessions.index');
    Route::post('cash-sessions/open', [CashSessionController::class, 'open'])->name('cash-sessions.open');
    Route::post('cash-sessions/close', [CashSessionController::class, 'close'])->name('cash-sessions.close');

    Route::get('sales/scanner', [SalesScannerController::class, 'index'])->name('sales.scanner');
    Route::post('sales/scanner/add', [SalesScannerController::class, 'add'])->name('sales.scanner.add');
    Route::post('sales/scanner/clear', [SalesScannerController::class, 'clear'])->name('sales.scanner.clear');
    Route::post('sales/scanner/checkout', [SalesScannerController::class, 'checkout'])->name('sales.scanner.checkout');

    Route::middleware('role:admin|Administrador|vendedor')->group(function () {
        Route::get('/reportes', [ReportController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/ventas', [ReportController::class, 'index'])->name('reportes.ventas');
        Route::get('/reportes/ventas/exportar', [ReportController::class, 'export'])->name('reportes.ventas.exportar');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

    Route::middleware('role:Administrador')->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::get('products-import', [ProductImportController::class, 'create'])
        ->middleware('admin.general.approval')
        ->name('products.import.create');

    Route::post('products-import', [ProductImportController::class, 'store'])
        ->middleware('admin.general.approval')
        ->name('products.import.store');
});

require __DIR__ . '/auth.php';