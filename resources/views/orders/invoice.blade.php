<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { margin-bottom: 20px; }
        table { width:100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border:1px solid #333; padding:8px; text-align:left; }
        .text-right { text-align:right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Invoice #{{ $order->id }}</h2>
        <p><strong>Customer:</strong> {{ $order->user->name ?? 'N/A' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Discount</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ optional($item->productSku)->sku }}</td>
                <td>{{ optional($item->productSku->product)->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price, 2) }}</td>
                <td>{{ number_format($item->discount ?? 0, 2) }}</td>
                <td>{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tr>
            <td class="text-right"><strong>Subtotal:</strong></td>
            <td class="text-right">{{ number_format($order->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="text-right"><strong>Discount:</strong></td>
            <td class="text-right">{{ number_format($order->discount ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td class="text-right"><strong>Total:</strong></td>
            <td class="text-right">{{ number_format($order->total, 2) }}</td>
        </tr>
    </table>

    <p>Generated on {{ now()->toDateTimeString() }}</p>
</body>
</html>
