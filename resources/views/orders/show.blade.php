@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Order #{{ $order->id }} Snapshot</h3>

    <pre style="background:#f5f5f5;padding:20px;border-radius:8px;overflow:auto;">
{{ json_encode($order->snapshot ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}
    </pre>

    <a href="{{ route('orders.invoice', $order->id) }}" target="_blank" class="btn btn-success">Download Invoice</a>
    <a href="{{ route('order.items', $order->id) }}" class="btn btn-secondary">Manage Items</a>
</div>
@endsection
