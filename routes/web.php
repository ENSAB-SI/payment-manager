<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BankTransactionController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('students', StudentController::class);
    Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');
    
    Route::resource('payments', PaymentController::class);
    
    Route::get('/bank', [BankTransactionController::class, 'index'])->name('bank.index');
    Route::post('/bank/import', [BankTransactionController::class, 'import'])->name('bank.import');
    Route::post('/bank/run-matching', [BankTransactionController::class, 'runMatching'])->name('bank.run-matching');
    Route::post('/bank/manual-match/{transaction}', [BankTransactionController::class, 'manualMatch'])->name('bank.manual-match');
    
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::get('/search/api', [SearchController::class, 'search'])->name('search.api');

Route::get('/report/revenue', [App\Http\Controllers\ReportController::class, 'revenueReport'])->name('report.revenue');
Route::get('/report/student/{student}', [App\Http\Controllers\ReportController::class, 'studentReport'])->name('report.student');
Route::get('/report/payments', [App\Http\Controllers\ReportController::class, 'paymentsReport'])->name('report.payments');
});