<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-light">
        <div class="container">
            
            <div class="mb-5">
                <h3 class="fw-light text-dark">Selamat datang, <span class="fw-bold">{{ Auth::user()->name }}</span></h3>
                <p class="text-muted small">
                    @if(Auth::user()->isCustomer())
                        Mau makan apa hari ini? Yuk cari merchant terdekat!
                    @else
                        Berikut adalah ringkasan performa bisnis Anda.
                    @endif
                </p>
            </div>

            @if(Auth::user()->isCustomer())
            
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden text-white" style="background: linear-gradient(135deg, #0d6efd, #0dcaf0);">
                            <div class="card-body p-4 position-relative">
                                <h4 class="fw-bold mb-2">Lapar?</h4>
                                <p class="mb-4 opacity-75">Temukan berbagai menu lezat dari merchant di sekitar Anda.</p>
                                <a href="{{ route('merchants.map') }}" class="btn btn-light text-primary rounded-pill fw-bold px-4 shadow-sm">
                                    <i class="bi bi-map-fill me-2"></i>Lihat Peta Merchant
                                </a>
                                <i class="bi bi-geo-alt-fill position-absolute bottom-0 end-0 display-1 opacity-25 me-n3 mb-n3"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                            <div class="card-body p-4 d-flex flex-column justify-content-center">
                                <h5 class="fw-bold text-dark mb-3">Pesanan Saya</h5>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <div class="h2 fw-bold mb-0 text-dark">0</div> <small class="text-muted">Sedang diproses</small>
                                    </div>
                                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                                        <i class="bi bi-clock-history fs-3"></i>
                                    </div>
                                </div>
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-dark rounded-pill w-100 mt-auto">
                                    Lihat Riwayat Pesanan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            @else

                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted fw-bold text-uppercase">
                                        {{ Auth::user()->isAdmin() ? 'Total Merchants' : 'Total Menu' }}
                                    </small>
                                    <h2 class="fw-bold text-dark mt-2 mb-0">12</h2> </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                                    <i class="bi bi-shop fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted fw-bold text-uppercase">Total Orders</small>
                                    <h2 class="fw-bold text-dark mt-2 mb-0">156</h2> </div>
                                <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                                    <i class="bi bi-bag-check fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted fw-bold text-uppercase">Pendapatan</small>
                                    <h2 class="fw-bold text-success mt-2 mb-0">Rp 5.2M</h2> </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                                    <i class="bi bi-cash-coin fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endif

            <div class="mt-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Aktivitas Terbaru</h5>
                        <button class="btn btn-sm btn-link text-decoration-none">Lihat Semua</button>
                    </div>
                    <div class="text-center py-5 border rounded-3 border-dashed">
                        <p class="text-muted mb-0">Belum ada aktivitas transaksi hari ini.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>