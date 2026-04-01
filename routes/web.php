<?php

use App\Http\Controllers\AdjustmentRuleController;
use App\Http\Controllers\ApiIntegrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DailyInputController;
use App\Http\Controllers\FormulaSettingController;
use App\Http\Controllers\NotificationSystemController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\PricingEngineController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchasePlanController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// ==========================================
// RUTE PUBLIK (TIDAK PERLU LOGIN)
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// ==========================================
// RUTE PRIVATE (WAJIB LOGIN / AUTH)
// ==========================================

// 🥉 KASTA 1: SEMUA ORANG YANG UDAH LOGIN (Staff, Manager, Administrator)
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.change');
    
    Route::get('/daily-inputs', [DailyInputController::class, 'create'])->name('daily-inputs.create');
    Route::post('/daily-inputs', [DailyInputController::class, 'store'])->name('daily-inputs.store');
    
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    
    Route::get('/input-penawaran', [QuotationController::class, 'create'])->name('quotations.create');
    Route::post('/input-penawaran', [QuotationController::class, 'store'])->name('quotations.store');
});

// 🥈 KASTA 2: MANAGER & ADMINISTRATOR SAJA (Staff ditendang 403)
Route::middleware(['auth', 'role:administrator,manager'])->group(function () {
Route::get('/', function (\Illuminate\Http\Request $request) {
        // 1. Siapkan mesin pencari dasar
        $query = \App\Models\DailyPricing::with('product')
            ->orderBy('date_input', 'desc')
            ->orderBy('created_at', 'desc');

        // 2. Tangkap sinyal klik dari tombol dashboard (contoh: ?status=yellow)
        $statusFilter = $request->query('status');

        // 3. Logika Filter Database (Mata Elang)
        if ($statusFilter === 'red') {
            $query->whereIn('status_margin', ['RED', 'Rugi']); 
        } elseif ($statusFilter === 'yellow') {
            $query->where('status_margin', 'YELLOW');
        } elseif ($statusFilter === 'green') {
            // Hijau adalah yang bukan Merah dan bukan Kuning
            $query->whereNotIn('status_margin', ['RED', 'Rugi', 'YELLOW']);
        }

        // 4. Eksekusi paginasi 10 data
        $dailyPricings = $query->paginate(10);

        // 5. Kirim data dan status filter yang lagi aktif ke Blade
        return view('welcome', compact('dailyPricings', 'statusFilter'));
    })->name('dashboard');
    
    // Modul Purchase Plan (Ruang Kerja Manager)
    Route::get('/purchase-plan', [PurchasePlanController::class, 'index'])->name('process_plan.index');
    Route::post('/purchase-plan/approve/{id}', [PurchasePlanController::class, 'approve'])->name('process_plan.approve');
    Route::post('/purchase-plan/cancel/{id}', [PurchasePlanController::class, 'cancel'])->name('process_plan.cancel');
    Route::post('/purchase-plan/hold/{id}', [PurchasePlanController::class, 'hold'])->name('process_plan.hold');
    Route::post('/purchase-plan/reject/{id}', [PurchasePlanController::class, 'reject'])->name('process_plan.reject');
    Route::post('/purchase-plan/store-manual-bulk', [PurchasePlanController::class, 'storeManualBulk'])->name('process_plan.store_manual_bulk');
    Route::get('/purchase-plan/print', [PurchasePlanController::class, 'printPDF'])->name('purchase-plan.print');
    Route::post('/process-plan/update-offer/{id}', [PurchasePlanController::class, 'updateOffer'])->name('process_plan.update_offer');
    Route::post('/purchase-plan/auto-termurah', [PurchasePlanController::class, 'autoSelectCheapest'])->name('process_plan.auto_termurah');
    Route::post('/purchase-plan/hold/{id}/{duration}', [PurchasePlanController::class, 'holdForDuration'])->name('process_plan.hold');
// Rute Arsip Purchase Plan
    Route::post('/purchase-plan/archive/{id}', [PurchasePlanController::class, 'archive'])->name('process_plan.archive');
    Route::post('/purchase-plan/archive-all', [PurchasePlanController::class, 'archiveAll'])->name('process_plan.archive_all');

    // Modul Restock Gudang (Kasta 2 Manager) - SUDAH DIRAPIKAN!
    Route::get('/restock-gudang', [\App\Http\Controllers\RestockController::class, 'index'])->name('restock.index');
    Route::post('/restock-gudang/process', [\App\Http\Controllers\RestockController::class, 'processReorder'])->name('restock.process');
    Route::delete('/restock-gudang/delete/{id}', [\App\Http\Controllers\RestockController::class, 'destroyPlan'])->name('restock.destroyPlan');
    Route::get('/restock-gudang/print', [\App\Http\Controllers\RestockController::class, 'printPDF'])->name('restock.print');
    Route::put('/restock-gudang/update/{id}', [\App\Http\Controllers\RestockController::class, 'updatePlan'])->name('restock.updatePlan');
    Route::post('/restock-gudang/auto', [\App\Http\Controllers\RestockController::class, 'autoRestock'])->name('restock.auto');

    // Modul History & Analytics
    Route::get('/history', [PricingEngineController::class, 'history'])->name('pricing.history');
    Route::get('/pricing-history/{id}/edit', [PricingEngineController::class, 'editHistory'])->name('pricing.edit');
    Route::put('/pricing-history/{id}', [PricingEngineController::class, 'updateHistory'])->name('pricing.update');
    Route::delete('/pricing-history/{id}', [PricingEngineController::class, 'destroyHistory'])->name('pricing.destroy');
    Route::get('/analytics', [PricingEngineController::class, 'analytics'])->name('analytics.index');
    Route::get('/supplier-products', [SupplierController::class, 'productsPerSupplier'])->name('suppliers.products');

    // Document Center & Kontrak
    Route::get('/upload-contract', [SupplierController::class, 'uploadContractForm'])->name('contracts.upload');
    Route::post('/upload-contract', [SupplierController::class, 'storeContract'])->name('contracts.store');
    Route::get('/contract-history', [SupplierController::class, 'contractHistory'])->name('contracts.history');
    Route::get('/document-center/{type}', [\App\Http\Controllers\DocumentCenterController::class, 'index'])->name('document.center');

    // Export/Import & API Chart
    Route::get('/export-excel', [PricingController::class, 'exportExcel'])->name('export.excel');
    Route::post('/import-excel', [PricingController::class, 'importExcel'])->name('import.excel');
    Route::get('/api/chart/product/{id}', [PricingController::class, 'getProductChart'])->name('api.chart.product');
});

