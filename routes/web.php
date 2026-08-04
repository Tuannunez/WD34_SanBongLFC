<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookingDetailController;
use App\Http\Controllers\Admin\BookingServiceController;
use App\Http\Controllers\Admin\FieldController;
use App\Http\Controllers\Admin\FieldTypeController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\StadiumsController;
use App\Http\Controllers\Admin\TimeSlotsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookingCheckInScannerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\User\BookingController as UserBookingController;
use App\Http\Controllers\User\NewsController as UserNewsController;
use App\Http\Controllers\User\NotificationController as UserNotificationController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ReviewController as UserReviewController;
use App\Http\Controllers\User\ServiceController as UserServiceController;
use App\Http\Controllers\User\StadiumController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', [StadiumController::class, 'index'])
    ->name('home');

Route::view('/gioi-thieu', 'user.about')
    ->name('about');

Route::get('/tin-tuc', [UserNewsController::class, 'index'])
    ->name('news.index');

Route::get('/tin-tuc/{news}', [UserNewsController::class, 'show'])
    ->name('news.show');

Route::get('/stadiums', [StadiumController::class, 'list'])
    ->name('stadiums.index');

Route::get('/stadiums/{id}', [StadiumController::class, 'show'])
    ->whereNumber('id')
    ->name('stadiums.show');

Route::get('/stadiums/{stadium}/availability', [UserBookingController::class, 'availability'])
    ->whereNumber('stadium')
    ->name('user.bookings.availability');

Route::get('/services', [UserServiceController::class, 'index'])
    ->name('services.index');

Route::get('/services/{service}', [UserServiceController::class, 'show'])
    ->whereNumber('service')
    ->name('services.show');

