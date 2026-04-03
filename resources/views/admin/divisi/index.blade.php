@extends('layouts.app')

@section('title', 'Manajemen Divisi')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">🏢 MANAJEMEN DIVISI</h1>
                <p class="text-gray-500 mt-2 font-medium">Kelola struktur organisasi dan prefix penomoran nota</p>
            </div>
            <button onclick="openCreateModal()"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-1">
                + TAMBAH DIVISI BARU
            </button>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest border-b">Detail Divisi
                        </th>
                        <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest border-b text-center">
                            Kode Prefix</th>
                        <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest border-b text-center">
                            Status</th>
                        <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest border-b text-right">
                            Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($divisis as $divisi)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="font-bold text-gray-900 text-lg group-hover:text-indigo-600 transition-colors">
                                    {{ $divisi->nama }}</div>
                                <div class="text-sm text-gray-500 mt-1 font-medium">{{ $divisi->deskripsi ?: '—' }}</div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span
                                    class="inline-block bg-white border-2 border-indigo-100 text-indigo-700 px-4 py-1.5 rounded-xl text-sm font-black tracking-wider shadow-sm">
                                    {{ $divisi->kode }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if ($divisi->aktif)
                                    <span
                                        class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-tighter">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> AKTIF
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 bg-gray-50 text-gray-400 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-tighter">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span> NON-AKTIF
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right space-x-1">
                                <button onclick="openEditModal({{ json_encode($divisi) }})"
                                    class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors title='Edit'">
                                    ✏️
                                </button>
                                @if (!$divisi->notas()->exists())
                                    <button onclick="openConfirmDeleteModal({{ json_encode($divisi) }})"
                                        class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors title='Hapus'">
                                        🗑️
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="text-gray-300 text-6xl mb-4">📂</div>
                                <p class="text-gray-400 font-medium italic">Belum ada data divisi yang tersimpan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Danger Zone -->
        <div class="mt-12 bg-white rounded-3xl border-2 border-rose-100 p-8 shadow-xl shadow-rose-50 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="bg-rose-50 w-16 h-16 rounded-2xl flex items-center justify-center text-3xl">☢️</div>
                <div>
                    <h3 class="text-rose-900 font-black text-xl tracking-tight">AREA BERBAHAYA (WIPEOUT)</h3>
                    <p class="text-rose-600/70 font-medium text-sm">Gunakan fitur ini untuk mereset seluruh data nota dan lampiran secara permanen.</p>
                </div>
            </div>
            <form action="{{ route('admin.system.reset') }}" method="POST" 
                onsubmit="return confirm('⚠️ KONFIRMASI RESET TOTAL!\n\nSemua nota, gambar lampiran, dan arsip akan dihapus permanen.\n\nLanjutkan?')">
                @csrf
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-8 py-4 rounded-2xl font-black shadow-lg shadow-rose-200 transition-all hover:scale-105 active:scale-95 leading-none uppercase tracking-wider text-xs">
                    Reset Semua Data
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL: CREATE DIVISI (MULTIPLE) -->
    <div id="createModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-indigo-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-5xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-fade-in-up">
            <div class="bg-indigo-600 px-8 py-6 flex justify-between items-center text-white">
                <div>
                    <h3 class="text-2xl font-black tracking-tight">➕ TAMBAH DIVISI BARU</h3>
                    <p class="text-indigo-100 text-sm font-medium mt-1">Anda bisa menambahkan beberapa divisi sekaligus</p>
                </div>
                <button onclick="closeCreateModal()" class="text-indigo-100 hover:text-white text-3xl">&times;</button>
            </div>
            
            <form action="{{ route('admin.divisi.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden" id="bulkForm">
                @csrf
                <div class="p-8 flex-1 overflow-y-auto">
                    <div id="rowContainer" class="space-y-4">
                        <!-- Initial Row -->
                        <div class="grid grid-cols-12 gap-4 items-end bg-gray-50 p-4 rounded-2xl border border-gray-100 transition-all hover:border-indigo-200 group relative">
                            <div class="col-span-4">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Nama Divisi</label>
                                <input type="text" name="items[0][nama]" required
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold" placeholder="E.g. Agrika Estate">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Kode Prefix</label>
                                <input type="text" name="items[0][kode]" required maxlength="10"
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-black uppercase text-center" placeholder="AGE">
                            </div>
                            <div class="col-span-5">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Deskripsi (Opsional)</label>
                                <input type="text" name="items[0][deskripsi]"
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-medium" placeholder="Keterangan singkat divisi...">
                            </div>
                            <div class="col-span-1 text-center">
                                <!-- No delete for first row -->
                            </div>
                        </div>
                    </div>

                    <button type="button" onclick="addRow()" class="mt-6 w-full py-4 border-2 border-dashed border-indigo-200 rounded-2xl text-indigo-500 font-black text-xs uppercase tracking-widest hover:bg-indigo-50 hover:border-indigo-400 transition-all">
                        ✨ Tambah Baris Input Baru
                    </button>
                </div>

                <div class="bg-gray-50 px-8 py-6 flex gap-4 border-t">
                    <button type="button" onclick="closeCreateModal()" class="flex-1 py-4 bg-white border border-gray-200 rounded-2xl font-bold text-gray-500 hover:bg-gray-100">Batal</button>
                    <button type="submit" class="flex-[2] py-4 bg-indigo-600 text-white rounded-2xl font-black shadow-lg shadow-indigo-100 hover:bg-indigo-700">SIMPAN SEMUA DATA</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDIT DIVISI (SINGLE) -->
    <div id="editModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-indigo-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden animate-fade-in-up">
            <div class="bg-amber-500 px-8 py-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-black tracking-tight">✏️ EDIT DATA DIVISI</h3>
                <button onclick="closeEditModal()" class="text-amber-100 hover:text-white text-3xl">&times;</button>
            </div>
            
            <form id="editForm" method="POST" class="p-8 space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama Divisi</label>
                    <input type="text" name="nama" id="edit_nama" required
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-bold text-lg">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Kode Prefix</label>
                        <input type="text" name="kode" id="edit_kode" required maxlength="10"
                            class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-black uppercase text-center text-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Status Input</label>
                        <select name="aktif" id="edit_aktif" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-bold appearance-none">
                            <option value="1">✅ AKTIF</option>
                            <option value="0">❌ NON-AKTIF</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Deskripsi</label>
                    <textarea name="deskripsi" id="edit_deskripsi" rows="3"
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-medium"></textarea>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeEditModal()" class="flex-1 py-4 bg-gray-100 rounded-2xl font-bold text-gray-500">Batal</button>
                    <button type="submit" class="flex-[2] py-4 bg-amber-500 text-white rounded-2xl font-black shadow-lg shadow-amber-100">UPDATE DATA</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: DELETE CONFIRMATION -->
    <div id="deleteModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-rose-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden animate-fade-in-up border-2 border-rose-50">
            <div class="bg-rose-600 px-6 py-6 flex items-center gap-4 text-white">
                <span class="text-4xl">⚠️</span>
                <div>
                    <h3 class="text-xl font-black leading-tight uppercase tracking-tight">Hapus Divisi?</h3>
                    <p class="text-rose-100 text-xs font-medium">Tindakan ini tidak bisa dibatalkan</p>
                </div>
            </div>
            
            <div class="p-8">
                <p class="text-gray-700 font-medium mb-6">Apakah Anda yakin ingin menghapus divisi ini dari database? Data yang terhubung mungkin akan terdampak.</p>
                
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex items-center gap-4">
                    <div class="bg-white w-12 h-12 rounded-xl flex items-center justify-center font-black text-gray-400 border border-gray-200" id="del_kode">—</div>
                    <div>
                        <div class="font-black text-gray-900 leading-tight" id="del_nama">—</div>
                        <div class="text-[10px] text-gray-400 uppercase font-black tracking-widest mt-1">Target Penghapusan</div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-8 py-6 flex gap-3 border-t">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 py-4 bg-white border border-gray-200 rounded-2xl font-bold text-gray-500">Batal</button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-4 bg-rose-600 text-white rounded-2xl font-black shadow-lg shadow-rose-100">YA, HAPUS</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let rowCount = 1;

        function addRow() {
            const container = document.getElementById('rowContainer');
            const newRow = document.createElement('div');
            newRow.className = 'grid grid-cols-12 gap-4 items-end bg-gray-50 p-4 rounded-2xl border border-gray-100 transition-all hover:border-indigo-200 group relative animate-fade-in-up';
            newRow.innerHTML = `
                <div class="col-span-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Nama Divisi</label>
                    <input type="text" name="items[${rowCount}][nama]" required
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold" placeholder="E.g. Agrika Estate">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Kode Prefix</label>
                    <input type="text" name="items[${rowCount}][kode]" required maxlength="10"
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-black uppercase text-center" placeholder="AGE">
                </div>
                <div class="col-span-5">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Deskripsi</label>
                    <input type="text" name="items[${rowCount}][deskripsi]"
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-medium" placeholder="...">
                </div>
                <div class="col-span-1 text-center">
                    <button type="button" onclick="removeRow(this)" class="p-2 text-rose-300 hover:text-rose-500 transition-colors text-xl">✕</button>
                </div>
            `;
            container.appendChild(newRow);
            rowCount++;
        }

        function removeRow(btn) {
            btn.closest('.grid').remove();
        }

        // Modals Management
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            rowCount = 1;
            document.getElementById('rowContainer').innerHTML = '';
            addRow(); // Initial row
        }
        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openEditModal(divisi) {
            const form = document.getElementById('editForm');
            form.action = `/admin/divisi/${divisi.id}`;
            document.getElementById('edit_nama').value = divisi.nama;
            document.getElementById('edit_kode').value = divisi.kode;
            document.getElementById('edit_deskripsi').value = divisi.deskripsi || '';
            document.getElementById('edit_aktif').value = divisi.aktif ? "1" : "0";
            
            document.getElementById('editModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openConfirmDeleteModal(divisi) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin/divisi/${divisi.id}`;
            document.getElementById('del_nama').textContent = divisi.nama;
            document.getElementById('del_kode').textContent = divisi.kode;
            
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close on ESC
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeCreateModal();
                closeEditModal();
                closeDeleteModal();
            }
        });
    </script>

    <style>
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.3s ease-out;
        }
    </style>
@endsection
