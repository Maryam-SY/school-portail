<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BulletinController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/gestion-bulletins/{classe}/{periode}', [BulletinController::class, 'show'])->name('gestion.bulletins');
Route::get('/bulletin-pdf/{eleve}/{periode}', [BulletinController::class, 'telechargerBulletinPDF'])->name('bulletin.pdf');
Route::get('/bulletins', [BulletinController::class, 'indexView'])->name('bulletins.index');
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');
