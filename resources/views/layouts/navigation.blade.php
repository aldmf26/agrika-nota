<nav class="sticky top-0 z-40 border-b border-gray-200 bg-white/95 shadow-sm backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex min-w-0 items-center gap-8">
                <div class="flex min-w-0 items-center gap-2">
                    @auth
                        <button type="button" id="globalBackButton"
                            class="flex h-10 w-10 shrink-0 items-center justify-center text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                            title="Kembali">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="sr-only">Kembali ke halaman sebelumnya</span>
                        </button>
                    @endauth
                    <a href="{{ auth()->check() ? route('dashboard') : '/' }}"
                        class="shrink-0 text-lg font-bold text-green-600 sm:text-xl">
                        📝 Agrika Nota
                    </a>
                </div>

                @auth
                    <div class="hidden items-center gap-1 md:flex">
                        <a href="{{ route('nota.index') }}"
                            class="px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('nota.*') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            Nota
                        </a>
                        @if (auth()->user()->can('weekly-review.view'))
                            <a href="{{ route('weekly-reviews.index') }}"
                                class="px-3 py-2 text-sm font-medium {{ request()->routeIs('weekly-reviews.*') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50' }}">Pemeriksaan
                                Mingguan</a>
                        @endif
                        @if (auth()->user()->hasRole('super_admin'))
                            <a href="{{ route('admin.reports.index') }}"
                                class="px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-orange-50 font-bold text-orange-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                Laporan
                            </a>
                        @endif
                        @if (auth()->user()->hasRole('super_admin'))
                            <span class="mx-2 h-6 w-px bg-gray-200"></span>
                            <a href="{{ route('admin.divisi.index') }}"
                                class="px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.divisi.*') ? 'bg-indigo-50 font-bold text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                Divisi
                            </a>
                            <a href="{{ route('admin.settings.index') }}"
                                class="px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.system.*') ? 'bg-red-50 font-bold text-red-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                Pengaturan
                            </a>
                            <a href="{{ route('admin.users.index') }}"
                                class="px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 font-bold text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                User
                            </a>
                        @endif
                    </div>
                @endauth
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                @auth
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-bold leading-tight text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs font-medium text-green-600">
                            {{ auth()->user()->getRoleNames()->map(fn($role) => str_replace('_', ' ', $role))->join(', ') }}
                        </p>
                    </div>
                    <div
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-600 font-bold text-white shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}"
                        class="hidden border-l border-gray-200 pl-3 md:block">
                        @csrf
                        <button type="submit"
                            class="cursor-pointer px-2 py-1 text-sm font-medium text-gray-500 hover:text-red-600">
                            Logout
                        </button>
                    </form>
                    <button type="button" id="mobileMenuButton" aria-controls="mobileMenu" aria-expanded="false"
                        class="flex h-10 w-10 items-center justify-center text-gray-700 hover:bg-gray-100 md:hidden"
                        title="Buka menu">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span class="sr-only">Buka menu navigasi</span>
                    </button>
                @else
                    <a href="{{ route('login') }}"
                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                        Login
                    </a>
                @endauth
            </div>
        </div>

        @auth
            <div id="mobileMenu" class="hidden border-t border-gray-100 py-3 md:hidden">
                <div class="grid gap-1">
                    <a href="{{ route('dashboard') }}"
                        class="px-3 py-3 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('nota.index') }}"
                        class="px-3 py-3 text-sm font-medium {{ request()->routeIs('nota.*') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        Nota
                    </a>
                    @if (auth()->user()->can('weekly-review.view'))
                        <a href="{{ route('weekly-reviews.index') }}"
                            class="px-3 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">Pemeriksaan Mingguan</a>
                    @endif
                    @if (auth()->user()->hasRole('super_admin'))
                        <a href="{{ route('admin.reports.index') }}"
                            class="px-3 py-3 text-sm font-medium {{ request()->routeIs('admin.reports.*') ? 'bg-orange-50 text-orange-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            Laporan
                        </a>
                    @endif
                    @if (auth()->user()->hasRole('super_admin'))
                        <a href="{{ route('admin.divisi.index') }}"
                            class="px-3 py-3 text-sm font-medium {{ request()->routeIs('admin.divisi.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">Divisi</a>
                        <a href="{{ route('admin.settings.index') }}"
                            class="px-3 py-3 text-sm font-medium {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.system.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-gray-50' }}">Pengaturan</a>
                        <a href="{{ route('admin.users.index') }}"
                            class="px-3 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">User</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-gray-100 pt-2">
                        @csrf
                        <button type="submit"
                            class="w-full px-3 py-3 text-left text-sm font-medium text-red-600 hover:bg-red-50">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>

@auth
    <script>
        document.getElementById('globalBackButton')?.addEventListener('click', function() {
            const hasInternalReferrer = document.referrer && new URL(document.referrer).origin === window.location
                .origin;

            if (hasInternalReferrer) {
                window.history.back();
                return;
            }

            window.location.href = @json(route('dashboard'));
        });

        document.getElementById('mobileMenuButton')?.addEventListener('click', function() {
            const menu = document.getElementById('mobileMenu');
            const isOpen = !menu.classList.contains('hidden');

            menu.classList.toggle('hidden');
            this.setAttribute('aria-expanded', String(!isOpen));
        });
    </script>
@endauth
