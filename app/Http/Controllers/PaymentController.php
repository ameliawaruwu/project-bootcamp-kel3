<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Midtrans\Transaction;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    /**
     * Initialize payment and get Snap token
     */
    public function createPayment(Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan pembayaran pesanan ini.');
        }

        // Check if order is already paid
        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Pesanan ini sudah dibayar.');
        }

        // Check if order is cancelled
        if ($order->status === 'cancelled') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Pesanan ini sudah dibatalkan.');
        }

        try {
            // Prepare transaction details
            $transactionDetails = [
                'order_id' => 'ORDER-' . $order->id . '-' . time(),
                'gross_amount' => (int) $order->total_price,
            ];

            // Prepare customer details
            $customerDetails = [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone ?? '081234567890', // Default phone if not available
            ];

            // Prepare item details
            $itemDetails = [];
            foreach ($order->orderItems as $item) {
                $itemDetails[] = [
                    'id' => $item->foodMenu->id,
                    'price' => (int) $item->price,
                    'quantity' => $item->quantity,
                    'name' => $item->foodMenu->name,
                ];
            }

            // Prepare Snap parameters
            $params = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
            ];

            // Get Snap token
            $snapToken = Snap::getSnapToken($params);

            // Save payment token to order
            $order->update([
                'payment_token' => $snapToken,
                'payment_status' => 'pending',
                'transaction_id' => $transactionDetails['order_id'],
            ]);

            return view('orders.payment', [
                'order' => $order,
                'snapToken' => $snapToken,
                'clientKey' => config('services.midtrans.client_key'),
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans payment error: ' . $e->getMessage());
            return redirect()->route('orders.show', $order)
                ->with('error', 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
        }
    }

    /**
     * Handle Midtrans notification callback
     */
    public function notification(Request $request)
    {
        try {
            // Log the raw request for debugging
            Log::info('Midtrans notification received', [
                'raw_request' => $request->all(),
            ]);

            $notification = new Notification();

            $transaction = $notification->transaction_status;
            $type = $notification->payment_type;
            $orderId = $notification->order_id;
            $fraud = $notification->fraud_status;

            // Extract order ID from transaction ID (format: ORDER-{id}-{timestamp})
            $orderIdParts = explode('-', $orderId);
            if (count($orderIdParts) < 2) {
                Log::error('Invalid order ID format: ' . $orderId);
                return response()->json(['status' => 'error', 'message' => 'Invalid order ID'], 400);
            }

            $orderIdNumber = $orderIdParts[1];
            $order = Order::find($orderIdNumber);

            if (!$order) {
                Log::error('Order not found: ' . $orderIdNumber);
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            // Log notification details
            Log::info('Midtrans notification received', [
                'order_id' => $orderIdNumber,
                'transaction_status' => $transaction,
                'payment_type' => $type,
                'fraud_status' => $fraud,
                'order_id_from_midtrans' => $orderId,
            ]);

            // Use helper method to update order status
            $status = (object) [
                'transaction_status' => $transaction,
                'payment_type' => $type,
                'fraud_status' => $fraud,
            ];
            $this->updateOrderFromTransactionStatus($order, $status);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Midtrans notification error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle payment success redirect
     */
    public function success(Order $order)
    {
        // Refresh order from database
        $order->refresh();

        // Check payment status directly from Midtrans if we have transaction_id
        if ($order->transaction_id) {
            try {
                $status = Transaction::status($order->transaction_id);
                $this->updateOrderFromTransactionStatus($order, $status);
                $order->refresh();
            } catch (\Exception $e) {
                Log::error('Error checking payment status from Midtrans: ' . $e->getMessage());
            }
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order)
                ->with('success', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');
        }

        return redirect()->route('orders.show', $order)
            ->with('info', 'Pembayaran sedang diproses. Kami akan mengirimkan notifikasi setelah pembayaran dikonfirmasi.');
    }

    /**
     * Check payment status manually
     */
    public function checkStatus(Order $order)
    {
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin() && !Auth::user()->isMerchant()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat status pembayaran ini.');
        }

        if (!$order->transaction_id) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Transaksi belum dimulai.');
        }

        try {
            $status = Transaction::status($order->transaction_id);
            $this->updateOrderFromTransactionStatus($order, $status);
            $order->refresh();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Status pembayaran telah diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error checking payment status: ' . $e->getMessage());
            return redirect()->route('orders.show', $order)
                ->with('error', 'Gagal memeriksa status pembayaran. Silakan coba lagi.');
        }
    }

    /**
     * Update order status from Midtrans transaction status
     */
    private function updateOrderFromTransactionStatus(Order $order, $status)
    {
        $transactionStatus = $status->transaction_status ?? null;
        $paymentType = $status->payment_type ?? null;
        $fraudStatus = $status->fraud_status ?? null;

        Log::info('Updating order payment status', [
            'order_id' => $order->id,
            'transaction_status' => $transactionStatus,
            'payment_type' => $paymentType,
            'fraud_status' => $fraudStatus,
        ]);

        if ($transactionStatus == 'capture') {
            if ($paymentType == 'credit_card') {
                if ($fraudStatus == 'challenge') {
                    $order->update(['payment_status' => 'pending']);
                } else {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed',
                    ]);
                }
            } else {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                ]);
            }
        } elseif ($transactionStatus == 'settlement') {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
            ]);
        } elseif ($transactionStatus == 'pending') {
            $order->update(['payment_status' => 'pending']);
        } elseif ($transactionStatus == 'deny') {
            $order->update(['payment_status' => 'failed']);
        } elseif ($transactionStatus == 'expire') {
            $order->update(['payment_status' => 'expired']);
        } elseif ($transactionStatus == 'cancel') {
            $order->update(['payment_status' => 'cancelled']);
        }
    }

    /**
     * Handle payment failure/error redirect
     */
    public function error(Order $order)
    {
        return redirect()->route('orders.show', $order)
            ->with('error', 'Pembayaran gagal atau dibatalkan. Silakan coba lagi.');
    }
}
