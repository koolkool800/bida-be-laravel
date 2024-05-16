<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.5;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header, .footer {
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 0;
        }
        .content {
            margin-top: 20px;
        }
        .content .order-details, .content .customer-details {
            margin-bottom: 20px;
        }
        .content .order-details table, .content .order-details th, .content .order-details td {
            width: 100%;
            border: 1px solid #ddd;
            border-collapse: collapse;
            padding: 8px;
            text-align: left;
        }
        .content .order-details th {
            background-color: #f2f2f2;
        }
        .content .order-summary {
            margin-top: 20px;
        }
        .content .order-summary table {
            width: 100%;
        }
        .content .order-summary th, .content .order-summary td {
            text-align: right;
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>HÓA ĐƠN #{{ $order->id }}</h1>
            <p>{{ $order->created_at->format('d/m/Y') }}</p>
        </div>

        <div class="content">
            <div class="customer-details">
                <h2>Thông Tin Chi Tiết</h2>
                <p><strong>Tên khách hàng:</strong> {{ $order->customer_name }}</p>
                <p><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</p>
                <p><strong>Bàn:</strong> {{ $order->table_name }}</p>
                <p><strong>Loại bàn:</strong> {{ $order->setting_table_type }}</p>
                <p><strong>Giờ vào:</strong> {{ $order->start_time }}</p>
                <p><strong>Giờ ra:</strong> {{ $order->end_time }}</p>
            </div>

            <div class="order-details">
                <h2>Thực Đơn</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Loại</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->order_detail as $detail)
                            <tr>
                                <td>{{ $detail->product_name }}</td>
                                <td>{{ $detail->product_type }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>{{ number_format($detail->product_price, 2) }}</td>
                                <td>{{ number_format($detail->quantity * $detail->product_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="order-summary">
                <table>
                    <tr>
                        <th>Tổng giá tiền sản phẩm:</th>
                        <td>{{ number_format($order->total_product_price, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Tổng giá tiền thuê bàn:</th>
                        <td>{{ number_format($order->price_table, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total Price:</th>
                        <td>{{ number_format($order->total_price, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer">
            <p>Cảm ơn quý khách đã sử dụng dịch của chúng tôi!</p>
        </div>
    </div>
</body>
</html>