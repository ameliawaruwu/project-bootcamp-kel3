<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ Auth::user()->isMerchant() || Auth::user()->isAdmin() ? __('Kelola Pesanan') : __('Daftar Pesanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container"> 
            
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">
                        @if(Auth::user()->isMerchant() || Auth::user()->isAdmin())
                            Semua Pesanan Masuk
                        @else
                            Riwayat Pesanan Saya
                        @endif
                    </h4>
                    <small class="text-muted">Kelola dan pantau status transaksi Anda.</small>
                </div>

                @if(Auth::user()->isCustomer())
                    <a href="{{ route('merchants.map') }}" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Pesan Baru
                    </a>
                @endif
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white" style="border-top: 4px solid #198754;">
                <div class="card-body p-0">
                    
                    @if($orders->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-3 text-muted opacity-25">
                                <i class="bi bi-basket3 display-1"></i>
                            </div>
                            <h5 class="fw-bold text-muted">Belum ada pesanan saat ini.</h5>
                            @if(Auth::user()->isCustomer())
                                <a href="{{ route('merchants.map') }}" class="btn btn-link text-success text-decoration-none fw-bold">Mulai belanja sekarang &rarr;</a>
                            @endif
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                                <thead class="table-dark text-uppercase small fw-bold" style="letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="py-3 ps-4 border-0">No. Order</th>
                                        @if(Auth::user()->isCustomer())
                                            <th class="py-3 border-0">Merchant</th>
                                        @endif
                                        @if(Auth::user()->isMerchant() || Auth::user()->isAdmin())
                                            <th class="py-3 border-0">Pelanggan</th>
                                        @endif
                                        <th class="py-3 border-0">Tanggal</th>
                                        <th class="py-3 border-0">Alamat</th>
                                        <th class="py-3 border-0">Total</th>
                                        <th class="py-3 border-0">Status</th>
                                        @if(Auth::user()->isCustomer())
                                            <th class="py-3 border-0">Pembayaran</th>
                                        @endif
                                        <th class="py-3 pe-4 border-0 text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @foreach($orders as $order)
                                        <tr class="border-bottom border-light">
                                            <td class="ps-4 py-3">
                                                <span class="fw-bolder text-dark">#{{ $order->id }}</span>
                                            </td>

                                            @if(Auth::user()->isCustomer())
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $order->merchant->name }}</div>
                                                    <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                                                        <i class="bi bi-geo-alt me-1"></i>{{ $order->merchant->address }}
                                                    </small>
                                                </td>
                                            @endif

                                            @if(Auth::user()->isMerchant() || Auth::user()->isAdmin())
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $order->user->name }}</div>
                                                    <small class="text-muted">{{ $order->user->email }}</small>
                                                </td>
                                            @endif

                                            <td>
                                                <div class="text-dark fw-medium">{{ $order->created_at->format('d M Y') }}</div>
                                                <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                            </td>

                                            <td>
                                                <div class="text-truncate text-secondary" style="max-width: 150px;" title="{{ $order->address }}">
                                                    {{ $order->address }}
                                                </div>
                                            </td>

                                            <td>
                                                <span class="fw-bolder text-dark">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                                                <div class="small text-muted">{{ $order->orderItems->count() }} Item</div>
                                            </td>

                                            <td>
                                                @if(Auth::user()->isMerchant() || Auth::user()->isAdmin())
                                                    <form action="{{ route('merchant.orders.updateStatus', $order) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="status" class="form-select form-select-sm border bg-light rounded-pill shadow-none fw-medium" style="width: 140px; font-size: 0.85rem;" onchange="this.form.submit()">
                                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>⏳ Menunggu</option>
                                                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>✅ Konfirmasi</option>
                                                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>🍳 Proses</option>
                                                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>🛵 Dikirim</option>
                                                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>🏁 Selesai</option>
                                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>❌ Batal</option>
                                                        </select>
                                                    </form>
                                                @else
                                                    @php
                                                        $badges = [
                                                            'pending' => 'bg-warning text-dark',
                                                            'confirmed' => 'bg-info text-dark',
                                                            'processing' => 'bg-primary',
                                                            'delivered' => 'bg-primary',
                                                            'completed' => 'bg-success',
                                                            'cancelled' => 'bg-danger'
                                                        ];
                                                        $labels = [
                                                            'pending' => 'Menunggu',
                                                            'confirmed' => 'Dikonfirmasi',
                                                            'processing' => 'Diproses',
                                                            'delivered' => 'Dikirim',
                                                            'completed' => 'Selesai',
                                                            'cancelled' => 'Batal'
                                                        ];
                                                    @endphp
                                                    <span class="badge rounded-pill {{ $badges[$order->status] ?? 'bg-secondary' }} px-3 py-2">
                                                        {{ $labels[$order->status] ?? ucfirst($order->status) }}
                                                    </span>
                                                @endif
                                            </td>

                                            @if(Auth::user()->isCustomer())
                                                <td>
                                                    @if($order->payment_status == 'paid')
                                                        <span class="badge rounded-pill bg-soft-success text-success border border-success px-3">Lunas</span>
                                                    @elseif($order->payment_status == 'pending')
                                                        <span class="badge rounded-pill bg-soft-warning text-warning border border-warning px-3">Menunggu</span>
                                                    @elseif($order->payment_status == 'failed')
                                                        <span class="badge rounded-pill bg-danger px-3">Gagal</span>
                                                    @else
                                                        <span class="badge rounded-pill bg-light text-muted border px-3">Belum</span>
                                                    @endif
                                                </td>
                                            @endif

                                            <td class="pe-4 text-end">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-light text-primary border rounded-3 fw-bold px-3" title="Lihat Detail">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                
                @if(!$orders->isEmpty())
                <div class="card-footer bg-white border-top py-3">
                    <div class="row text-center text-md-start align-items-center">
                        <div class="col-md-auto me-4">
                            <small class="text-secondary text-uppercase fw-bold" style="font-size: 0.7rem;">Total Pesanan</small>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $orders->count() }}</div>
                        </div>
                        <div class="col-md-auto me-4">
                            <small class="text-secondary text-uppercase fw-bold" style="font-size: 0.7rem;">Perlu Diproses</small>
                            <div class="h5 mb-0 fw-bold text-warning">{{ $orders->where('status', 'pending')->count() }}</div>
                        </div>
                        <div class="col-md-auto">
                            <small class="text-secondary text-uppercase fw-bold" style="font-size: 0.7rem;">Selesai</small>
                            <div class="h5 mb-0 fw-bold text-success">{{ $orders->where('status', 'completed')->count() + $orders->where('status', 'delivered')->count() }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
    
    <style>
        .bg-soft-success { background-color: #d1e7dd; }
        .bg-soft-warning { background-color: #fff3cd; }
        /* Table spacing adjustment */
        table tbody tr td { vertical-align: middle; padding-top: 1rem; padding-bottom: 1rem; }
    </style>
</x-app-layout>