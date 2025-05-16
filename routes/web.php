<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\ProsesQcController;
use App\Http\Controllers\HasilTestController;

// Route::get('/', function () {
//     return view('auth.register');
// });
Route::get('/', function () {
    if (auth()->check()) {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    return view('auth.register');
});

Route::get('/proses-qc', [ProsesQcController::class, 'index'])->name('proses-qc.index');
Route::get('/form/{id}', [FormController::class, 'show'])->middleware(['auth', 'role:Teknisi'])->name('form.show');
Route::post('/hasil-test', [HasilTestController::class, 'store'])->name('hasil-test.store');
Route::get('/hasil-test', [HasilTestController::class, 'index'])->name('hasil-test.index');
Route::get('/riwayat/{sn}/{tagg}', [RiwayatController::class, 'index'])->name('riwayat.index');
Route::get('/surat/{history_id}', [SuratController::class, 'show'])->name('surat.show');
Route::get('/hasil-test/{history_id}/edit', [FormController::class, 'edit'])->name('hasil-test.edit');
Route::put('/hasil-test/{history_id}', [FormController::class, 'update'])->name('hasil-test.update');
Route::post('/hasil-test/restore/{id}', [HasilTestController::class, 'restore'])->name('hasil-test.restore');
Route::post('/riwayat/{history}/restore', [\App\Http\Controllers\RiwayatController::class, 'restore'])->name('riwayat.restore');
Route::get('/summary', function () {
    return view('summary');
})->name('summary');
Route::get('/summary/export', [SummaryController::class, 'exportExcel'])->name('summary.export');





Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';