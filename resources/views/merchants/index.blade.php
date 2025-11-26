<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Merchant') }}
        </h2>
    </x-slot>

    <style>
        .card-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
        .text-justify { text-align: justify; }
    </style>

    <div class="py-12">
        <div class="container">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5">
                <div class="mb-3 mb-md-0">
                    <h3 class="fw-light text-dark mb-1">Jelajahi Kuliner</h3>
                    <p class="text-muted small mb-0">Temukan merchant favorit di sekitarmu.</p>
                </div>
                <div>
                    <a href="{{ route('merchants.map') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 me-2">
                        <i class="bi bi-map me-1"></i> Lihat Peta
                    </a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('merchants.create') }}" class="btn btn-dark btn-sm rounded-pill px-3">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Merchant
                        </a>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 mb-5 bg-white">
                <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h6 class="fw-bold mb-1"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Lokasi Kamu</h6>
                        <div id="location-status" class="text-muted small">Aktifkan lokasi untuk hasil terbaik.</div>
                    </div>
                    <button onclick="findNearbyMerchants()" class="btn btn-success rounded-pill px-4 shadow-sm">
                        Cari Terdekat
                    </button>
                </div>
            </div>

            <div class="row g-4" id="merchants-list">
                @forelse($merchants as $merchant)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover bg-white">
                            
                            <div class="position-relative">
                                @if($merchant->image)
                                    <img src="{{ asset('storage/' . $merchant->image) }}" class="card-img-top" alt="{{ $merchant->name }}" style="height: 220px; object-fit: cover;">
                                @else
                                    <div class="bg-light text-secondary d-flex align-items-center justify-content-center" style="height: 220px;">
                                        <i class="bi bi-shop display-4 opacity-50"></i>
                                    </div>
                                @endif
                                
                                <span class="position-absolute top-0 end-0 m-3 badge rounded-pill {{ $merchant->is_active ? 'bg-white text-success shadow-sm' : 'bg-secondary text-white' }}">
                                    @if($merchant->is_active)
                                        <i class="bi bi-circle-fill small me-1"></i> Buka
                                    @else
                                        Tutup
                                    @endif
                                </span>
                            </div>

                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold text-dark mb-1">{{ $merchant->name }}</h5>
                                <p class="text-muted small mb-3 text-truncate"><i class="bi bi-geo-alt me-1"></i> {{ $merchant->address }}</p>
                                
                                <p class="card-text text-secondary small text-justify mb-4" style="line-height: 1.6;">
                                    {{ Str::limit($merchant->description, 90) }}
                                </p>

                                <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-auto">
                                    <div class="text-muted small">
                                        <i class="bi bi-basket me-1"></i> {{ $merchant->foodMenus->count() }} Menu
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('merchants.show', $merchant) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Detail</a>
                                        
                                        @if(auth()->user()->isAdmin())
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                                    <li><a class="dropdown-item" href="{{ route('merchants.edit', $merchant) }}">Edit</a></li>
                                                    <li>
                                                        <form action="{{ route('merchants.destroy', $merchant) }}" method="POST" onsubmit="return confirm('Hapus merchant ini?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">Hapus</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="opacity-50 mb-3"><i class="bi bi-shop-window display-1 text-secondary"></i></div>
                        <h5 class="text-muted">Belum ada merchant yang terdaftar.</h5>
                    </div>
                @endforelse
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $merchants->links() }}
            </div>
        </div>
    </div>

    <script>
        function findNearbyMerchants() {
            const statusDiv = document.getElementById('location-status');
            statusDiv.innerHTML = '<span class="text-primary"><span class="spinner-border spinner-border-sm me-1"></span>Mendapatkan koordinat...</span>';

            if (!navigator.geolocation) {
                statusDiv.innerHTML = '<span class="text-danger">Browser tidak mendukung Geolocation</span>';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    statusDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Lokasi ditemukan! Mengalihkan...</span>';
                    window.location.href = `{{ route('merchants.nearby') }}?latitude=${lat}&longitude=${lng}`;
                },
                function(error) {
                    let errorMsg = 'Gagal melacak lokasi.';
                    switch(error.code) {
                        case error.PERMISSION_DENIED: errorMsg = 'Izin lokasi ditolak.'; break;
                        case error.POSITION_UNAVAILABLE: errorMsg = 'Lokasi tidak tersedia.'; break;
                        case error.TIMEOUT: errorMsg = 'Waktu permintaan habis.'; break;
                    }
                    statusDiv.innerHTML = `<span class="text-danger">${errorMsg}</span>`;
                }
            );
        }
    </script>
</x-app-layout>