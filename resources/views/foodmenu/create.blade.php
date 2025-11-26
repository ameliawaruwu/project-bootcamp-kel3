<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Menu') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-light">
        <div class="container">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Tambah Menu Baru</h4>
                    <p class="text-muted small mb-0">Isi detail menu makanan yang ingin dijual.</p>
                </div>
                <a href="{{ route('foodmenu.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white" style="border-top: 4px solid #198754;">
                        <div class="card-body p-4">
                            <form action="{{ route('foodmenu.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">Informasi Dasar</h6>

                                <div class="mb-4">
                                    <label for="name" class="form-label fw-bold text-secondary small text-uppercase">Nama Menu <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control form-control-lg fs-6 rounded-3 @error('name') is-invalid @enderror" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name') }}"
                                           placeholder="Contoh: Nasi Goreng Spesial Telur Dadar"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="form-label fw-bold text-secondary small text-uppercase">Deskripsi Menu <span class="text-danger">*</span></label>
                                    <textarea class="form-control rounded-3 @error('description') is-invalid @enderror" 
                                              name="description" 
                                              id="description" 
                                              rows="4"
                                              placeholder="Jelaskan bahan utama, rasa, dan keunikan menu ini..."
                                              required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text small">Deskripsi yang menarik meningkatkan minat pembeli.</div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="price" class="form-label fw-bold text-secondary small text-uppercase">Harga (IDR) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold border-end-0 text-success">Rp</span>
                                            <input type="number" 
                                                   class="form-control form-control-lg fs-6 border-start-0 ps-0 @error('price') is-invalid @enderror" 
                                                   name="price" 
                                                   id="price" 
                                                   value="{{ old('price') }}"
                                                   min="0"
                                                   step="500"
                                                   placeholder="0"
                                                   required>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="is_available" class="form-label fw-bold text-secondary small text-uppercase">Status Ketersediaan</label>
                                        <select class="form-select form-select-lg fs-6 rounded-3 @error('is_available') is-invalid @enderror" 
                                                name="is_available" 
                                                id="is_available">
                                            <option value="1" {{ old('is_available', '1') == '1' ? 'selected' : '' }}>✅ Tersedia (Ready Stock)</option>
                                            <option value="0" {{ old('is_available') == '0' ? 'selected' : '' }}>❌ Habis (Out of Stock)</option>
                                        </select>
                                        @error('is_available')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <h6 class="fw-bold text-dark mb-4 mt-2 border-bottom pb-2">Media Gambar</h6>

                                <div class="mb-4">
                                    <label for="image" class="form-label fw-bold text-secondary small text-uppercase">Foto Menu</label>
                                    <input type="file" 
                                           class="form-control rounded-3 @error('image') is-invalid @enderror" 
                                           name="image" 
                                           id="image"
                                           accept="image/*"
                                           onchange="previewImage(event)">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text small">Format: JPG, PNG. Maksimal 2MB.</div>
                                </div>

                                <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top">
                                    <a href="{{ route('foodmenu.index') }}" class="btn btn-light text-muted fw-bold rounded-pill px-4">
                                        Batal
                                    </a>
                                    <button type="submit" class="btn btn-success fw-bold rounded-pill px-5 shadow-sm">
                                        Simpan Menu
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                        <div class="card-body p-4 text-center">
                            <h6 class="fw-bold text-secondary text-uppercase small mb-3">Preview Foto</h6>
                            
                            <div id="imagePreview" class="border-2 border-dashed rounded-4 p-4 d-flex align-items-center justify-content-center bg-light position-relative overflow-hidden" style="height: 250px; border-color: #dee2e6;">
                                <div class="text-center text-muted">
                                    <i class="bi bi-card-image display-4 opacity-25"></i>
                                    <p class="small mb-0 mt-2">Gambar akan muncul di sini</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-white p-2 rounded-circle text-primary me-2 shadow-sm">
                                    <i class="bi bi-lightbulb-fill"></i>
                                </div>
                                <h6 class="fw-bold text-primary mb-0">Tips Menu Menarik</h6>
                            </div>
                            <ul class="list-unstyled small text-secondary mb-0 d-grid gap-2">
                                <li class="d-flex align-items-start"><i class="bi bi-check-circle-fill text-primary me-2 mt-1"></i> Gunakan nama yang unik & menggugah selera.</li>
                                <li class="d-flex align-items-start"><i class="bi bi-check-circle-fill text-primary me-2 mt-1"></i> Foto menu dengan pencahayaan yang terang.</li>
                                <li class="d-flex align-items-start"><i class="bi bi-check-circle-fill text-primary me-2 mt-1"></i> Jelaskan detail rasa (pedas, manis, gurih).</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            const imagePreview = document.getElementById('imagePreview');
            
            reader.onload = function() {
                imagePreview.innerHTML = `
                    <img src="${reader.result}" class="w-100 h-100 object-fit-cover rounded-3 animate-fade" alt="Preview">
                `;
            }
            
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>

    <style>
        .animate-fade { animation: fadeIn 0.5s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .border-dashed { border-style: dashed !important; }
    </style>
</x-app-layout>