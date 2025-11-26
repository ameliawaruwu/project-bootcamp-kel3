<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Menu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Daftar Menu Makanan</h4>
                    <p class="text-muted small mb-0">Kelola katalog dan harga menu Anda.</p>
                </div>
                <a href="{{ route('foodmenu.create') }}" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Menu
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white" style="border-top: 4px solid #198754;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark text-uppercase small fw-bold">
                                <tr>
                                    <th class="py-3 ps-4 border-0" style="width: 10%;">Gambar</th>
                                    <th class="py-3 border-0" style="width: 30%;">Nama Menu</th>
                                    <th class="py-3 border-0" style="width: 30%;">Deskripsi</th>
                                    <th class="py-3 border-0" style="width: 15%;">Harga</th>
                                    <th class="py-3 pe-4 border-0 text-end" style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($foodMenus as $item)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            @if($item->image)
                                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="rounded-3 object-fit-cover shadow-sm border" style="width: 60px; height: 60px;">
                                            @else
                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-secondary border" style="width: 60px; height: 60px;">
                                                    <i class="bi bi-image fs-4 opacity-50"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-bold text-dark fs-6">{{ $item->name }}</div>
                                        </td>
                                        <td class="py-3">
                                            <span class="text-secondary small text-truncate d-inline-block" style="max-width: 280px;">
                                                {{ $item->description }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <span class="fw-bolder text-dark">Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('foodmenu.edit', $item->id) }}" class="btn btn-sm btn-light text-primary rounded-3 border" title="Edit">
                                                    <i class="bi bi-pencil-square fs-6"></i>
                                                </a>
                                                
                                                <form action="{{ route('foodmenu.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light text-danger rounded-3 border" title="Hapus">
                                                        <i class="bi bi-trash fs-6"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($foodMenus->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <div class="mb-3">
                                                <i class="bi bi-clipboard-x display-4 text-secondary opacity-25"></i>
                                            </div>
                                            <h6 class="fw-bold">Belum ada menu</h6>
                                            <p class="small mb-0">Klik tombol tambah di atas untuk memulai.</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                {{-- {{ $foodMenus->links() }} --}}
            </div>

        </div>
    </div>
</x-app-layout>