@extends('layouts.app')

@section('title', 'Detail Nota - ' . ($nota->nomor_nota ?? 'Digital'))

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start mb-6 gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Public View</span>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">📝 Detail Nota</h1>
                </div>
                <p class="text-gray-600">{{ $nota->tanggal_nota->format('d F Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <x-status-badge :status="$nota->status" />
                @if($nota->is_printed)
                    <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-black uppercase tracking-tighter border border-amber-100">
                        TERDAFTAR CETAK
                    </span>
                @endif
            </div>
        </div>

        @include('nota.partials.detail-content')
        
    </div>

    <!-- Image Modal with Zoom (Reused from show.blade) -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-75 p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900" id="modalFileName">File Name</h3>
                <button type="button" onclick="closeImageModal()" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
            </div>
            <div class="flex-1 overflow-auto flex items-center justify-center bg-gray-50 relative">
                <img id="modalImage" src="" alt="Full size image" class="max-w-full max-h-full object-contain" style="transform: scale(1); transition: transform 0.2s ease;">
            </div>
            <div class="flex justify-center items-center gap-3 p-4 border-t bg-gray-50">
                <button onclick="zoomOut()" class="bg-gray-300 hover:bg-gray-400 text-gray-900 px-4 py-2 rounded text-sm font-medium transition-colors">🔍− Zoom Out</button>
                <span id="zoomLevel" class="text-sm font-medium text-gray-600 min-w-[60px] text-center">100%</span>
                <button onclick="zoomIn()" class="bg-gray-300 hover:bg-gray-400 text-gray-900 px-4 py-2 rounded text-sm font-medium transition-colors">🔍+ Zoom In</button>
                <div class="border-l border-gray-300 mx-2"></div>
                <button onclick="resetZoom()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium transition-colors">✓ Reset</button>
                <a id="downloadLink" href="" download class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm font-medium transition-colors">⬇ Download</a>
            </div>
        </div>
    </div>

    <script>
        let currentZoom = 1;
        const zoomStep = 0.2;
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
            modalImage.style.transform = 'scale(1)';

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function zoomIn() { if (currentZoom < maxZoom) { currentZoom += zoomStep; applyZoom(); } }
        function zoomOut() { if (currentZoom > minZoom) { currentZoom -= zoomStep; applyZoom(); } }
        function resetZoom() { currentZoom = 1; applyZoom(); }
        function applyZoom() {
            document.getElementById('modalImage').style.transform = `scale(${currentZoom})`;
            updateZoomDisplay();
        }
        function updateZoomDisplay() {
            document.getElementById('zoomLevel').textContent = Math.round(currentZoom * 100) + '%';
        }

        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeImageModal(); });
    </script>
@endsection
