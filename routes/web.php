<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\StadiumController;
use App\Http\Controllers\User\BookingController as UserBookingController;
use App\Http\Controllers\User\ProfileController;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StadiumsController;
use App\Http\Controllers\Admin\FieldTypeController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\BookingServiceController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\BookingDetailController;


Route::middleware(['web'])->group(function () {

    Route::get('/', [StadiumController::class, 'index'])
        ->name('home');

    Route::get('/stadiums/{id}', [StadiumController::class, 'show'])
        ->name('stadiums.show');

    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->name('register.store');

    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->name('login.store');
    
    Route::post('/stadiums/{stadium}', [UserBookingController::class, 'storeFromStadium'])
        ->name('user.bookings.store.from-stadium');

    Route::middleware(['auth'])->group(function () {

        Route::get('/dat-san/{stadium}', [UserBookingController::class, 'create'])
            ->name('user.bookings.create');

        Route::post('/dat-san', [UserBookingController::class, 'store'])
            ->name('user.bookings.store');

        Route::post('/stadiums/{stadium}', [UserBookingController::class, 'storeFromStadium'])
            ->name('user.bookings.store.from-stadium');

        Route::get('/don-dat-san-cua-toi', [UserBookingController::class, 'index'])
            ->name('user.bookings.index');

        Route::get('/don-dat-san-cua-toi/{booking}', [UserBookingController::class, 'show'])
            ->name('user.bookings.show');
            
        Route::delete('/don-dat-san-cua-toi/{booking}', [UserBookingController::class, 'destroy'])
            ->name('user.bookings.destroy');

        Route::get('/ho-so-ca-nhan', [ProfileController::class, 'index'])
            ->name('user.profile.index');

        Route::put('/ho-so-ca-nhan', [ProfileController::class, 'update'])
            ->name('user.profile.update');

        Route::put('/ho-so-ca-nhan/mat-khau', [ProfileController::class, 'updatePassword'])
            ->name('user.profile.password');


        Route::post('/logout', function (Request $request) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        })->name('logout');
    });

    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

        Route::get('/', function () {
            if (Auth::user()->role !== 'admin') {
                return redirect('/');
            }

            return redirect()->route('admin.dashboard');
        });

        Route::get('/dashboard', function () {
            if (Auth::user()->role !== 'admin') {
                return redirect('/');
            }

            return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('roles', RoleController::class);

        Route::resource('users', UserController::class)
            ->except(['show']);

        Route::match(['get', 'post', 'patch'], 'users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');

        Route::resource('stadiums', StadiumsController::class);

        Route::resource('field-types', FieldTypeController::class);

        Route::resource('services', ServiceController::class);

        Route::resource('booking-services', BookingServiceController::class);

        Route::resource('bookings', BookingController::class)
            ->only(['index', 'show', 'update', 'destroy']);

        Route::get('booking-details', [BookingDetailController::class, 'index'])
            ->name('booking-details.index');

        Route::get('booking-details/{bookingDetail}', [BookingDetailController::class, 'show'])
            ->name('booking-details.show');
    });
});