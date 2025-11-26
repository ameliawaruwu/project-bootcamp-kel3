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
                    <h4 class="fw-bold text-dark mb-1">Edit Menu Makanan</h4>
                    <p class="text-muted small mb-0">Perbarui informasi harga, deskripsi, atau foto menu.</p>
                </div>
                <a href="{{ route('foodmenu.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <form action="{{ route('foodmenu.update', $foodMenu->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white" style="border-top: 4px solid #ffc107;">
                            <div class="card-body p-4">
                                
                                <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="bi bi-pencil-square me-2 text-warning"></i>Detail Menu
                                </h6>

                                <div class="mb-4">
                                    <label for="name" class="form-label fw-bold text-secondary small text-uppercase">Nama Menu <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control form-control-lg fs-6 rounded-3 @error('name') is-invalid @enderror" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name', $foodMenu->name) }}"
                                           required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="form-label fw-bold text-secondary small text-uppercase">Deskripsi Menu <span class="text-danger">*</span></label>
                                    <textarea class="form-control rounded-3 @error('description') is-invalid @enderror" 
                                              name="description" 
                                              id="description" 
                                              rows="4"
                                              required>{{ old('description', $foodMenu->description) }}</textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                                   value="{{ old('price', $foodMenu->price) }}"
                                                   min="0"
                                                   step="500"
                                                   required>
                                        </div>
                                        @error('price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="is_available" class="form-label fw-bold text-secondary small text-uppercase">Status Ketersediaan</label>
                                        <select class="form-select form-select-lg fs-6 rounded-3 @error('is_available') is-invalid @enderror" 
                                                name="is_available" 
                                                id="is_available">
                                            <option value="1" {{ old('is_available', $foodMenu->is_available) == 1 ? 'selected' : '' }}>✅ Tersedia</option>
                                            <option value="0" {{ old('is_available', $foodMenu->is_available) == 0 ? 'selected' : '' }}>❌ Habis</option>
                                        </select>
                                        @error('is_available') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <h6 class="fw-bold text-dark mb-4 mt-2 border-bottom pb-2">Update Media</h6>

                                <div class="mb-4">
                                    <label for="image" class="form-label fw-bold text-secondary small text-uppercase">Foto Menu Baru</label>
                                    <input type="file" 
                                           class="form-control rounded-3 @error('image') is-invalid @enderror" 
                                           name="image" 
                                           id="image"
                                           accept="image/*"
                                           onchange="previewImage(event)">
                                    <div class="form-text small">Biarkan kosong jika tidak ingin mengubah foto.</div>
                                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top">
                                    <a href="{{ route('foodmenu.index') }}" class="btn btn-light text-muted fw-bold rounded-pill px-4">
                                        Batal
                                    </a>
                                    <button type="submit" class="btn btn-warning fw-bold rounded-pill px-5 shadow-sm">
                                        <i class="bi bi-save me-2"></i> Update Perubahan
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        
                        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                            <div class="card-body p-4 text-center">
                                <h6 class="fw-bold text-secondary text-uppercase small mb-3">Preview Foto</h6>
                                
                                <div id="imagePreview" class="border-2 border-dashed rounded-4 p-2 d-flex align-items-center justify-content-center bg-light position-relative overflow-hidden" style="height: 250px; border-color: #dee2e6;">
                                    @if($foodMenu->image)
                                        <img src="{{ asset('storage/' . $foodMenu->image) }}" class="w-100 h-100 object-fit-cover rounded-3" alt="{{ $foodMenu->name }}">
                                        <span class="position-absolute top-0 end-0 m-2 badge bg-dark opacity-75">Saat ini</span>
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="bi bi-image display-4 opacity-25"></i>
                                            <p class="small mb-0 mt-2">Belum ada gambar</p>
                                        </div>
                                    @endif
                                </div>
                                <p class="text-muted small mt-2 mb-0 fst-italic">Foto akan berubah di sini saat Anda memilih file baru.</p>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 bg-danger bg-opacity-10">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-danger mb-3">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Zona Bahaya
                                </h6>
                                <p class="small text-muted mb-3">Menghapus menu ini akan menghilangkannya secara permanen dari daftar pesanan.</p>
                                <button type="button" class="btn btn-danger w-100 rounded-pill fw-bold shadow-sm" onclick="deleteMenu()">
                                    <i class="bi bi-trash me-2"></i> Hapus Menu
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>

            <form id="deleteForm" action="{{ route('foodmenu.destroy', $foodMenu->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>

        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            const imagePreview = document.getElementById('imagePreview');
            
            reader.onload = function() {
                // Saat gambar baru dipilih, replace konten preview
                imagePreview.innerHTML = `
                    <img src="${reader.result}" class="w-100 h-100 object-fit-cover rounded-3 animate-fade" alt="Preview">
                    <span class="position-absolute top-0 end-0 m-2 badge bg-success shadow-sm">Baru</span>
                `;
            }
            
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        function deleteMenu() {
            if (confirm('APAKAH ANDA YAKIN?\n\nTindakan ini tidak dapat dibatalkan. Menu akan dihapus permanen.')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>

    <style>
        .animate-fade { animation: fadeIn 0.5s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .border-dashed { border-style: dashed !important; }
    </style>
</x-app-layout>