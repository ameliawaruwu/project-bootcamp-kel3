<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Merchant') }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="py-12 bg-light">
        <div class="container">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Daftarkan Merchant Baru</h4>
                    <p class="text-muted small mb-0">Isi data lengkap lokasi dan informasi tokomu.</p>
                </div>
                <a href="{{ route('merchants.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <form action="{{ route('merchants.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-4">
                    
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 bg-white h-100" style="border-top: 4px solid #198754;">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="bi bi-shop me-2 text-success"></i>Informasi Toko
                                </h6>

                                <div class="mb-4">
                                    <label for="name" class="form-label fw-bold text-secondary small text-uppercase">Nama Merchant <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg fs-6 rounded-3 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Kedai Kopi Senja" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="phone" class="form-label fw-bold text-secondary small text-uppercase">Nomor Telepon</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                                        <input type="text" class="form-control form-control-lg fs-6 @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="0812...">
                                    </div>
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="form-label fw-bold text-secondary small text-uppercase">Deskripsi Singkat</label>
                                    <textarea class="form-control rounded-3 @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Jelaskan keunikan tokomu...">{{ old('description') }}</textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="row">
                                    <label class="form-label fw-bold text-secondary small text-uppercase mb-2">Jam Operasional</label>
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-success fw-bold">Buka</span>
                                            <input type="time" class="form-control @error('opening_time') is-invalid @enderror" id="opening_time" name="opening_time" value="{{ old('opening_time') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-danger fw-bold">Tutup</span>
                                            <input type="time" class="form-control @error('closing_time') is-invalid @enderror" id="closing_time" name="closing_time" value="{{ old('closing_time') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label for="image" class="form-label fw-bold text-secondary small text-uppercase">Foto Toko</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                    <div class="form-text small">Upload foto depan toko agar mudah dikenali.</div>
                                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="bi bi-geo-alt-fill me-2 text-danger"></i>Lokasi & Alamat
                                </h6>

                                <div class="mb-3">
                                    <label for="address" class="form-label fw-bold text-secondary small text-uppercase">Alamat Lengkap <span class="text-danger">*</span></label>
                                    <textarea class="form-control rounded-3 @error('address') is-invalid @enderror" id="address" name="address" rows="2" placeholder="Jalan, Nomor, RT/RW, Kelurahan..." required>{{ old('address') }}</textarea>
                                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3 position-relative">
                                    <label class="form-label fw-bold text-secondary small text-uppercase">Titik Lokasi (Peta)</label>
                                    <div id="map" class="rounded-4 border shadow-sm" style="height: 300px; width: 100%; z-index: 1;"></div>
                                    
                                    <button type="button" class="btn btn-light text-primary btn-sm shadow-sm position-absolute top-0 end-0 m-2 fw-bold" style="z-index: 2; margin-top: 35px !important; margin-right: 15px !important;" onclick="getLocation()">
                                        <i class="bi bi-crosshair me-1"></i> Lokasi Saya
                                    </button>
                                </div>

                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="small text-muted">Latitude</label>
                                        <input type="number" step="0.00000001" class="form-control form-control-sm bg-light" id="latitude" name="latitude" placeholder="-6.xxxxx" value="{{ old('latitude') }}" readonly>
                                        @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted">Longitude</label>
                                        <input type="number" step="0.00000001" class="form-control form-control-sm bg-light" id="longitude" name="longitude" placeholder="107.xxxxx" value="{{ old('longitude') }}" readonly>
                                        @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="form-text small mt-2 text-primary">
                                    <i class="bi bi-info-circle me-1"></i> Klik pada peta untuk menandai lokasi akurat.
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                    <a href="{{ route('merchants.index') }}" class="btn btn-light text-muted fw-bold rounded-pill px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-success fw-bold rounded-pill px-5 shadow-sm">
                        <i class="bi bi-check-circle-fill me-2"></i> Simpan Merchant
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
        // Inisialisasi Map (Default ke Bandung/Jakarta, sesuaikan jika perlu)
        let map = L.map('map').setView([-6.9175, 107.6191], 13); // Default Bandung
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker = null;

        // Fungsi Update Input saat marker berubah
        function updateInputs(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);
        }

        // Event Klik pada Peta
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
            updateInputs(lat, lng);
        });

        // Fungsi Lokasi Saya (Browser Geolocation)
        function getLocation() {
            if (navigator.geolocation) {
                // Tampilkan loading di tombol (opsional UX)
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    updateInputs(lat, lng);
                    
                    map.setView([lat, lng], 16);
                    
                    if (marker) {
                        marker.setLatLng([lat, lng]);
                    } else {
                        marker = L.marker([lat, lng]).addTo(map);
                    }
                }, function(error) {
                    alert('Gagal mendapatkan lokasi: ' + error.message);
                });
            } else {
                alert('Browser Anda tidak mendukung Geolocation.');
            }
        }
    </script>
</x-app-layout>