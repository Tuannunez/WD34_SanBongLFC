<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\StadiumController;
use App\Http\Controllers\User\ServiceController as UserServiceController;
use App\Http\Controllers\User\BookingController as UserBookingController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\PaymentController;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StadiumsController;
use App\Http\Controllers\Admin\TimeSlotsController;
use App\Http\Controllers\Admin\FieldController;
use App\Http\Controllers\Admin\FieldTypeController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\BookingServiceController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\BookingDetailController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\User\ReviewController as UserReviewController;

Route::middleware(['web'])->group(function () {

    Route::get('/', [StadiumController::class, 'index'])
        ->name('home');

    Route::view('/gioi-thieu', 'user.about')
        ->name('about');

    // Public news listing and detail
    Route::get('/tin-tuc', [App\Http\Controllers\User\NewsController::class, 'index'])
        ->name('news.index');
    Route::get('/tin-tuc/{news}', [App\Http\Controllers\User\NewsController::class, 'show'])
        ->name('news.show');

    Route::get('/stadiums', [StadiumController::class, 'list'])
        ->name('stadiums.index');

    Route::get('/stadiums/{id}', [StadiumController::class, 'show'])
        ->name('stadiums.show');

    Route::get('/services', [UserServiceController::class, 'index'])
        ->name('services.index');

    Route::get('/services/{service}', [UserServiceController::class, 'show'])
        ->name('services.show');

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

    Route::get('/stadiums/{stadium}/availability', [UserBookingController::class, 'availability'])
        ->name('user.bookings.availability');

    Route::middleware(['auth'])->group(function () {

        Route::get('/dat-san/{stadium}', [UserBookingController::class, 'create'])
            ->name('user.bookings.create');

        // 🔥 ROUTE MỚI: MỞ TRANG RIÊNG DÀNH CHO ĐẶT CỐ ĐỊNH THEO THÁNG
        Route::get('/dat-san-thang/{stadium}', [UserBookingController::class, 'createMonthly'])
            ->name('user.bookings.createMonthly');

        Route::post('/dat-san', [UserBookingController::class, 'store'])
            ->name('user.bookings.store');

        // 🔥 ROUTE XỬ LÝ LƯU ĐƠN LỊCH CỐ ĐỊNH THEO THÁNG
        Route::post('/dat-san-thang', [UserBookingController::class, 'storeMonthly'])
            ->name('user.bookings.storeMonthly');

        Route::get('/don-dat-san-cua-toi', [UserBookingController::class, 'index'])
            ->name('user.bookings.index');

        Route::get('/don-dat-san-cua-toi/{booking}', [UserBookingController::class, 'show'])
            ->name('user.bookings.show');

        Route::post('/don-dat-san-cua-toi/{booking}/review', [UserReviewController::class, 'storeBooking'])
            ->name('user.bookings.review.store');

        Route::delete('/don-dat-san-cua-toi/{booking}', [UserBookingController::class, 'destroy'])
            ->name('user.bookings.destroy');

        // =========================================================================
        // TUYẾN ĐƯỜNG PHẢN HỒI HOÀN TIỀN DÀNH CHO USER
        // =========================================================================
        Route::post('/don-dat-san-cua-toi/{booking}/confirm-refund', [UserBookingController::class, 'confirmRefund'])
            ->name('user.bookings.confirmRefund');

        Route::post('/don-dat-san-cua-toi/{booking}/dispute-refund', [UserBookingController::class, 'disputeRefund'])
            ->name('user.bookings.disputeRefund');

        Route::get('/ho-so-ca-nhan', [ProfileController::class, 'index'])
            ->name('user.profile.index');

        // User notifications
        Route::get('/thong-bao', [App\Http\Controllers\User\NotificationController::class, 'index'])
            ->name('user.notifications.index');
        Route::get('/thong-bao/{id}', [App\Http\Controllers\User\NotificationController::class, 'show'])
            ->name('user.notifications.show');

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

        Route::post('/stadiums/{stadium}/reviews', [UserReviewController::class, 'store'])
            ->name('stadiums.reviews.store');

        // =========================================================================
        // CẤU HÌNH CÁC TUYẾN ĐƯỜNG THANH TOÁN VNPAY CHO USER
        // =========================================================================

        // 1. Hiển thị trang lựa chọn phương thức thanh toán
        Route::get('/thanh-toan/{booking_id}', [PaymentController::class, 'showPaymentPage'])
            ->name('user.payment.show');

        // 2. Xử lý biểu mẫu khi người dùng bấm xác nhận thanh toán (Tạo link chuyển tiếp sang VNPay)
        Route::post('/thanh-toan/process', [PaymentController::class, 'processPayment'])
            ->name('user.payment.process');

        // 3. Đường dẫn tiếp nhận phản hồi kết quả giao dịch từ cổng VNPay trả về
        Route::get('/vnpay-return', [PaymentController::class, 'vnpayReturn'])
            ->name('vnpay.return');
    });

    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

        Route::get('/', function () {
            if (Auth::user()->role !== 'admin') {
                return redirect('/');
            }

            return redirect()->route('admin.dashboard');
        });

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('notifications.index');
        Route::get('/notifications/create', [NotificationController::class, 'create'])
            ->name('notifications.create');
        Route::post('/notifications', [NotificationController::class, 'store'])
            ->name('notifications.store');

        Route::resource('roles', RoleController::class);

        Route::resource('users', UserController::class)
            ->except(['show']);

        Route::match(['get', 'post', 'patch'], 'users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');

        Route::resource('stadiums', StadiumsController::class);
        Route::post('stadiums/{stadium}/fields', [StadiumsController::class, 'storeField'])
            ->name('stadiums.fields.store');
        Route::put('stadiums/{stadium}/fields/{field}', [StadiumsController::class, 'updateField'])
            ->name('stadiums.fields.update');
        Route::delete('stadiums/{stadium}/fields/{field}', [StadiumsController::class, 'destroyField'])
            ->name('stadiums.fields.destroy');

        Route::get('time-slots', [TimeSlotsController::class, 'index'])
            ->name('time-slots.index');
        Route::get('time-slots/{stadium}', [TimeSlotsController::class, 'show'])
            ->name('time-slots.show');
        Route::post('time-slots/{stadium}', [TimeSlotsController::class, 'storeForStadium'])
            ->name('time-slots.store');
        Route::put('time-slots/{stadium}/{timeSlot}', [TimeSlotsController::class, 'update'])
            ->name('time-slots.update');
        Route::post('time-slots/{stadium}/add', [TimeSlotsController::class, 'addForStadium'])
            ->name('time-slots.add');
        Route::delete('time-slots/{stadium}/{timeSlot}', [TimeSlotsController::class, 'destroy'])
            ->name('time-slots.destroy');

        // Per-stadium price manager (fixed slots + custom special ranges)
        Route::get('stadiums/{stadium}/prices', [StadiumsController::class, 'prices']);
        Route::post('stadiums/{stadium}/prices', [StadiumsController::class, 'storePrices']);
        Route::post('stadiums/{stadium}/prices/custom', [StadiumsController::class, 'storeCustom']);
        Route::delete('stadiums/{stadium}/prices/custom/{slot}', [StadiumsController::class, 'destroyCustom']);

        Route::resource('field-types', FieldTypeController::class);

        Route::get('fields', [FieldController::class, 'index'])->name('fields.index');
        Route::post('fields', [FieldController::class, 'store'])->name('fields.store');
        Route::put('fields/{field}', [FieldController::class, 'update'])->name('fields.update');
        Route::delete('fields/{field}', [FieldController::class, 'destroy'])->name('fields.destroy');

        Route::resource('services', ServiceController::class);

        Route::resource('booking-services', BookingServiceController::class);

        Route::resource('bookings', AdminBookingController::class)
            ->only(['index', 'show', 'update', 'destroy']);

        // =========================================================================
        // TUYẾN ĐƯỜNG UPLOAD BILL CHUYỂN KHOẢN CHO ADMIN
        // =========================================================================
        Route::post('bookings/{id}/process-refund', [AdminBookingController::class, 'processRefund'])
            ->name('bookings.processRefund');

        Route::resource('promotions', PromotionController::class);
        Route::resource('news', App\Http\Controllers\Admin\NewsController::class);
        Route::resource('reviews', AdminReviewController::class)->only(['index', 'destroy']);

        Route::get('booking-details', [BookingDetailController::class, 'index'])
            ->name('booking-details.index');

        Route::get('booking-details/{bookingDetail}', [BookingDetailController::class, 'show'])
            ->name('booking-details.show');
    });
});