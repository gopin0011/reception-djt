<?php

use App\Http\Controllers\GateController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UsersController;
use App\Models\Guest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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

Route::get('/reset-password', [UsersController::class, 'resetPasswordShow']);
Route::post('/reset-password', [UsersController::class, 'resetPassword']);

Route::get('/', [HomeController::class, 'index']);

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    // HOME
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // UBAH PASSWORD
    Route::get('users/ubah-password', [UsersController::class, 'changepassword'])->name('password.change');
    Route::post('users/ubah-password', [UsersController::class, 'storepassword'])->name('password.store');

    // UBAH PASSWORD
    Route::get('users/ubah-data', [UsersController::class, 'changedata'])->name('data.change');
    Route::post('users/ubah-data', [UsersController::class, 'storedata'])->name('data.store');

    //GATE
    Route::get('gates/data', [GateController::class, 'showData'])->name('gates.data');
    Route::resource('gates', GateController::class);

    //ROOM
    Route::get('rooms/data', [RoomController::class, 'showData'])->name('rooms.data');
    Route::resource('rooms', RoomController::class);

    //USER
    Route::get('users/data', [UsersController::class, 'showData'])->name('users.data');
    Route::resource('users', UsersController::class);

    //BOOKING
    Route::get('tamu/booking', [GuestController::class, 'booking'])->name('guest.booking');
    Route::get('tamu/booking/{id}/edit', [GuestController::class, 'editBooking'])->name('edit.booking');
    Route::delete('tamu/booking/{id}', [GuestController::class, 'destroyBooking'])->name('destroy.booking');
    Route::post('tamu/booking', [GuestController::class, 'postBooking'])->name('post.booking');
    Route::get('tamu/booking/data', [GuestController::class, 'dataBooking'])->name('data.booking');

    //REPORT
    Route::get('tamu/report', [GuestController::class, 'reportsearch'])->name('report.search');
    Route::get('tamu/report/cetak', [GuestController::class, 'reportprint'])->name('report.print');

    //GUEST
    Route::get('tamu/hari-ini', [GuestController::class, 'today'])->name('guest.today');
    Route::get('tamu/hari-ini/data', [GuestController::class, 'todayData'])->name('data.today');
    Route::get('tamu/semua', [GuestController::class, 'all'])->name('guest.all');
    Route::get('tamu/semua/data', [GuestController::class, 'allData'])->name('data.all');
    Route::get('tamu/{id}', [GuestController::class, 'approveData'])->name('guests.approve');
    Route::get('tamu/edit/{id}', [GuestController::class, 'editData'])->name('data.edit');
    Route::post('tamu/edit', [GuestController::class, 'selectRoom'])->name('guests.selectRoom');
});
