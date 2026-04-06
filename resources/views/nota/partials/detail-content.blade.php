<!-- Info Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <x-info-card label="Nominal" :value="$nota->nominal_formatted" icon="💰" />
    <x-info-card label="Tipe Nota" :value="ucfirst(str_replace('_', ' ', $nota->tipe))" icon="📌" />
    <x-info-card label="Dari Admin" :value="$nota->user->name" icon="👤" />
</div>

<!-- Main Content -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Left: Nota Details -->
    <div class="col-span-1 md:col-span-2 space-y-4">
        <!-- Basic Info -->
        <x-card title="Informasi Umum">
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-600">Nomor Nota:</dt>
                    <dd class="font-medium text-gray-900">{{ $nota->nomor_nota ?? '(Digital)' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Tanggal:</dt>
                    <dd class="font-medium text-gray-900">{{ $nota->tanggal_nota->format('d/m/Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Divisi Utama:</dt>
                    <dd class="font-medium text-gray-900">{{ $nota->divisi->nama ?? '-' }}</dd>
                </div>
                <div class="flex flex-col gap-1">
                    <dt class="text-gray-600">Keterangan:</dt>
                    <dd class="font-medium text-gray-900 bg-gray-50 p-2 rounded border">{{ $nota->keterangan }}</dd>
                </div>
            </dl>
        </x-card>

        <!-- Tipe-Specific Details -->
        @if ($nota->tipe === 'split')
            <x-card title="Split Tagihan">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-2 text-left">Divisi</th>
                                <th class="px-4 py-2 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($nota->items as $item)
                                <tr>
                                    <td class="px-4 py-2">{{ $item->divisi->nama }}</td>
                                    <td class="px-4 py-2 text-right font-semibold">{{ $item->nominalFormatted() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        @endif

        @if ($nota->tipe === 'revenue_sharing')
            <x-card title="Revenue Sharing">
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Base Amount:</dt>
                        <dd class="font-medium">Rp {{ number_format($nota->base_amount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Persentase:</dt>
                        <dd class="font-medium">{{ $nota->persentase }}%</dd>
                    </div>
                    <div class="border-t pt-3 flex justify-between bg-green-50 p-3 rounded">
                        <dt class="text-gray-900 font-semibold">Total Nominal:</dt>
                        <dd class="font-bold text-green-700">{{ $nota->nominal_formatted }}</dd>
                    </div>
                </dl>
            </x-card>
        @endif

        @if ($nota->tipe === 'kelebihan_bayar')
            <x-card title="Kelebihan Bayar (Deposit)">
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Nominal Seharusnya:</dt>
                        <dd class="font-medium">Rp {{ number_format($nota->nominal_seharusnya, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Nominal Dibayar:</dt>
                        <dd class="font-medium">Rp {{ number_format($nota->nominal_dibayar, 0, ',', '.') }}</dd>
                    </div>
                    <div class="border-t pt-3 flex justify-between bg-yellow-50 p-3 rounded">
                        <dt class="text-gray-900 font-semibold">Deposit:</dt>
                        <dd class="font-bold text-yellow-700">+ Rp {{ number_format($nota->selisih, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>
            </x-card>
        @endif

        <!-- Attachments -->
        @if ($nota->attachments->count() > 0)
            <x-card title="Lampiran Foto" :subtitle="$nota->attachments->count() . ' file'">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach ($nota->attachments as $attachment)
                        <div>
                            <img src="{{ Storage::url($attachment->file_path) }}" alt="Attachment"
                                class="w-full h-32 md:h-48 object-cover rounded-lg border border-gray-200 cursor-pointer hover:shadow-lg transition-shadow"
                                onclick="openImageModal(this.src, '{{ $attachment->file_name }}')">
                            <p class="text-[10px] text-gray-500 mt-1 truncate">{{ $attachment->file_name }}</p>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endif

        <!-- Approval Info -->
        @if ($nota->status === 'approved')
            <x-card title="✓ Approved By" class="bg-green-50 border-green-200">
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Approver:</dt>
                        <dd class="font-medium text-green-800">{{ $nota->approver->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Tanggal:</dt>
                        <dd class="font-medium">{{ $nota->approved_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if ($nota->catatan_approver)
                        <div class="pt-2 border-t border-green-100 flex flex-col gap-1">
                            <dt class="text-gray-600">Catatan:</dt>
                            <dd class="font-medium italic text-green-900">"{{ $nota->catatan_approver }}"</dd>
                        </div>
                    @endif
                </dl>
            </x-card>
        @endif

        @if ($nota->status === 'rejected')
            <x-card title="✗ Rejected By" class="bg-red-50 border-red-200">
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Approver:</dt>
                        <dd class="font-medium text-red-800">{{ $nota->approver->name }}</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt class="text-gray-600">Alasan:</dt>
                        <dd class="font-medium bg-white p-2 rounded border border-red-100 text-red-900 text-sm">
                            {{ $nota->catatan_approver }}
                        </dd>
                    </div>
                    <div class="flex justify-between text-xs text-red-400">
                        <dt>Tanggal:</dt>
                        <dd>{{ $nota->rejected_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </x-card>
        @endif
    </div>
    
    @yield('actions-sidebar')
</div>
