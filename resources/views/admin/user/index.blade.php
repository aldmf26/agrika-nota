@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight uppercase">👥 Manajemen User</h1>
                <p class="text-gray-500 mt-2 font-medium">Kelola hak akses, divisi, dan akun pengguna sistem</p>
            </div>
            <button onclick="openCreateUserModal()"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-1">
                + TAMBAH USER BARU
            </button>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" data-debounce-search
            class="filter-toolbar mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <label class="flex items-center gap-2 whitespace-nowrap text-sm font-medium text-slate-600">
                Tampilkan
                <select name="per_page" onchange="this.form.submit()" class="filter-control-compact w-20">
                    @foreach ([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected($users->perPage() === $size)>{{ $size }}</option>
                    @endforeach
                </select>
                data
            </label>
            <div class="flex w-full gap-2 sm:max-w-md">
                <input type="search" name="search" value="{{ request('search') }}" data-search-input placeholder="Cari nama, email, atau role..."
                    autocomplete="off" class="filter-control min-w-0 flex-1">
                @if (request('search'))
                    <a href="{{ route('admin.users.index', ['per_page' => $users->perPage()]) }}" class="filter-button-secondary">Reset</a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto rounded-3xl border border-gray-100 bg-white shadow-xl shadow-gray-200/50">
            <table class="w-full min-w-[760px] text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest border-b">Nama / Email</th>
                        <th class="px-6 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest border-b text-center">Role / Level</th>
                        <th class="px-6 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest border-b text-center">Akses Divisi</th>
                        <th class="px-6 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest border-b text-center">Terdaftar</th>
                        <th class="px-6 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest border-b text-right">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($users as $user)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="font-bold text-gray-900 text-base group-hover:text-indigo-600 transition-colors">
                                    {{ $user->name }}</div>
                                <div class="text-sm text-gray-500 font-medium">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                @foreach ($user->roles as $role)
                                    <span class="inline-block bg-white border border-indigo-200 text-indigo-700 px-3 py-1 rounded-xl text-xs font-black tracking-wider uppercase">
                                        {{ str_replace('_', ' ', $role->name) }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if ($user->hasRole('super_admin') || $user->hasRole('approver') || $user->all_divisi)
                                    <span class="inline-block bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-xl text-xs font-bold">
                                        🌐 Semua Divisi
                                    </span>
                                @else
                                    <div class="flex flex-wrap justify-center gap-1">
                                        @forelse ($user->divisis as $d)
                                            <span class="inline-block bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-0.5 rounded-lg text-xs font-medium">
                                                {{ $d->nama }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400 italic">Belum dipilih</span>
                                        @endforelse
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center text-gray-500 text-sm font-medium">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-5 text-right space-x-1">
                                <button onclick="openEditUserModal({{ json_encode($user->load('roles', 'divisis')) }})"
                                    class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                    ✏️
                                </button>
                                @if ($user->id !== auth()->id())
                                    <button onclick="openConfirmDeleteUserModal({{ json_encode($user) }})"
                                        class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                        🗑️
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="text-gray-300 text-6xl mb-4">👤</div>
                                <p class="text-gray-400 font-medium italic">Belum ada user tambahan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->total() > 0)
            <div class="mt-4 flex flex-col gap-3 text-sm text-gray-500 sm:flex-row sm:items-center sm:justify-between">
                <p>Menampilkan {{ $users->firstItem() }}-{{ $users->lastItem() }} dari {{ $users->total() }} data</p>
                {{ $users->links() }}
            </div>
        @endif

        <script>
            (() => {
                const form = document.querySelector('[data-debounce-search]');
                const input = form?.querySelector('[data-search-input]');
                let timer;
                input?.addEventListener('input', () => {
                    window.clearTimeout(timer);
                    timer = window.setTimeout(() => form.requestSubmit(), 450);
                });
            })();
        </script>
    </div>

    <!-- MODAL: CREATE USER -->
    <div id="createUserModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-indigo-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-fade-in-up">
            <div class="bg-indigo-600 px-8 py-6 text-white flex justify-between items-center shrink-0">
                <h3 class="text-xl font-black tracking-tight">👤 TAMBAH USER BARU</h3>
                <button onclick="closeCreateUserModal()" class="text-indigo-100 hover:text-white text-3xl">&times;</button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="p-8 space-y-5 overflow-y-auto flex-1">
                @csrf
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama Lengkap</label>
                    <input type="text" name="name" required
                        class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Email Address</label>
                    <input type="email" name="email" required
                        class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Role / Level</label>
                        <select name="role" required
                            class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ strtoupper(str_replace('_', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                    </div>
                </div>

                <!-- Hak Akses Divisi -->
                <div class="border-t border-gray-100 pt-4">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Hak Akses Divisi</label>
                    
                    <label class="flex items-center gap-3 p-3 bg-indigo-50/60 rounded-xl border border-indigo-100 cursor-pointer mb-3">
                        <input type="checkbox" name="all_divisi" value="1" checked id="create_all_divisi" onchange="toggleDivisiPicker('create')" class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                        <span class="font-bold text-gray-800 text-sm">Akses Semua Divisi (Global Admin)</span>
                    </label>

                    <div id="create_divisi_picker" class="hidden space-y-2 max-h-48 overflow-y-auto p-3 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-500 font-medium mb-2">Pilih divisi yang dapat diakses user ini:</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($divisis as $divisi)
                                <label class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 text-xs font-semibold text-gray-700 cursor-pointer hover:bg-indigo-50/50">
                                    <input type="checkbox" name="divisi_ids[]" value="{{ $divisi->id }}" class="w-4 h-4 text-indigo-600 rounded">
                                    <span>{{ $divisi->nama }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeCreateUserModal()"
                        class="flex-1 py-4 bg-gray-100 rounded-2xl font-bold text-gray-500">Batal</button>
                    <button type="submit"
                        class="flex-[2] py-4 bg-indigo-600 text-white rounded-2xl font-black shadow-lg shadow-indigo-100 uppercase tracking-wider text-xs">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDIT USER -->
    <div id="editUserModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-indigo-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-fade-in-up">
            <div class="bg-amber-500 px-8 py-6 text-white flex justify-between items-center shrink-0">
                <h3 class="text-xl font-black tracking-tight uppercase">✏️ Edit User & Hak Akses</h3>
                <button onclick="closeEditUserModal()" class="text-amber-100 hover:text-white text-3xl">&times;</button>
            </div>

            <form id="editUserForm" method="POST" class="p-8 space-y-5 overflow-y-auto flex-1">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="edit_user_name" required
                        class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Email Address</label>
                    <input type="email" name="email" id="edit_user_email" required
                        class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-bold">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Role / Level</label>
                        <select name="role" id="edit_user_role" required
                            class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-bold">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ strtoupper(str_replace('_', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 text-rose-500">Ganti Password</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tak diubah"
                            class="w-full px-5 py-3 bg-rose-50 border border-rose-100 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-bold text-sm">
                    </div>
                </div>

                <!-- Hak Akses Divisi Edit -->
                <div class="border-t border-gray-100 pt-4">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Hak Akses Divisi</label>
                    
                    <label class="flex items-center gap-3 p-3 bg-amber-50/60 rounded-xl border border-amber-100 cursor-pointer mb-3">
                        <input type="checkbox" name="all_divisi" value="1" id="edit_all_divisi" onchange="toggleDivisiPicker('edit')" class="w-5 h-5 text-amber-600 rounded focus:ring-amber-500">
                        <span class="font-bold text-gray-800 text-sm">Akses Semua Divisi (Global Admin)</span>
                    </label>

                    <div id="edit_divisi_picker" class="hidden space-y-2 max-h-48 overflow-y-auto p-3 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-500 font-medium mb-2">Pilih divisi yang dapat diakses user ini:</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($divisis as $divisi)
                                <label class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 text-xs font-semibold text-gray-700 cursor-pointer hover:bg-amber-50/50">
                                    <input type="checkbox" name="divisi_ids[]" value="{{ $divisi->id }}" class="edit_divisi_checkbox w-4 h-4 text-amber-600 rounded" data-divisi-id="{{ $divisi->id }}">
                                    <span>{{ $divisi->nama }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeEditUserModal()"
                        class="flex-1 py-4 bg-gray-100 rounded-2xl font-bold text-gray-500">Batal</button>
                    <button type="submit"
                        class="flex-[2] py-4 bg-amber-500 text-white rounded-2xl font-black shadow-lg shadow-amber-100 uppercase tracking-wider text-xs">Update Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: DELETE USER -->
    <div id="deleteUserModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-rose-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden animate-fade-in-up border-2 border-rose-50">
            <div class="bg-rose-600 px-6 py-6 flex items-center gap-4 text-white">
                <span class="text-4xl">⚠️</span>
                <div>
                    <h3 class="text-xl font-black leading-tight uppercase tracking-tight">Hapus Akun?</h3>
                    <p class="text-rose-100 text-xs font-medium">Pengguna ini tak akan bisa login lagi</p>
                </div>
            </div>

            <div class="p-8">
                <p class="text-gray-700 font-medium mb-6">Apakah Anda yakin ingin menghapus user ini secara permanen?</p>
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                    <div class="font-black text-gray-900" id="del_user_name">—</div>
                    <div class="text-xs text-gray-400 font-medium" id="del_user_email">—</div>
                </div>
            </div>

            <div class="bg-gray-50 px-8 py-6 flex gap-3 border-t">
                <button type="button" onclick="closeDeleteUserModal()"
                    class="flex-1 py-4 bg-white border border-gray-200 rounded-2xl font-bold text-gray-500">Batal</button>
                <form id="deleteUserForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full py-4 bg-rose-600 text-white rounded-2xl font-black shadow-lg shadow-rose-100 uppercase text-xs">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleDivisiPicker(prefix) {
            const allCheckbox = document.getElementById(`${prefix}_all_divisi`);
            const picker = document.getElementById(`${prefix}_divisi_picker`);
            if (allCheckbox.checked) {
                picker.classList.add('hidden');
            } else {
                picker.classList.remove('hidden');
            }
        }

        function openCreateUserModal() {
            document.getElementById('createUserModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            document.getElementById('create_all_divisi').checked = true;
            toggleDivisiPicker('create');
        }
        function closeCreateUserModal() {
            document.getElementById('createUserModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openEditUserModal(user) {
            document.getElementById('editUserForm').action = `/admin/users/${user.id}`;
            document.getElementById('edit_user_name').value = user.name;
            document.getElementById('edit_user_email').value = user.email;
            document.getElementById('edit_user_role').value = user.roles[0].name;

            const allDivisiCheckbox = document.getElementById('edit_all_divisi');
            allDivisiCheckbox.checked = Boolean(user.all_divisi);

            const userDivisiIds = (user.divisis || []).map(d => String(d.id));
            document.querySelectorAll('.edit_divisi_checkbox').forEach(cb => {
                cb.checked = userDivisiIds.includes(String(cb.dataset.divisiId));
            });

            toggleDivisiPicker('edit');

            document.getElementById('editUserModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeEditUserModal() {
            document.getElementById('editUserModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openConfirmDeleteUserModal(user) {
            document.getElementById('deleteUserForm').action = `/admin/users/${user.id}`;
            document.getElementById('del_user_name').textContent = user.name;
            document.getElementById('del_user_email').textContent = user.email;

            document.getElementById('deleteUserModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeDeleteUserModal() {
            document.getElementById('deleteUserModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>

    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.3s ease-out;
        }
    </style>
@endsection
