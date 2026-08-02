<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Hóa đơn {{ $booking->booking_code ?? ('#'.$booking->id) }}
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f3f4f6;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        .invoice-page {
            width: min(980px, calc(100% - 32px));
            margin: 28px auto;
            padding: 38px;
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            gap: 28px;
            padding-bottom: 24px;
            border-bottom: 2px solid #e5e7eb;
        }

        .brand h1 {
            margin: 0 0 6px;
            font-size: 28px;
            letter-spacing: .04em;
        }

        .brand p,
        .invoice-meta p {
            margin: 3px 0;
            color: #6b7280;
        }

        .invoice-meta {
            min-width: 280px;
            text-align: right;
        }

        .invoice-title {
            margin: 0 0 10px;
            color: #2563eb;
            font-size: 24px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .status-badge {
            display: inline-block;
            margin-top: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            font-weight: 700;
        }

        .status-badge.cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .section {
            margin-top: 28px;
        }

        .section-title {
            margin: 0 0 13px;
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .info-card {
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #f9fafb;
        }

        .info-label {
            margin-bottom: 4px;
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
        }

        .info-value {
            font-weight: 700;
            word-break: break-word;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            text-align: left;
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .amount {
            white-space: nowrap;
            font-weight: 700;
        }

        .summary {
            width: min(430px, 100%);
            margin-top: 22px;
            margin-left: auto;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 9px 0;
            border-bottom: 1px dashed #d1d5db;
        }

        .summary-row.total {
            margin-top: 4px;
            padding-top: 15px;
            border-bottom: 0;
            color: #2563eb;
            font-size: 19px;
            font-weight: 800;
        }

        .payment-note {
            margin-top: 16px;
            padding: 14px 16px;
            border-left: 4px solid #2563eb;
            border-radius: 8px;
            background: #eff6ff;
        }

        .footer {
            margin-top: 36px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            text-align: center;
            font-size: 12px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            width: min(980px, calc(100% - 32px));
            margin: 22px auto 0;
        }

        .button {
            display: inline-block;
            padding: 11px 18px;
            border: 0;
            border-radius: 10px;
            color: #ffffff;
            background: #2563eb;
            cursor: pointer;
            font-weight: 700;
            text-decoration: none;
        }

        .button.secondary {
            color: #111827;
            background: #e5e7eb;
        }

        @media (max-width: 720px) {
            .invoice-page {
                width: 100%;
                margin: 0;
                padding: 24px 18px;
                border-radius: 0;
            }

            .actions {
                width: calc(100% - 24px);
                margin-top: 12px;
            }

            .invoice-header {
                display: block;
            }

            .invoice-meta {
                margin-top: 22px;
                text-align: left;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 12px;
            }

            th,
            td {
                padding: 9px 7px;
            }
        }

        @media print {
            @page {
                size: A4;
                margin: 12mm;
            }

            body {
                background: #ffffff;
            }

            .actions {
                display: none !important;
            }

            .invoice-page {
                width: 100%;
                margin: 0;
                padding: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .info-card,
            th {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
@php
    $details = collect($booking->bookingDetails ?? []);
    $services = collect($booking->bookingServices ?? []);
    $payments = collect($booking->payments ?? []);

    $status = strtolower((string) ($booking->status ?? 'pending'));
    $usageStatus = strtolower((string) (
        $booking->usage_status ?? 'not_checked_in'
    ));
    $paymentStatus = strtolower((string) (
        $booking->payment_status ?? 'unpaid'
    ));

    $statusLabels = [
        'pending' => 'Chờ thanh toán',
        'confirmed' => 'Đã xác nhận',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];

    $paymentLabels = [
        'unpaid' => 'Chưa thanh toán',
        'deposit_paid' => 'Đã đặt cọc',
        'paid' => 'Đã thanh toán đủ',
        'paid_full' => 'Đã thanh toán đủ',
        'completed' => 'Đã thanh toán đủ',
        'partially_refunded' => 'Đã hoàn một phần',
        'refunded' => 'Đã hoàn tiền',
    ];

    $usageLabels = [
        'not_checked_in' => 'Chưa check-in',
        'checked_in' => 'Đã check-in',
        'checked_out' => 'Đã check-out',
    ];

    $customerName = data_get($booking, 'user.name')
        ?? $booking->customer_name
        ?? $booking->name
        ?? 'Khách hàng';

    $customerPhone = $booking->customer_phone
        ?? $booking->phone
        ?? data_get($booking, 'user.phone')
        ?? '-';

    $customerEmail = data_get($booking, 'user.email')
        ?? $booking->customer_email
        ?? $booking->email
        ?? '-';

    $totalAmount = max(
        0,
        (float) (
            $booking->total_amount
            ?? $booking->total_price
            ?? $booking->total
            ?? 0
        )
    );

    $discountAmount = max(
        0,
        (float) ($booking->discount_amount ?? 0)
    );

    $finalAmount = max(
        0,
        (float) (
            $booking->final_amount
            ?? ($totalAmount - $discountAmount)
        )
    );

    $depositAmount = max(
        0,
        (float) ($booking->deposit_amount ?? 0)
    );

    $paidAmount = max(
        0,
        (float) ($booking->paid_amount ?? 0)
    );

    if (
        $paidAmount <= 0
        && in_array($paymentStatus, ['paid', 'paid_full', 'completed'], true)
    ) {
        $paidAmount = $finalAmount;
    }

    if (
        $paidAmount <= 0
        && (
            $paymentStatus === 'deposit_paid'
            || (bool) ($booking->is_deposit_paid ?? false)
        )
    ) {
        $paidAmount = $depositAmount;
    }

    $remainingAmount = max(0, $finalAmount - $paidAmount);

    $serviceTotal = $services->sum(function ($item) {
        $quantity = max(0, (int) ($item->quantity ?? 0));
        $price = max(0, (float) ($item->price ?? 0));

        return (float) (
            $item->total_price
            ?? $item->total
            ?? ($quantity * $price)
        );
    });

    $invoiceDate = !empty($booking->invoice_issued_at)
        ? \Carbon\Carbon::parse($booking->invoice_issued_at)
        : now();

    $createdAt = !empty($booking->created_at)
        ? \Carbon\Carbon::parse($booking->created_at)
        : null;

    $checkedInAt = !empty($booking->checked_in_at)
        ? \Carbon\Carbon::parse($booking->checked_in_at)
        : null;

    $checkedOutAt = !empty($booking->checked_out_at)
        ? \Carbon\Carbon::parse($booking->checked_out_at)
        : null;
@endphp

<div class="actions">
    <a
        href="{{ route('admin.bookings.show', $booking->id) }}"
        class="button secondary"
    >
        Quay lại đơn
    </a>

    <button type="button" class="button" onclick="window.print()">
        In hóa đơn
    </button>
</div>

<main class="invoice-page">
    <header class="invoice-header">
        <div class="brand">
            <h1>SÂN BÓNG LFC</h1>
            <p>Hệ thống đặt sân bóng trực tuyến</p>
            <p>Hóa đơn dịch vụ đặt sân</p>
        </div>

        <div class="invoice-meta">
            <div class="invoice-title">Hóa đơn</div>
            <p>
                Mã đơn:
                <strong>
                    {{ $booking->booking_code
                        ?? $booking->code
                        ?? ('#'.$booking->id) }}
                </strong>
            </p>
            <p>
                Ngày lập:
                <strong>{{ $invoiceDate->format('H:i d/m/Y') }}</strong>
            </p>

            <span class="status-badge {{ $status }}">
                {{ $statusLabels[$status] ?? ucfirst($status) }}
            </span>
        </div>
    </header>

    <section class="section">
        <h2 class="section-title">Thông tin khách hàng</h2>

        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">Khách hàng</div>
                <div class="info-value">{{ $customerName }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">Số điện thoại</div>
                <div class="info-value">{{ $customerPhone }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $customerEmail }}</div>
            </div>

            <div class="info-card">
                <div class="info-label">Ngày đặt đơn</div>
                <div class="info-value">
                    {{ $createdAt?->format('H:i d/m/Y') ?? '-' }}
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <h2 class="section-title">Chi tiết sân đặt</h2>

        <table>
            <thead>
                <tr>
                    <th style="width: 36%;">Sân</th>
                    <th style="width: 18%;">Ngày</th>
                    <th style="width: 24%;">Khung giờ</th>
                    <th class="text-right" style="width: 22%;">Thành tiền</th>
                </tr>
            </thead>

            <tbody>
                @forelse($details as $detail)
                    @php
                        $fieldName = data_get($detail, 'field.name')
                            ?? data_get($detail, 'field.field_name')
                            ?? data_get($detail, 'field_name')
                            ?? 'Sân chưa xác định';

                        $bookingDate = data_get($detail, 'booking_date')
                            ?? data_get($detail, 'date')
                            ?? data_get($booking, 'booking_date');

                        $startTime = data_get($detail, 'slot_start_time')
                            ?? data_get($detail, 'start_time')
                            ?? data_get($detail, 'timeSlot.start_time')
                            ?? data_get($detail, 'time_slot.start_time')
                            ?? '-';

                        $endTime = data_get($detail, 'slot_end_time')
                            ?? data_get($detail, 'end_time')
                            ?? data_get($detail, 'timeSlot.end_time')
                            ?? data_get($detail, 'time_slot.end_time')
                            ?? '-';

                        $lineAmount = max(
                            0,
                            (float) (
                                data_get($detail, 'total_price')
                                ?? data_get($detail, 'price')
                                ?? data_get($detail, 'field_price')
                                ?? data_get($detail, 'field_price_per_hour')
                                ?? 0
                            )
                        );
                    @endphp

                    <tr>
                        <td>
                            <strong>{{ $fieldName }}</strong>
                        </td>
                        <td>
                            {{ $bookingDate
                                ? \Carbon\Carbon::parse($bookingDate)->format('d/m/Y')
                                : '-' }}
                        </td>
                        <td>
                            {{ $startTime }} – {{ $endTime }}
                        </td>
                        <td class="text-right amount">
                            {{ number_format($lineAmount, 0, ',', '.') }}đ
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            Không có dữ liệu chi tiết sân.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    @if($services->isNotEmpty())
        <section class="section">
            <h2 class="section-title">Dịch vụ đi kèm</h2>

            <table>
                <thead>
                    <tr>
                        <th>Dịch vụ</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-right">Đơn giá</th>
                        <th class="text-right">Thành tiền</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($services as $item)
                        @php
                            $quantity = max(0, (int) ($item->quantity ?? 0));
                            $price = max(0, (float) ($item->price ?? 0));
                            $lineTotal = max(
                                0,
                                (float) (
                                    $item->total_price
                                    ?? $item->total
                                    ?? ($quantity * $price)
                                )
                            );
                        @endphp

                        <tr>
                            <td>
                                {{ data_get($item, 'service.name')
                                    ?? data_get($item, 'service.service_name')
                                    ?? $item->service_name
                                    ?? 'Dịch vụ' }}
                            </td>
                            <td class="text-center">{{ $quantity }}</td>
                            <td class="text-right amount">
                                {{ number_format($price, 0, ',', '.') }}đ
                            </td>
                            <td class="text-right amount">
                                {{ number_format($lineTotal, 0, ',', '.') }}đ
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif

    <section class="section">
        <h2 class="section-title">Thanh toán và sử dụng sân</h2>

        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">Thanh toán</div>
                <div class="info-value">
                    {{ $paymentLabels[$paymentStatus] ?? ucfirst($paymentStatus) }}
                </div>
            </div>

            <div class="info-card">
                <div class="info-label">Trạng thái sử dụng</div>
                <div class="info-value">
                    {{ $usageLabels[$usageStatus] ?? ucfirst($usageStatus) }}
                </div>
            </div>

            <div class="info-card">
                <div class="info-label">Check-in</div>
                <div class="info-value">
                    {{ $checkedInAt?->format('H:i d/m/Y') ?? 'Chưa check-in' }}
                </div>
            </div>

            <div class="info-card">
                <div class="info-label">Check-out</div>
                <div class="info-value">
                    {{ $checkedOutAt?->format('H:i d/m/Y') ?? 'Chưa check-out' }}
                </div>
            </div>
        </div>

        <div class="summary">
            <div class="summary-row">
                <span>Tổng tiền trước giảm</span>
                <strong>{{ number_format($totalAmount, 0, ',', '.') }}đ</strong>
            </div>

            @if($serviceTotal > 0)
                <div class="summary-row">
                    <span>Tổng dịch vụ</span>
                    <strong>{{ number_format($serviceTotal, 0, ',', '.') }}đ</strong>
                </div>
            @endif

            <div class="summary-row">
                <span>Giảm giá</span>
                <strong>-{{ number_format($discountAmount, 0, ',', '.') }}đ</strong>
            </div>

            <div class="summary-row">
                <span>Đã thanh toán</span>
                <strong>{{ number_format($paidAmount, 0, ',', '.') }}đ</strong>
            </div>

            <div class="summary-row">
                <span>Còn lại</span>
                <strong>{{ number_format($remainingAmount, 0, ',', '.') }}đ</strong>
            </div>

            <div class="summary-row total">
                <span>Tổng thanh toán</span>
                <span>{{ number_format($finalAmount, 0, ',', '.') }}đ</span>
            </div>
        </div>

        @if($payments->isNotEmpty())
            @php
                $latestPayment = $payments
                    ->sortByDesc(fn ($payment) => $payment->created_at ?? null)
                    ->first();

                $paymentMethodName = data_get($latestPayment, 'paymentMethod.name')
                    ?? data_get($latestPayment, 'payment_method.name')
                    ?? data_get($latestPayment, 'method')
                    ?? data_get($latestPayment, 'payment_method')
                    ?? 'Không xác định';

                $transactionCode = data_get($latestPayment, 'transaction_code')
                    ?? data_get($latestPayment, 'transaction_id')
                    ?? data_get($latestPayment, 'vnp_txn_ref')
                    ?? '-';
            @endphp

            <div class="payment-note">
                <strong>Phương thức:</strong> {{ $paymentMethodName }}<br>
                <strong>Mã giao dịch:</strong> {{ $transactionCode }}
            </div>
        @endif
    </section>

    @if(!empty($booking->note))
        <section class="section">
            <h2 class="section-title">Ghi chú</h2>
            <div class="payment-note">
                {{ $booking->note }}
            </div>
        </section>
    @endif

    <footer class="footer">
        Hóa đơn được tạo tự động từ hệ thống đặt sân bóng LFC.
        <br>
        Cảm ơn quý khách đã sử dụng dịch vụ.
    </footer>
</main>
</body>
</html>