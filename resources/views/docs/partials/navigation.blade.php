<nav aria-label="Navigasi panduan" class="mb-8 overflow-x-auto border-b border-slate-200">
    <div class="flex min-w-max gap-1">
        <a href="{{ route('docs.index') }}" class="border-b-2 px-4 py-3 text-sm font-bold {{ request()->routeIs('docs.index') ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-900' }}">Ringkasan</a>
        <a href="{{ route('docs.user-guide') }}" class="border-b-2 px-4 py-3 text-sm font-bold {{ request()->routeIs('docs.user-guide') ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-900' }}">Cara Menggunakan</a>
        <a href="{{ route('docs.workflow') }}" class="border-b-2 px-4 py-3 text-sm font-bold {{ request()->routeIs('docs.workflow') ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-900' }}">Alur dan Peran</a>
    </div>
</nav>
