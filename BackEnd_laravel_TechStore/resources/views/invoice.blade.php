<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; }
        h2 { margin-bottom: 10px; }
        p { margin: 4px 0; }
        .header { font-size: 20px; font-weight: bold; margin-bottom: 10px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        .summary-table {
            margin-top: 20px;
            width: 300px;
            float: right;
        }
        .summary-table td {
            padding: 6px;
            font-size: 13px;
            border: none
        }
        .summary-table .label {
            text-align: left;
            width: 60%;
            font-weight: bold;
        }
        .summary-table .value {
            text-align: right;
            width: 40%;
        }
        .total-row td {
            font-size: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">Order Invoice - {{ $orderCode }}</div>

    <p><strong>Customer Name:</strong> {{ $customer['fullname'] }}</p>
    <p><strong>Email:</strong> {{ $customer['email'] }}</p>
    <p><strong>Phone:</strong> {{ $customer['phone'] }}</p>
    <p><strong>Address:</strong> {{ $customer['address'] }}</p>

    <h4 style="margin-top: 20px;">Order Details:</h4>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Color</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['product_name'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ $item['color'] }}</td>
                <td>${{ $item['unit_price'] }}</td>
                <td>${{ number_format($item['unit_price'] * $item['quantity'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td class="label">Subtotal:</td>
            <td class="value">${{ $summary['subtotal'] }}</td>
        </tr>
        <tr>
            <td class="label">Shipping Fee:</td>
            <td class="value">${{ $summary['shipping_fee'] }}</td>
        </tr>
        <tr>
            <td class="label">Discount:</td>
            <td class="value">-${{ $summary['discount'] }}</td>
        </tr>
        <tr class="total-row">
            <td class="label">Total:</td>
            <td class="value">${{ $summary['total'] }}</td>
        </tr>
    </table>
</body>
</html>
