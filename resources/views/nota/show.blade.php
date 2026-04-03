@extends('layouts.app')

@section('title', 'Detail Nota')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">📝 Detail Nota</h1>
                <p class="text-gray-600 mt-1">{{ $nota->tanggal_nota->format('d F Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <x-status-badge :status="$nota->status" />
                @if($nota->is_printed)
                    <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-black uppercase tracking-tighter border border-amber-100 flex items-center gap-1" title="Dicetak oleh {{ $nota->printer->name ?? 'System' }} pada {{ $nota->printed_at->format('d/m/Y H:i') }}">
                        <span class="animate-pulse">●</span> TERDAFTAR CETAK
                    </span>
                @endif
                <a href="{{ route('nota.print', $nota) }}" target="_blank"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-md active:scale-95 flex items-center gap-2">
                    🖨️ CETAK NOTA
                </a>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <x-info-card label="Nominal" :value="$nota->nominal_formatted" icon="💰" />
            <x-info-card label="Tipe Nota" :value="ucfirst(str_replace('_', ' ', $nota->tipe))" icon="📌" />
            <x-info-card label="Dari Admin" :value="$nota->user->name" icon="👤" />
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-3 gap-6">
            <!-- Left: Nota Details -->
            <div class="col-span-2 space-y-4">
                <!-- Basic Info -->
                <x-card title="Informasi Umum">
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Nomor Nota:</dt>
                            <dd class="font-medium">{{ $nota->nomor_nota ?? '(Digital)' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Tanggal:</dt>
                            <dd class="font-medium">{{ $nota->tanggal_nota->format('d/m/Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Divisi Utama:</dt>
                            <dd class="font-medium">{{ $nota->divisi->nama ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Keterangan:</dt>
                            <dd class="font-medium">{{ $nota->keterangan }}</dd>
                        </div>
                    </dl>
                </x-card>

                <!-- Tipe-Specific Details -->
                @if ($nota->tipe === 'split')
                    <x-card title="Split Tagihan">
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
                        <div class="grid grid-cols-2 gap-4">
                            @foreach ($nota->attachments as $attachment)
                                <div>
                                    <img src="{{ Storage::disk('public')->url($attachment->file_path) }}" alt="Attachment"
                                        class="w-full h-48 object-cover rounded-lg border border-gray-200 cursor-pointer hover:shadow-lg transition-shadow"
                                        onclick="openImageModal(this.src, '{{ $attachment->file_name }}')">
                                    <p class="text-xs text-gray-600 mt-1">{{ $attachment->file_name }}</p>
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
                                <dd class="font-medium">{{ $nota->approver->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Tanggal:</dt>
                                <dd class="font-medium">{{ $nota->approved_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            @if ($nota->catatan_approver)
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Catatan:</dt>
                                    <dd class="font-medium">{{ $nota->catatan_approver }}</dd>
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
                                <dd class="font-medium">{{ $nota->approver->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Alasan:</dt>
                                <dd class="font-medium">{{ $nota->catatan_approver }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Tanggal:</dt>
                                <dd class="font-medium">{{ $nota->rejected_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </x-card>
                @endif
            </div>

            <!-- Right: Actions -->
            <div>
                <x-card title="Aksi">
                    <div class="space-y-2">
                        @if ($nota->status === 'rejected' && auth()->user()->can('update', $nota))
                            <!-- Admin dapat edit nota yang rejected (untuk revisi) -->
                            <a href="{{ route('nota.edit', $nota) }}"
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors block text-center">
                                ✏️ Edit & Resubmit
                            </a>
                        @endif

                        @if ($nota->status === 'pending' && auth()->user()->can('approve', $nota))
                            <!-- Approver dapat approve -->
                            <form action="{{ route('nota.approve', $nota) }}" method="POST" class="mb-2">
                                @csrf
                                <textarea name="catatan_approver" class="w-full text-xs p-2 border border-gray-300 rounded mb-2"
                                    placeholder="Optional: catatan"></textarea>
                                <button type="submit"
                                    class="w-full bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                                    ✓ Approve
                                </button>
                            </form>

                            <!-- Approver dapat reject -->
                            <button
                                class="w-full bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors"
                                onclick="toggleRejectForm()">
                                ✗ Reject
                            </button>

                            <form id="rejectForm" action="{{ route('nota.reject', $nota) }}" method="POST"
                                class="hidden mt-2" onsubmit="return validateRejectMessage()">
                                @csrf
                                <textarea name="catatan_approver" id="rejectMessage" class="w-full text-xs p-2 border border-red-300 rounded mb-2"
                                    placeholder="Alasan penolakan (minimal 10 karakter)" required></textarea>
                                <p id="charCount" class="text-xs text-gray-600 mb-2">0 karakter</p>
                                <button type="submit"
                                    class="w-full bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">
                                    Konfirm Reject
                                </button>
                                <button type="button"
                                    class="w-full mt-1 bg-gray-300 text-gray-900 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-400"
                                    onclick="toggleRejectForm()">
                                    Batal
                                </button>
                            </form>
                        @endif

                        @if (in_array($nota->status, ['approved', 'rejected']) && auth()->user()->can('void', $nota))
                            <!-- Approver dapat void -->
                            <form action="{{ route('nota.void', $nota) }}" method="POST"
                                onsubmit="return confirm('Yakin mau batalkan nota ini?')">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-slate-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-700 transition-colors">
                                    ⊘ Void (Batalkan)
                                </button>
                            </form>
                        @endif

                        @if (in_array($nota->status, ['pending', 'rejected']) && auth()->user()->can('delete', $nota))
                            <!-- Delete nota (force delete + archive) -->
                            <button type="button"
                                class="w-full bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors"
                                onclick="openDeleteModal()">
                                🗑️ Hapus Nota
                            </button>
                        @endif

                        <!-- Back to list -->
                        <a href="{{ route('nota.index') }}"
                            class="block w-full bg-gray-200 text-gray-900 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors text-center mt-4">
                            ← Kembali
                        </a>
                    </div>
                </x-card>
            </div>
        </div>
    </div>

    <!-- Image Modal with Zoom -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-75 p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900" id="modalFileName">File Name</h3>
                <button type="button" onclick="closeImageModal()"
                    class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
            </div>

            <!-- Modal Body with Image -->
            <div class="flex-1 overflow-auto flex items-center justify-center bg-gray-50 relative">
                <img id="modalImage" src="" alt="Full size image" class="max-w-full max-h-full object-contain"
                    style="transform: scale(1); transition: transform 0.2s ease;">
            </div>

            <!-- Modal Footer with Controls -->
            <div class="flex justify-center items-center gap-3 p-4 border-t bg-gray-50">
                <button onclick="zoomOut()"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-900 px-4 py-2 rounded text-sm font-medium transition-colors">
                    🔍− Zoom Out
                </button>
                <span id="zoomLevel" class="text-sm font-medium text-gray-600 min-w-[60px] text-center">100%</span>
                <button onclick="zoomIn()"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-900 px-4 py-2 rounded text-sm font-medium transition-colors">
                    🔍+ Zoom In
                </button>
                <div class="border-l border-gray-300 mx-2"></div>
                <button onclick="resetZoom()"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                    ✓ Reset
                </button>
                <a id="downloadLink" href="" download
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                    ⬇ Download
                </a>
            </div>
        </div>
    </div>

    <script>
        let currentZoom = 1;
        const zoomStep = 0.1;
        const maxZoom = 3;
        const minZoom = 0.5;

        function openImageModal(imageSrc, fileName) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalFileName = document.getElementById('modalFileName');
            const downloadLink = document.getElementById('downloadLink');

            modalImage.src = imageSrc;
            modalFileName.textContent = fileName;
            downloadLink.href = imageSrc;
            downloadLink.download = fileName;

            currentZoom = 1;
            updateZoomDisplay();
            resetImageTransform();

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function zoomIn() {
            if (currentZoom < maxZoom) {
                currentZoom = Math.min(currentZoom + zoomStep, maxZoom);
                applyZoom();
            }
        }

        function zoomOut() {
            if (currentZoom > minZoom) {
                currentZoom = Math.max(currentZoom - zoomStep, minZoom);
                applyZoom();
            }
        }

        function resetZoom() {
            currentZoom = 1;
            applyZoom();
        }

        function applyZoom() {
            const modalImage = document.getElementById('modalImage');
            modalImage.style.transform = `scale(${currentZoom})`;
            updateZoomDisplay();
        }

        function resetImageTransform() {
            const modalImage = document.getElementById('modalImage');
            modalImage.style.transform = 'scale(1)';
        }

        function updateZoomDisplay() {
            const zoomLevel = document.getElementById('zoomLevel');
            zoomLevel.textContent = Math.round(currentZoom * 100) + '%';
        }

        // Close modal dengan ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('imageModal');
                if (!modal.classList.contains('hidden')) {
                    closeImageModal();
                }
            }
        });

        // Scroll zoom dengan mouse wheel (optional)
        document.addEventListener('wheel', function(e) {
            const modal = document.getElementById('imageModal');
            if (!modal.classList.contains('hidden') && e.target.id === 'modalImage') {
                e.preventDefault();
                if (e.deltaY > 0) {
                    zoomOut();
                } else {
                    zoomIn();
                }
            }
        }, {
            passive: false
        });
    </script>

    <script>
        function toggleRejectForm() {
            const form = document.getElementById('rejectForm');
            form.classList.toggle('hidden');
        }

        function validateRejectMessage() {
            const message = document.getElementById('rejectMessage').value.trim();
            if (message.length < 10) {
                alert('Alasan penolakan minimal 10 karakter! Saat ini: ' + message.length + ' karakter.');
                return false;
            }
            return true;
        }

        // Update char counter
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('rejectMessage');
            const charCount = document.getElementById('charCount');

            if (textarea) {
                textarea.addEventListener('input', function() {
                    const count = this.value.length;
                    charCount.textContent = count + ' karakter' + (count < 10 ? ' (min 10)' : '');
                    charCount.className = count < 10 ? 'text-xs text-red-600 mb-2 font-medium' :
                        'text-xs text-green-600 mb-2 font-medium';
                });
            }
        });
    </script>

    <!-- Custom Delete Modal (Tailwind) -->
    <div id="deleteModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden animate-fade-in-up">
            <!-- Header -->
            <div class="bg-red-600 px-6 py-4 flex items-center gap-3 text-white">
                <span class="text-2xl">⚠️</span>
                <h3 class="text-xl font-bold">Konfirmasi Penghapusan</h3>
            </div>
            
            <!-- Body -->
            <div class="p-6">
                <p class="text-gray-800 font-semibold mb-4">Anda yakin ingin menghapus nota ini secara permanen?</p>
                
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <span class="text-blue-500 mt-0.5">ℹ️</span>
                        <div>
                            <p class="text-sm text-blue-800 font-bold mb-1">Informasi Penting:</p>
                            <ul class="text-xs text-blue-700 space-y-1 list-disc ml-4">
                                <li>Data akan dipindahkan ke <strong>tabel arsip</strong></li>
                                <li>Nomor urut nota ini akan <strong>tersedia kembali</strong> untuk input baru jika ini adalah nota terakhir</li>
                                <li>Data asli di tabel utama akan <strong>benar-benar dihapus</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="text-sm text-gray-600 space-y-1 bg-gray-50 p-3 rounded-lg border">
                    <div class="flex justify-between">
                        <span>Nomor Nota:</span>
                        <span class="font-bold text-gray-900">{{ $nota->nomor_nota ?? '(Digital)' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Nominal:</span>
                        <span class="font-bold text-gray-900">{{ $nota->nominal_formatted }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end border-t">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                    ❌ Batal
                </button>
                <form action="{{ route('nota.destroy', $nota) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 transition-colors shadow-sm">
                        🗑️ Ya, Hapus & Arsipkan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal on click outside (optional)
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
    </script>
@endsection
