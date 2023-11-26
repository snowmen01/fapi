<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận Đơn Hàng từ {{ __('common.app_name') }}</title>
</head>

<body>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="font-size: 24px; color: #f82929;">Hóa đơn đặt hàng tại {{ __('common.app_name') }}</h1>
        <p>Xin chào: <span
                style="font-weight: bold;">{{ $customerService->getCustomerById($invoiceData['customer_id'])->name }}</span>
        </p>
        <p>Số hóa đơn: <span style="font-weight: bold;">{{ $invoiceData['code'] }}</span></p>
        <p>Ngày đặt hàng: <span
                style="font-weight: bold;">{{ \Carbon\Carbon::parse($invoiceData['created_at'])->format('d-m-Y') }}</span>
        </p>
        <p>Điện thoại đặt hàng: <span
                style="font-weight: bold;">{{ $customerService->getCustomerById($invoiceData['customer_id'])->phone }}</span>
        </p>
        <p>Địa chỉ nhận hàng: <span style="font-weight: bold;">{{ $invoiceData['address'] }}</span></p>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">STT</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Hình ảnh</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Tên sản phẩm</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Đơn giá</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Số lượng</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoiceData->childrenOrders as $index => $item)
                    <tr style="background-color: {{ $index % 2 != 0 ? '#f2f2f2' : '#ffffff' }};">
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $index + 1 }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><img
                                src="{{ asset('storage/' . $productService->getProductById($item->product_id)->image) }}"
                                alt="{{ $productService->getProductById($item->product_id)->name }}" width="80"
                                height="80"></td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            <a style="text-decoration: none; font-weight: 500; color: #f82929"
                                href="{{ route('web.products.productDetail', $productService->getProductById($item->product_id)->slug) }}">
                                {{ $productService->getProductById($item->product_id)->name . ' - ' . $propertyService->getPropertyById($item->color_id)->name . ' - ' . $propertyService->getPropertyById($item->size_id)->name }}
                            </a>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            {{ number_format($item->quantity, 0, ',', '.') }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: end;font-weight: 700" colspan="5">
                        Tổng tiền:</td>
                    <td style="border: 1px solid #ddd; padding: 8px; font-weight: 700">
                        {{ number_format($invoiceData['total'], 0, ',', '.') . ' đ' }}</td>
                </tr>
            </tbody>
        </table>

        <p>Cảm ơn đã mua hàng từ cửa hàng của chúng tôi!</p>
        <p>Xem hóa đơn điện tử tại đây: <a style="text-decoration: none; font-weight: bold; color: #f82929"
                href="{{ asset('storage/' . pathinfo($url, PATHINFO_BASENAME)) }}" download>tải xuống</a>.</p>
    </div>
</body>

</html>