// 🥇 KASTA 3: SANGAT RAHASIA - HANYA ADMINISTRATOR DEWA
Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/pricing-engine', [PricingEngineController::class, 'index'])->name('pricing.index');
    Route::post('/pricing-engine/calculate', [PricingEngineController::class, 'calculate'])->name('pricing.calculate');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/import', [ProductController::class, 'importExcel'])->name('products.import');

    Route::get('/pricing-type/{type}', [\App\Http\Controllers\PricingTypeController::class, 'index'])->name('pricing.type');
    Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']);
    Route::get('/audit-trail', [\App\Http\Controllers\AuditTrailController::class, 'index'])->name('audit_trail.index');

    Route::get('/notifications', [NotificationSystemController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationSystemController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    Route::get('/backup-restore', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup-restore/download', [BackupController::class, 'download'])->name('backup.download');

    Route::get('/pricing/formula', [FormulaSettingController::class, 'index'])->name('formula.index');
    Route::post('/pricing/formula/update', [FormulaSettingController::class, 'update'])->name('formula.update');

    Route::get('/pricing/rules', [AdjustmentRuleController::class, 'index'])->name('rules.index');
    Route::post('/pricing/rules/update', [AdjustmentRuleController::class, 'update'])->name('rules.update');

    Route::get('/settings/api', [ApiIntegrationController::class, 'index'])->name('api.index');
    Route::post('/settings/api/generate', [ApiIntegrationController::class, 'generate'])->name('api.generate');
    Route::post('/settings/api/revoke/{id}', [ApiIntegrationController::class, 'revoke'])->name('api.revoke');
    
    Route::get('/laporan-pricing', [DailyInputController::class, 'reportPricing'])->name('laporan.pricing');

// 🔥 TOMBOL NUKLIR SINKRONISASI SEMUA PRODUK 🔥
Route::get('/sync-semua-stok', function() {
    // 1. Bersihin dulu meja manager dari "Data Hantu"
    \App\Models\SupplierOffer::truncate(); 

    // 2. Tarik semua produk berserta laporan hariannya
    $semuaProduk = \App\Models\Product::with(['dailyPricings' => function($q) {
        $q->orderBy('date_input', 'desc');
    }])->get();

    $jumlahDiupdate = 0;

    foreach($semuaProduk as $prod) {
        $dataHarian = $prod->dailyPricings->first(); // Ambil inputan paling baru
        
        if($dataHarian && isset($dataHarian->stock)) {
            // TEMBAK LANGSUNG KE DATABASE CORE (Bypass semua gembok Laravel!)
            \Illuminate\Support\Facades\DB::table('products')
                ->where('id', $prod->id)
                ->update(['stock' => $dataHarian->stock]);
            
            $jumlahDiupdate++;
        }
    }

    return "<h1>💥 BOOOMM!!! SINKRONISASI MASSAL BERHASIL! 💥</h1>
            <p>Total <b>$jumlahDiupdate Produk</b> (termasuk Bolu dkk) berhasil di-update paksa stoknya sesuai laporan harian terbaru!</p>
            <p>Antrean Manager juga udah suci bersih dari hantu!</p>
            <p>Silakan buka menu <b>Restock Gudang</b> lu sekarang Breyy!</p>";
});
});