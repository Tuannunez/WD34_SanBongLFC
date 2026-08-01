<?php

return [
    // Đơn pending giữ sân trong thời gian này để hoàn tất thanh toán.
    'hold_minutes' => (int) env('BOOKING_HOLD_MINUTES', 5),

    // Pending chưa thanh toán không được tiếp tục giữ sân sau giờ bắt đầu.
    'expire_unpaid_at_start' => (bool) env('BOOKING_EXPIRE_UNPAID_AT_START', true),

    // Cho phép khách chủ động check-in sớm tối đa bao nhiêu phút.
    'check_in_early_minutes' => (int) env('BOOKING_CHECK_IN_EARLY_MINUTES', 15),

    // Quá giờ bắt đầu từng này phút mà chưa check-in thì bị coi là no-show.
    'no_show_grace_minutes' => (int) env('BOOKING_NO_SHOW_GRACE_MINUTES', 15),

    // No-show sẽ bị giữ tiền cọc.
    'forfeit_deposit_on_no_show' => (bool) env('BOOKING_FORFEIT_DEPOSIT_ON_NO_SHOW', true),

    'timezone' => env('APP_TIMEZONE', 'Asia/Ho_Chi_Minh'),
    'chunk_size' => (int) env('BOOKING_LIFECYCLE_CHUNK_SIZE', 200),
];
