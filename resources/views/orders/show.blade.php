<x-app-layout>
    @if(session('success'))
        <div class="container py-4">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container py-4">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="container py-4">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Detail Pesanan #{{ $order->id }}</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Pemesan:</strong> {{ $order->user->name }}</p>
                            <p><strong>Email:</strong> {{ $order->user->email }}</p>
                            <p><strong>Tanggal Pesan:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                            <p><strong>Alamat Pengiriman:</strong> {{ $order->address }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-1"><strong>Status:</strong></p>
                            
                            @if(Auth::user()->hasRole('merchant') || Auth::user()->hasRole('admin'))
                                {{-- Merchant/Admin can change status --}}
                                <form action="{{ route('merchant.orders.updateStatus', $order) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-lg" onchange="this.form.submit()">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Sedang Diproses</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Telah Dikirim</option>
                                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </form>
                            @else
                                {{-- Customer only sees status badge --}}
                                @if ($order->status == 'pending')
                                    <span class="badge bg-warning text-dark fs-6">Menunggu Konfirmasi</span>
                                @elseif ($order->status == 'confirmed')
                                    <span class="badge bg-primary fs-6">Dikonfirmasi</span>
                                @elseif ($order->status == 'processing')
                                    <span class="badge bg-info text-dark fs-6">Sedang Diproses</span>
                                @elseif ($order->status == 'delivered')
                                    <span class="badge bg-success fs-6">Telah Dikirim</span>
                                @elseif ($order->status == 'completed')
                                    <span class="badge bg-dark fs-6">Selesai</span>
                                @elseif ($order->status == 'cancelled')
                                    <span class="badge bg-danger fs-6">Dibatalkan</span>
                                @endif
                            @endif
                            
                            <p class="mb-1 mt-3"><strong>Status Pembayaran:</strong></p>
                            @if($order->payment_status == 'paid')
                                <span class="badge bg-success fs-6">Sudah Dibayar</span>
                            @elseif($order->payment_status == 'pending')
                                <span class="badge bg-warning text-dark fs-6">Menunggu Pembayaran</span>
                            @elseif($order->payment_status == 'failed')
                                <span class="badge bg-danger fs-6">Pembayaran Gagal</span>
                            @elseif($order->payment_status == 'expired')
                                <span class="badge bg-secondary fs-6">Pembayaran Kadaluarsa</span>
                            @elseif($order->payment_status == 'cancelled')
                                <span class="badge bg-danger fs-6">Pembayaran Dibatalkan</span>
                            @else
                                <span class="badge bg-secondary fs-6">Belum Dibayar</span>
                            @endif
                            
                            <h4 class="mt-3"><strong>Total Harga:</strong> Rp{{ number_format($order->total_price, 0, ',', '.') }}</h4>
                        </div>
                    </div>

                    <hr>

                    {{-- Payment Section --}}
                    @if(Auth::user()->isCustomer() && $order->user_id == Auth::id())
                        @if($order->payment_status != 'paid' && $order->status != 'cancelled')
                            <div class="alert alert-warning">
                                <h5 class="alert-heading">Pembayaran Diperlukan</h5>
                                <p class="mb-3">Silakan lakukan pembayaran untuk melanjutkan proses pesanan Anda.</p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('payment.create', $order) }}" class="btn btn-primary btn-lg">
                                        <i class="bi bi-credit-card"></i> Bayar Sekarang
                                    </a>
                                    @if($order->transaction_id)
                                        <form action="{{ route('payment.check', $order) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary btn-lg">
                                                <i class="bi bi-arrow-clockwise"></i> Periksa Status Pembayaran
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @elseif($order->payment_status == 'paid')
                            <div class="alert alert-success">
                                <h5 class="alert-heading">Pembayaran Berhasil</h5>
                                <p class="mb-0">Pembayaran Anda telah dikonfirmasi. Pesanan sedang diproses.</p>
                            </div>
                        @endif
                    @endif

                    <hr>

                    <h4 class="mb-3">Item Pesanan:</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Makanan</th>
                                    <th>Harga Satuan</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->foodMenu->name }}</td>
                                        <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total Item:</th>
                                    <th>Rp{{ number_format($order->total_price, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="text-end mt-4">
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>