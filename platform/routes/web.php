<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\ProductStatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\SalesIntelligenceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/manifest.webmanifest', function () {
    return response()->json([
        'name' => 'SHOPLINK Intelligence',
        'short_name' => 'SHOPLINK',
        'id' => config('app.url').'/',
        'start_url' => '/dashboard',
        'scope' => '/',
        'display' => 'standalone',
        'background_color' => '#f8fafc',
        'theme_color' => '#e11d48',
        'icons' => [
            [
                'src' => '/icons/shoplink-icon-192.svg',
                'sizes' => '192x192',
                'type' => 'image/svg+xml',
                'purpose' => 'any',
            ],
            [
                'src' => '/icons/shoplink-icon-512.svg',
                'sizes' => '512x512',
                'type' => 'image/svg+xml',
                'purpose' => 'any',
            ],
        ],
    ], 200, ['Content-Type' => 'application/manifest+json']);
})->name('pwa.manifest');

Route::get('/sw.js', function () {
    return response()->view('pwa.sw')
        ->header('Content-Type', 'application/javascript');
})->name('pwa.sw');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/snapshot', [TaskController::class, 'snapshot'])->name('tasks.snapshot');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/preferences', [TaskController::class, 'updatePreferences'])->name('tasks.preferences');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'storeComment'])->name('tasks.comments.store');
    Route::patch('/tasks/{task}/comments/{comment}', [TaskController::class, 'updateComment'])->name('tasks.comments.update');
    Route::delete('/tasks/{task}/comments/{comment}', [TaskController::class, 'destroyComment'])->name('tasks.comments.destroy');
    Route::post('/task-categories', [TaskCategoryController::class, 'store'])->name('task-categories.store');
    Route::patch('/task-categories/reorder', [TaskCategoryController::class, 'reorder'])->name('task-categories.reorder');
    Route::patch('/task-categories/{taskCategory}', [TaskCategoryController::class, 'update'])->name('task-categories.update');
    Route::delete('/task-categories/{taskCategory}', [TaskCategoryController::class, 'destroy'])->name('task-categories.destroy');

    Route::get('/sales', [SalesIntelligenceController::class, 'index'])->name('sales.index');
    Route::get('/sales/top30', [SalesIntelligenceController::class, 'top30'])->name('sales.top30');
    Route::get('/sales/detail', [SalesIntelligenceController::class, 'detail'])->name('sales.detail');
    Route::get('/sales/category-report', [SalesIntelligenceController::class, 'categoryReport'])->name('sales.category-report');
    Route::post('/sales/import', [SalesIntelligenceController::class, 'import'])->name('sales.import');
    Route::post('/sales/import-lines', [SalesIntelligenceController::class, 'importLines'])->name('sales.import-lines');
    Route::post('/sales/import-products', [SalesIntelligenceController::class, 'importProducts'])->name('sales.import-products');

    Route::get('/monitors', [MonitorController::class, 'index'])->name('monitors.index');
    Route::get('/monitors/{monitor}/edit', [MonitorController::class, 'edit'])->name('monitors.edit');
    Route::post('/monitors', [MonitorController::class, 'store'])->name('monitors.store');
    Route::patch('/monitors/{monitor}', [MonitorController::class, 'update'])->name('monitors.update');
    Route::patch('/monitors/{monitor}/toggle', [MonitorController::class, 'toggle'])->name('monitors.toggle');
    Route::post('/monitors/{monitor}/test', [MonitorController::class, 'test'])->name('monitors.test');

    Route::get('/product-stats', [ProductStatController::class, 'index'])->name('product-stats.index');
    Route::get('/product-stats/preparation', [ProductStatController::class, 'preparation'])->name('product-stats.preparation');
    Route::post('/product-stats/import', [ProductStatController::class, 'import'])->name('product-stats.import');
    Route::post('/product-stats/import-hurtel-stock', [ProductStatController::class, 'importHurtelStock'])->name('product-stats.import-hurtel-stock');
    Route::post('/product-stats/import-fdd-stock', [ProductStatController::class, 'importFddStock'])->name('product-stats.import-fdd-stock');
    Route::patch('/product-stats/import-supplier-stock-url', [ProductStatController::class, 'importSupplierStockFromUrl'])->name('product-stats.import-supplier-stock-url');
    Route::post('/product-stats/import-fdd-incoming', [ProductStatController::class, 'importFddIncoming'])->name('product-stats.import-fdd-incoming');
    Route::patch('/product-stats/import-fdd-incoming-url', [ProductStatController::class, 'importFddIncomingFromUrl'])->name('product-stats.import-fdd-incoming-url');
    Route::delete('/product-stats/import-fdd-incoming', [ProductStatController::class, 'clearFddIncoming'])->name('product-stats.clear-fdd-incoming');
    Route::delete('/product-stats/supplier-stock/{supplier}', [ProductStatController::class, 'clearSupplierStock'])->name('product-stats.clear-supplier-stock');
    Route::post('/product-stats/export-csv', [ProductStatController::class, 'exportCsv'])->name('product-stats.export-csv');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/rates', [SettingsController::class, 'updateRates'])->name('settings.rates.update');
    Route::patch('/settings/task-center', [SettingsController::class, 'updateTaskCenter'])->name('settings.task-center.update');
    Route::patch('/settings/product-stats-suppliers', [SettingsController::class, 'updateProductStatsSuppliers'])->name('settings.product-stats-suppliers.update');
    Route::post('/push/subscriptions', [PushSubscriptionController::class, 'store'])->name('push-subscriptions.store');
    Route::delete('/push/subscriptions', [PushSubscriptionController::class, 'destroy'])->name('push-subscriptions.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/notifications/test', [UserController::class, 'sendNotificationTest'])->name('users.notifications.test');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
