<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jelajahi Merchant') }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="py-12 bg-light">
        <div class="container">
            
            <div class="mb-4">
                <h4 class="fw-bold text-dark mb-1">Peta Kuliner</h4>
                <p class="text-muted small mb-0">Temukan merchant favorit di sekitarmu dengan mudah.</p>
            </div>

            <div class="row g-4">
                
                <div class="col-lg-4">
                    
                    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4" style="border-top: 4px solid #198754;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-4">
                                <i class="bi bi-sliders me-2 text-success"></i>Filter Lokasi
                            </h6>

                            <div class="mb-3">
                                <label for="radius" class="form-label fw-bold text-secondary small text-uppercase">Jarak Maksimal</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-broadcast"></i></span>
                                    <select class="form-select border-start-0 bg-light" id="radius">
                                        <option value="1">1 km (Sangat Dekat)</option>
                                        <option value="2">2 km (Dekat)</option>
                                        <option value="5" selected>5 km (Sedang)</option>
                                        <option value="10">10 km (Jauh)</option>
                                        <option value="20">20 km (Sangat Jauh)</option>
                                    </select>
                                </div>
                            </div>

                            <button type="button" class="btn btn-success w-100 rounded-pill fw-bold shadow-sm py-2" onclick="findMyLocation()">
                                <i class="bi bi-geo-alt-fill me-2"></i> Cari di Sekitar Saya
                            </button>

                            <div id="location-status" class="mt-3 small"></div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 bg-white">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-3">Keterangan Peta</h6>
                            
                            <ul class="list-unstyled d-grid gap-3 mb-0">
                                <li class="d-flex align-items-center">
                                    <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png" height="25" class="me-3">
                                    <div>
                                        <span class="fw-bold d-block text-dark">Lokasi Kamu</span>
                                        <small class="text-muted">Titik posisi kamu saat ini.</small>
                                    </div>
                                </li>
                                <li class="d-flex align-items-center">
                                    <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png" height="25" class="me-3">
                                    <div>
                                        <span class="fw-bold d-block text-success">Dalam Jangkauan</span>
                                        <small class="text-muted">Merchant yang berada dalam radius.</small>
                                    </div>
                                </li>
                                <li class="d-flex align-items-center">
                                    <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png" height="25" class="me-3">
                                    <div>
                                        <span class="fw-bold d-block text-danger">Diluar Jangkauan</span>
                                        <small class="text-muted">Merchant terlalu jauh dari radius.</small>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white">
                        <div class="card-body p-0 position-relative">
                            <div id="map" style="height: 650px; width: 100%; z-index: 1;"></div>
                            
                            <div class="position-absolute top-0 end-0 m-3 bg-white px-3 py-2 rounded-3 shadow-sm border opacity-90" style="z-index: 2;">
                                <small class="fw-bold text-secondary"><i class="bi bi-shop me-1"></i> Total Merchant: {{ count($merchants) }}</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Initialize map centered on Indonesia
        const map = L.map('map').setView([-6.2088, 106.8456], 12);

        // Add Tile Layer (Modern Style: CartoDB Voyager for cleaner look, or stick to OSM)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Define Icons
        const createIcon = (color) => {
            return L.icon({
                iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
        };

        const userIcon = createIcon('blue');
        const merchantIcon = createIcon('red');
        const nearbyIcon = createIcon('green');

        let userMarker = null;
        let radiusCircle = null;
        let merchantMarkers = [];

        // Load Merchants Data
        const merchants = @json($merchants);

        // Add Merchants to Map
        merchants.forEach(merchant => {
            if (merchant.latitude && merchant.longitude) {
                const marker = L.marker([merchant.latitude, merchant.longitude], {icon: merchantIcon})
                    .addTo(map);
                
                // Custom Popup Content (Styled)
                const popupContent = `
                    <div class="text-center p-2">
                        <h6 class="fw-bold text-dark mb-1">${merchant.name}</h6>
                        <p class="small text-muted mb-2 text-truncate" style="max-width: 200px;">${merchant.address}</p>
                        
                        <div class="d-flex justify-content-center gap-2 mb-2">
                            <span class="badge bg-light text-dark border"><i class="bi bi-clock"></i> ${merchant.opening_time ? merchant.opening_time.substring(0,5) : '--'} - ${merchant.closing_time ? merchant.closing_time.substring(0,5) : '--'}</span>
                            <span class="badge ${merchant.is_active ? 'bg-success' : 'bg-secondary'}">${merchant.is_active ? 'Buka' : 'Tutup'}</span>
                        </div>

                        <a href="/merchants/${merchant.id}" class="btn btn-sm btn-primary w-100 rounded-pill">
                            <i class="bi bi-basket2 me-1"></i> Lihat Menu
                        </a>
                    </div>
                `;

                marker.bindPopup(popupContent);
                merchantMarkers.push({marker: marker, data: merchant});
            }
        });

        // Auto Fit Bounds
        if (merchants.length > 0) {
            const bounds = merchants
                .filter(m => m.latitude && m.longitude)
                .map(m => [m.latitude, m.longitude]);
            if (bounds.length > 0) {
                map.fitBounds(bounds, {padding: [50, 50]});
            }
        }

        // Function: Find User Location
        function findMyLocation() {
            const statusDiv = document.getElementById('location-status');
            statusDiv.innerHTML = '<div class="alert alert-info py-2 px-3 rounded-3 border-0 small"><div class="spinner-border spinner-border-sm me-2"></div>Melacak lokasi Anda...</div>';

            if (!navigator.geolocation) {
                statusDiv.innerHTML = '<div class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i> Browser tidak mendukung Geolocation.</div>';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const radius = document.getElementById('radius').value * 1000; // Meters

                    statusDiv.innerHTML = '<div class="text-success small fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Lokasi ditemukan!</div>';

                    // Clear previous user indicators
                    if (userMarker) map.removeLayer(userMarker);
                    if (radiusCircle) map.removeLayer(radiusCircle);

                    // Add User Marker
                    userMarker = L.marker([lat, lng], {icon: userIcon})
                        .addTo(map)
                        .bindPopup('<div class="text-center fw-bold p-1">📍 Lokasi Anda</div>')
                        .openPopup();

                    // Add Radius Circle (Soft Blue Style)
                    radiusCircle = L.circle([lat, lng], {
                        color: '#0d6efd',
                        fillColor: '#0d6efd',
                        fillOpacity: 0.1,
                        radius: radius,
                        weight: 1
                    }).addTo(map);

                    // Zoom to user
                    map.setView([lat, lng], 14);

                    // Calculate Distance & Update Markers
                    merchantMarkers.forEach(({marker, data}) => {
                        if (data.latitude && data.longitude) {
                            const distance = calculateDistance(lat, lng, data.latitude, data.longitude);
                            const radiusKm = radius / 1000;

                            // Update Popup with Distance Info
                            const newPopupContent = `
                                <div class="text-center p-2">
                                    <h6 class="fw-bold text-dark mb-1">${data.name}</h6>
                                    <p class="small text-muted mb-2 text-truncate" style="max-width: 200px;">${data.address}</p>
                                    
                                    <div class="mb-2">
                                        <span class="badge ${distance <= radiusKm ? 'bg-success' : 'bg-danger'} mb-1">
                                            <i class="bi bi-geo-alt-fill"></i> ${distance.toFixed(2)} km
                                        </span>
                                    </div>

                                    <a href="/merchants/${data.id}" class="btn btn-sm btn-primary w-100 rounded-pill">
                                        <i class="bi bi-basket2 me-1"></i> Lihat Menu
                                    </a>
                                </div>
                            `;
                            marker.setPopupContent(newPopupContent);

                            // Change Icon Color
                            if (distance <= radiusKm) {
                                marker.setIcon(nearbyIcon);
                            } else {
                                marker.setIcon(merchantIcon);
                            }
                        }
                    });

                    // Clear status after 3 sec
                    setTimeout(() => { statusDiv.innerHTML = ''; }, 4000);
                },
                function(error) {
                    let msg = 'Gagal melacak lokasi.';
                    if(error.code === 1) msg = 'Izin lokasi ditolak.';
                    statusDiv.innerHTML = `<div class="text-danger small"><i class="bi bi-x-circle-fill me-1"></i> ${msg}</div>`;
                }
            );
        }

        // Helper: Haversine Distance Formula
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; 
            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function toRad(degrees) {
            return degrees * Math.PI / 180;
        }
    </script>
</x-app-layout>