<!-- Navigation - Auth -->
<nav class="bg-white shadow border-b border-gray-100 sticky top-0 z-40 transition-colors"
    style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding: 20px;">
        <div class="flex justify-between items-center h-16">

            <!-- Logo + Branding -->
            <div class="flex items-center gap-8">
                <a href="{{ auth()->check() ? route('dashboard') : '/' }}" class="text-xl font-bold"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    📝 Agrika Nota
                </a>

                @if (auth()->check())
                    <div class="hidden md:flex gap-2">
                        <a href="{{ route('nota.index') }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('nota.*') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            📋 Nota
                        </a>
                        @if (auth()->user()->hasRole('super_admin'))
                            <div class="h-6 w-px bg-gray-200 mx-2"></div>
                            <a href="{{ route('admin.users.index') }}"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                👥 User
                            </a>
                            <a href="{{ route('admin.divisi.index') }}"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.divisi.*') || request()->routeIs('admin.system.*') ? 'bg-red-50 text-red-700 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                🛠️ Sistem
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- User Menu -->
            <div class="flex items-center gap-4">
                @if (auth()->check())
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-900 leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-green-600 font-medium">
                            @foreach (auth()->user()->getRoleNames() as $role)
                                <span class="capitalize">{{ $role }}</span>
                            @endforeach
                        </p>
                    </div>

                    <div
                        class="h-8 w-8 rounded-full bg-gradient-to-r from-green-400 to-green-600 text-white flex items-center justify-center font-bold shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <div style="width: 1px; height: 1.5rem; background-color: #e5e7eb; margin: 0 0.5rem;"></div>

                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit"
                            class="text-gray-500 hover:text-red-600 text-sm font-medium px-2 py-1 rounded transition-colors"
                            style="cursor: pointer; background: transparent; border: none; font-family: inherit;">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="text-gray-600 hover:text-gray-900 text-sm font-medium px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                        Login
                    </a>
                @endif
            </div>
        </div>
    </div>
</nav>