/*
|--------------------------------------------------------------------------
| Guest routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->name('register.store');

    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->name('login.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated user routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Booking routes
    |--------------------------------------------------------------------------
    */

    Route::get('/dat-san/{stadium}', [UserBookingController::class, 'create'])
        ->whereNumber('stadium')
        ->name('user.bookings.create');

    Route::post('/dat-san', [UserBookingController::class, 'store'])
        ->name('user.bookings.store');

    Route::post('/stadiums/{stadium}', [UserBookingController::class, 'storeFromStadium'])
        ->whereNumber('stadium')
        ->name('user.bookings.store.from-stadium');

    // Đặt sân cố định theo tháng.
    Route::get('/dat-san-thang/{stadium}', [UserBookingController::class, 'createMonthly'])
        ->whereNumber('stadium')
        ->name('user.bookings.createMonthly');

    Route::post('/dat-san-thang', [UserBookingController::class, 'storeMonthly'])
        ->name('user.bookings.storeMonthly');

    Route::get('/don-dat-san-cua-toi', [UserBookingController::class, 'index'])
        ->name('user.bookings.index');

    Route::get('/don-dat-san-cua-toi/{booking}', [UserBookingController::class, 'show'])
        ->whereNumber('booking')
        ->name('user.bookings.show');

    // User tự thêm giờ (gia hạn sân) cho đơn của mình
    Route::post('/don-dat-san-cua-toi/{booking}/add-extra-time', [UserBookingController::class, 'addExtraTime'])
        ->whereNumber('booking')
        ->name('user.bookings.add-extra-time');

    Route::delete('/don-dat-san-cua-toi/{booking}', [UserBookingController::class, 'destroy'])
        ->whereNumber('booking')
        ->name('user.bookings.destroy');

    Route::post('/don-dat-san-cua-toi/{booking}/review', [UserReviewController::class, 'storeBooking'])
        ->whereNumber('booking')
        ->name('user.bookings.review.store');

    Route::post('/don-dat-san-cua-toi/{booking}/confirm-refund', [UserBookingController::class, 'confirmRefund'])
        ->whereNumber('booking')
        ->name('user.bookings.confirmRefund');

    Route::post('/don-dat-san-cua-toi/{booking}/dispute-refund', [UserBookingController::class, 'disputeRefund'])
        ->whereNumber('booking')
        ->name('user.bookings.disputeRefund');

    /*
    |--------------------------------------------------------------------------
    | User profile and notifications
    |--------------------------------------------------------------------------
    */

    Route::get('/ho-so-ca-nhan', [ProfileController::class, 'index'])
        ->name('user.profile.index');

    Route::put('/ho-so-ca-nhan', [ProfileController::class, 'update'])
        ->name('user.profile.update');

    Route::put('/ho-so-ca-nhan/mat-khau', [ProfileController::class, 'updatePassword'])
        ->name('user.profile.password');

    Route::get('/thong-bao', [UserNotificationController::class, 'index'])
        ->name('user.notifications.index');

    Route::get('/thong-bao/{id}', [UserNotificationController::class, 'show'])
        ->whereNumber('id')
        ->name('user.notifications.show');

    Route::post('/stadiums/{stadium}/reviews', [UserReviewController::class, 'store'])
        ->whereNumber('stadium')
        ->name('stadiums.reviews.store');

    /*
    |--------------------------------------------------------------------------
    | VNPay routes
    |--------------------------------------------------------------------------
    */

    Route::get('/thanh-toan/{booking_id}', [PaymentController::class, 'showPaymentPage'])
        ->whereNumber('booking_id')
        ->name('user.payment.show');

    Route::post('/thanh-toan/process', [PaymentController::class, 'processPayment'])
        ->name('user.payment.process');

    Route::get('/vnpay-return', [PaymentController::class, 'vnpayReturn'])
        ->name('vnpay.return');
});

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
|
| Admin/nhân viên xác nhận check-in bằng mã đơn mô phỏng;
| check-out và xử lý no-show do Scheduler tự động thực hiện.
|
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', EnsureUserIsAdmin::class])
    ->group(function (): void {
        Route::get('/', fn () => redirect()->route('admin.dashboard'));

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Notifications
        |--------------------------------------------------------------------------
        */

        Route::get('/notifications', [AdminNotificationController::class, 'index'])
            ->name('notifications.index');

        Route::get('/notifications/create', [AdminNotificationController::class, 'create'])
            ->name('notifications.create');

        Route::post('/notifications', [AdminNotificationController::class, 'store'])
            ->name('notifications.store');

        /*
        |--------------------------------------------------------------------------
        | Roles and users
        |--------------------------------------------------------------------------
        */

        Route::resource('roles', RoleController::class);

        Route::resource('users', UserController::class)
            ->except(['show']);

        Route::match(
            ['get', 'post', 'patch'],
            'users/{user}/toggle-status',
            [UserController::class, 'toggleStatus']
        )
            ->whereNumber('user')
            ->name('users.toggle-status');

        /*
        |--------------------------------------------------------------------------
        | Stadiums, fields, schedules and prices
        |--------------------------------------------------------------------------
        */

        Route::resource('stadiums', StadiumsController::class);

        Route::post('stadiums/{stadium}/fields', [StadiumsController::class, 'storeField'])
            ->whereNumber('stadium')
            ->name('stadiums.fields.store');

        Route::put('stadiums/{stadium}/fields/{field}', [StadiumsController::class, 'updateField'])
            ->whereNumber('stadium')
            ->whereNumber('field')
            ->name('stadiums.fields.update');

        Route::delete('stadiums/{stadium}/fields/{field}', [StadiumsController::class, 'destroyField'])
            ->whereNumber('stadium')
            ->whereNumber('field')
            ->name('stadiums.fields.destroy');

        Route::get('time-slots', [TimeSlotsController::class, 'index'])
            ->name('time-slots.index');

        Route::get('time-slots/{stadium}', [TimeSlotsController::class, 'show'])
            ->whereNumber('stadium')
            ->name('time-slots.show');

        Route::post('time-slots/{stadium}', [TimeSlotsController::class, 'storeForStadium'])
            ->whereNumber('stadium')
            ->name('time-slots.store');

        Route::post('time-slots/{stadium}/add', [TimeSlotsController::class, 'addForStadium'])
            ->whereNumber('stadium')
            ->name('time-slots.add');

        Route::put('time-slots/{stadium}/{timeSlot}', [TimeSlotsController::class, 'update'])
            ->whereNumber('stadium')
            ->whereNumber('timeSlot')
            ->name('time-slots.update');

        Route::delete('time-slots/{stadium}/{timeSlot}', [TimeSlotsController::class, 'destroy'])
            ->whereNumber('stadium')
            ->whereNumber('timeSlot')
            ->name('time-slots.destroy');

        Route::get('stadiums/{stadium}/prices', [StadiumsController::class, 'prices'])
            ->whereNumber('stadium')
            ->name('stadiums.prices.index');

        Route::post('stadiums/{stadium}/prices', [StadiumsController::class, 'storePrices'])
            ->whereNumber('stadium')
            ->name('stadiums.prices.store');

        Route::post('stadiums/{stadium}/prices/custom', [StadiumsController::class, 'storeCustom'])
            ->whereNumber('stadium')
            ->name('stadiums.prices.custom.store');

        Route::delete('stadiums/{stadium}/prices/custom/{slot}', [StadiumsController::class, 'destroyCustom'])
            ->whereNumber('stadium')
            ->whereNumber('slot')
            ->name('stadiums.prices.custom.destroy');

        Route::resource('field-types', FieldTypeController::class);

        Route::get('fields', [FieldController::class, 'index'])
            ->name('fields.index');

        Route::post('fields', [FieldController::class, 'store'])
            ->name('fields.store');

        Route::put('fields/{field}', [FieldController::class, 'update'])
            ->whereNumber('field')
            ->name('fields.update');

        Route::delete('fields/{field}', [FieldController::class, 'destroy'])
            ->whereNumber('field')
            ->name('fields.destroy');

        Route::resource('services', AdminServiceController::class);
        Route::resource('booking-services', BookingServiceController::class);

        /*
        |--------------------------------------------------------------------------
        | Bookings
        |--------------------------------------------------------------------------
        */

        // Route tĩnh phải đặt trước route bookings/{booking}.
        Route::get('bookings/check-in', [BookingCheckInScannerController::class, 'index'])
            ->name('bookings.check-in.index');

        Route::post('bookings/check-in', [BookingCheckInScannerController::class, 'store'])
            ->middleware('throttle:20,1')
            ->name('bookings.check-in.store');

        Route::get('bookings', [AdminBookingController::class, 'index'])
            ->name('bookings.index');

        Route::post('bookings/{id}/process-refund', [AdminBookingController::class, 'processRefund'])
            ->whereNumber('id')
            ->name('bookings.processRefund');

        Route::get('bookings/{id}/invoice', [AdminBookingController::class, 'invoice'])
            ->whereNumber('id')
            ->name('bookings.invoice');

        Route::delete('bookings/{booking}', [AdminBookingController::class, 'destroy'])
            ->whereNumber('booking')
            ->name('bookings.destroy');

        // Route động đặt cuối cùng để không nuốt /bookings/check-in.
        Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])
            ->whereNumber('booking')
            ->name('bookings.show');

        Route::get('booking-details', [BookingDetailController::class, 'index'])
            ->name('booking-details.index');

        Route::get('booking-details/{bookingDetail}', [BookingDetailController::class, 'show'])
            ->whereNumber('bookingDetail')
            ->name('booking-details.show');

        /*
        |--------------------------------------------------------------------------
        | Promotions, news and reviews
        |--------------------------------------------------------------------------
        */

        Route::resource('promotions', PromotionController::class);
        Route::resource('news', AdminNewsController::class);

        Route::resource('reviews', AdminReviewController::class)
            ->only(['index', 'destroy']);
    });