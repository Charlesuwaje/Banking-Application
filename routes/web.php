<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\WalletController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('forgot-password', [LoginController::class, 'sendResetLink'])->name('password.email');

    Route::get('password/reset/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');

    Route::post('password/reset', [LoginController::class, 'reset'])->name('password.update');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/wallet', [WalletController::class, 'show'])->name('wallet.show');
    // Route::post('/fetch-user-name', [WalletController::class, 'fetchUserName'])->name('fetch.user.name');
    Route::get('/users/{accountNumber}', [WalletController::class, 'getUserByAccountNumber']);
    Route::get('/wallet/withdraw', [WalletController::class, 'showWithdrawForm'])->name('wallet.withdraw.form');
    Route::get('/wallet/transfer', [WalletController::class, 'showTransferForm'])->name('wallet.transfer.form');
    Route::post('/wallet/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');
    Route::post('/wallet/transfer', [WalletController::class, 'transfer'])->name('wallet.transfer');
    // Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    // Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
Route::get('/chat/messages', [ChatController::class, 'fetchMessages'])->name('chat.fetch');
});
