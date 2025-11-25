<x-app-layout>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Pembayaran Pesanan #{{ $order->id }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5 class="alert-heading">Informasi Pembayaran</h5>
                            <p class="mb-0">
                                <strong>Total Pembayaran:</strong> Rp{{ number_format($order->total_price, 0, ',', '.') }}
                            </p>
                        </div>

                        <div id="snap-container"></div>

                        <div class="mt-4">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali ke Detail Pesanan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    @php
        $midtransUrl = config('services.midtrans.is_production') 
            ? 'https://app.midtrans.com/snap/snap.js' 
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp
    <script src="{{ $midtransUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        window.snap.pay('{{ $snapToken }}', {
            onSuccess: function(result){
                console.log('success');
                console.log(result);
                window.location.href = '{{ route("payment.success", $order) }}';
            },
            onPending: function(result){
                console.log('pending');
                console.log(result);
                window.location.href = '{{ route("payment.success", $order) }}';
            },
            onError: function(result){
                console.log('error');
                console.log(result);
                window.location.href = '{{ route("payment.error", $order) }}';
            },
            onClose: function(){
                console.log('customer closed the popup without finishing the payment');
            }
        });
    </script>
    @endpush
</x-app-layout>

