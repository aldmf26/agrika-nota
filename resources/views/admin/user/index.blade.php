@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight uppercase">👥 Manajemen User</h1>
                <p class="text-gray-500 mt-2 font-medium">Kelola hak akses dan akun pengguna sistem</p>
            </div>
            <button onclick="openCreateUserModal()"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-1">
                + TAMBAH USER BARU
            </button>
        </div>

        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest border-b">Nama /
                            Email</th>
                        <th
                            class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest border-b text-center">
                            Role / Hak Akses</th>
                        <th
                            class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest border-b text-center">
                            Terdaftar</th>
                        <th
                            class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest border-b text-right group-hover:block">
                            Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($users as $user)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="font-bold text-gray-900 text-lg group-hover:text-indigo-600 transition-colors">
                                    {{ $user->name }}</div>
                                <div class="text-sm text-gray-500 font-medium">{{ $user->email }}</div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @foreach ($user->roles as $role)
                                    <span
                                        class="inline-block bg-white border-2 border-indigo-100 text-indigo-700 px-3 py-1 rounded-xl text-xs font-black tracking-wider uppercase">
                                        {{ str_replace('_', ' ', $role->name) }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-8 py-6 text-center text-gray-500 text-sm font-medium">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-8 py-6 text-right space-x-1">
                                <button onclick="openEditUserModal({{ json_encode($user->load('roles')) }})"
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
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="text-gray-300 text-6xl mb-4">👤</div>
                                <p class="text-gray-400 font-medium italic">Belum ada user tambahan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: CREATE USER -->
    <div id="createUserModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-indigo-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden animate-fade-in-up">
            <div class="bg-indigo-600 px-8 py-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-black tracking-tight">👤 TAMBAH USER BARU</h3>
                <button onclick="closeCreateUserModal()" class="text-indigo-100 hover:text-white text-3xl">&times;</button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama
                        Lengkap</label>
                    <input type="text" name="name" required
                        class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Email
                        Address</label>
                    <input type="email" name="email" required
                        class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Role /
                            Level</label>
                        <select name="role" required
                            class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ strtoupper(str_replace('_', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeCreateUserModal()"
                        class="flex-1 py-4 bg-gray-100 rounded-2xl font-bold text-gray-500">Batal</button>
                    <button type="submit"
                        class="flex-[2] py-4 bg-indigo-600 text-white rounded-2xl font-black shadow-lg shadow-indigo-100 uppercase tracking-wider text-xs">Simpan
                        Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDIT USER -->
    <div id="editUserModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-indigo-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden animate-fade-in-up">
            <div class="bg-amber-500 px-8 py-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-black tracking-tight uppercase">✏️ Edit User & Password</h3>
                <button onclick="closeEditUserModal()" class="text-amber-100 hover:text-white text-3xl">&times;</button>
            </div>

            <form id="editUserForm" method="POST" class="p-8 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama
                        Lengkap</label>
                    <input type="text" name="name" id="edit_user_name" required
                        class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Email
                        Address</label>
                    <input type="email" name="email" id="edit_user_email" required
                        class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-bold">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Role /
                            Level</label>
                        <select name="role" id="edit_user_role" required
                            class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-bold">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ strtoupper(str_replace('_', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 text-rose-500">Ganti
                            Password</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tak diubah"
                            class="w-full px-5 py-3 bg-rose-50 border border-rose-100 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-bold text-sm">
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeEditUserModal()"
                        class="flex-1 py-4 bg-gray-100 rounded-2xl font-bold text-gray-500">Batal</button>
                    <button type="submit"
                        class="flex-[2] py-4 bg-amber-500 text-white rounded-2xl font-black shadow-lg shadow-amber-100 uppercase tracking-wider text-xs">Update
                        Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: DELETE USER -->
    <div id="deleteUserModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-rose-900/40 backdrop-blur-sm p-4">
        <div
            class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden animate-fade-in-up border-2 border-rose-50">
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
                        class="w-full py-4 bg-rose-600 text-white rounded-2xl font-black shadow-lg shadow-rose-100 uppercase text-xs">Ya,
                        Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCreateUserModal() {
            document.getElementById('createUserModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
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