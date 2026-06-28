<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\NeedController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureManager;
use App\Http\Middleware\EnsureMaster;
use App\Http\Middleware\EnsureWritable;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public reads.
Route::get('/', [BuildingController::class, 'index'])->name('home');
Route::get('/api/edificios-similares', [BuildingController::class, 'similar'])->name('buildings.similar');
Route::get('/edificios/registrar', [BuildingController::class, 'create'])->name('buildings.create');
Route::get('/edificios/{building:slug}', [BuildingController::class, 'show'])->name('buildings.show');

// Public writes — rate limited per IP and gated by the admin emergency brake.
Route::middleware(['throttle:30,1', EnsureWritable::class])->group(function () {
    Route::post('/edificios', [BuildingController::class, 'store'])->name('buildings.store');
    Route::put('/edificios/{building:slug}', [BuildingController::class, 'update'])->name('buildings.update');

    Route::post('/edificios/{building:slug}/necesidades', [NeedController::class, 'store'])->name('needs.store');
    Route::post('/edificios/{building:slug}/necesidades/lote', [NeedController::class, 'storeBatch'])->name('needs.batch');
    Route::post('/necesidades/{need}/comprometerse', [NeedController::class, 'commit'])->name('needs.commit');
    Route::patch('/commitments/{commitment}/estado', [NeedController::class, 'updateCommitmentStatus'])->name('commitments.status');
});

// Pending-account landing for registered-but-unassigned users.
Route::get('/cuenta', fn () => Inertia::render('Account/Pending'))->middleware('auth')->name('account');

// Management panel — master & admin. Read-only audit monitoring + edit lock.
Route::middleware(EnsureManager::class)->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/lock', [AdminController::class, 'toggleLock'])->name('lock');
});

// User management — master only. The master creates and manages all accounts.
Route::middleware(EnsureMaster::class)->prefix('admin/usuarios')->name('admin.users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::patch('/{user}', [UserController::class, 'updateRole'])->name('role');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});
