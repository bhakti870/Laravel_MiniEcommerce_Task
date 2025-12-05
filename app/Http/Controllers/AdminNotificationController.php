<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;

class AdminNotificationController extends Controller
{
    public function fetch()
    {
        // LOW STOCK NOTIFICATIONS
        $lowStock = Product::where('stock', '<=', 5)
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'low_stock',
                    'message' => "Low Stock: {$item->name} ({$item->stock} left)",
                ];
            });

        // NEW ORDERS NOTIFICATION (Last 10 minutes)
        $newOrders = Order::where('created_at', '>=', now()->subMinutes(10))
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'type' => 'new_order',
                    'message' => "New Order #{$order->id} received",
                ];
            });

        $notifications = $lowStock->merge($newOrders);

        return response()->json([
            'count' => $notifications->count(),
            'items' => $notifications,
        ]);
    }
}
