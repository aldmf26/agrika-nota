@props([
    'name' => 'divisi_id',
    'id' => null,
    'divisis' => [],
    'value' => '',
    'required' => false,
    'placeholder' => 'Ketik untuk mencari divisi...',
    'onchange' => null,
])

@php
    $inputId = $id ?? $name;
    $hiddenId = $inputId . '_hidden';
    $dropdownId = $inputId . '_dropdown';
    $selectedDivisi = $value ? (is_array($divisis) ? collect($divisis)->firstWhere('id', $value) : $divisis->firstWhere('id', $value)) : null;
@endphp

<div class="relative divisi-autocomplete-wrapper" data-input-id="{{ $inputId }}">
    <input type="hidden" name="{{ $name }}" id="{{ $hiddenId }}" value="{{ $value }}">
    <input type="text"
        id="{{ $inputId }}"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 autocomplete-input text-gray-900"
        placeholder="{{ $placeholder }}"
        value="{{ $selectedDivisi ? ($selectedDivisi['nama'] ?? $selectedDivisi->nama) : '' }}"
        autocomplete="off"
        @if($required) required @endif
        @if($onchange) data-onchange="{{ $onchange }}" @endif
    >
    <button type="button"
        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 autocomplete-clear {{ $value ? '' : 'hidden' }}"
        onclick="clearAutocomplete('{{ $inputId }}')"
        title="Hapus pilihan"
    >&times;</button>
    <div id="{{ $dropdownId }}"
        class="absolute z-[999] w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-60 overflow-y-auto hidden autocomplete-dropdown">
    </div>
</div>

@once
<script>
(function() {
    const STYLE_ID = 'divisi-autocomplete-style';
    if (!document.getElementById(STYLE_ID)) {
        const style = document.createElement('style');
        style.id = STYLE_ID;
        style.textContent = `
            .divisi-autocomplete-wrapper.is-active {
                z-index: 100 !important;
            }
            .autocomplete-dropdown {
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            }
            .autocomplete-dropdown .autocomplete-option {
                padding: 10px 16px;
                cursor: pointer;
                font-size: 0.875rem;
                color: #1f2937;
                display: flex;
                align-items: center;
                justify-content: space-between;
                transition: background-color 0.15s ease;
            }
            .autocomplete-dropdown .autocomplete-option:hover,
            .autocomplete-dropdown .autocomplete-option.active {
                background-color: #f0fdf4;
                color: #166534;
            }
            .autocomplete-dropdown .autocomplete-no-results {
                padding: 12px 16px;
                font-size: 0.875rem;
                color: #9ca3af;
                font-style: italic;
                text-align: center;
            }
        `;
        document.head.appendChild(style);
    }

    document.addEventListener('click', function(e) {
        document.querySelectorAll('.divisi-autocomplete-wrapper').forEach(wrapper => {
            if (!wrapper.contains(e.target)) {
                const dropdown = wrapper.querySelector('.autocomplete-dropdown');
                if (dropdown) dropdown.classList.add('hidden');
                wrapper.classList.remove('is-active');
            }
        });
    });

    window.clearAutocomplete = function(inputId) {
        const wrapper = document.querySelector(`[data-input-id="${inputId}"]`);
        if (!wrapper) return;
        const input = wrapper.querySelector('.autocomplete-input');
        const hidden = wrapper.querySelector('input[type="hidden"]');
        const clearBtn = wrapper.querySelector('.autocomplete-clear');
        const dropdown = wrapper.querySelector('.autocomplete-dropdown');
        
        if (input) input.value = '';
        if (hidden) hidden.value = '';
        if (clearBtn) clearBtn.classList.add('hidden');
        if (dropdown) dropdown.classList.add('hidden');
        wrapper.classList.remove('is-active');
        if (input) input.focus();
        
        if (input && input.dataset.onchange) {
            const fn = window[input.dataset.onchange];
            if (typeof fn === 'function') fn('');
        }
    };

    window.initDivisiAutocomplete = function(inputId, divisiItems, options = {}) {
        const wrapper = document.querySelector(`[data-input-id="${inputId}"]`);
        if (!wrapper) return;
        const input = wrapper.querySelector('.autocomplete-input');
        const hidden = wrapper.querySelector('input[type="hidden"]');
        const clearBtn = wrapper.querySelector('.autocomplete-clear');
        const dropdown = wrapper.querySelector('.autocomplete-dropdown');
        let activeIndex = -1;

        if (!input || !hidden || !dropdown) return;

        wrapper._divisiItems = divisiItems || [];

        function filter(query) {
            const q = (query || '').toLowerCase().trim();
            if (!q) return wrapper._divisiItems;
            return wrapper._divisiItems.filter(d =>
                (d.nama && d.nama.toLowerCase().includes(q)) ||
                (d.kode && d.kode.toLowerCase().includes(q))
            );
        }

        function renderDropdown(items) {
            dropdown.innerHTML = '';
            activeIndex = -1;
            wrapper.classList.add('is-active');

            if (!items || items.length === 0) {
                const noResult = document.createElement('div');
                noResult.className = 'autocomplete-no-results';
                noResult.textContent = 'Tidak ditemukan';
                dropdown.appendChild(noResult);
                dropdown.classList.remove('hidden');
                return;
            }

            items.forEach((item) => {
                const opt = document.createElement('div');
                opt.className = 'autocomplete-option';
                opt.dataset.id = item.id;
                opt.dataset.nama = item.nama;
                
                const nameSpan = document.createElement('span');
                nameSpan.className = 'font-medium';
                nameSpan.textContent = item.nama;
                opt.appendChild(nameSpan);

                if (item.kode) {
                    const badge = document.createElement('span');
                    badge.className = 'ml-2 text-xs font-mono px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded border border-gray-200';
                    badge.textContent = item.kode.toUpperCase();
                    opt.appendChild(badge);
                }

                opt.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    selectItem(item);
                });
                dropdown.appendChild(opt);
            });
            dropdown.classList.remove('hidden');
        }

        function selectItem(item) {
            input.value = item.nama;
            hidden.value = item.id;
            dropdown.classList.add('hidden');
            wrapper.classList.remove('is-active');
            if (clearBtn) clearBtn.classList.remove('hidden');

            if (options.onSelect) {
                options.onSelect(item);
            }
            if (input.dataset.onchange) {
                const fn = window[input.dataset.onchange];
                if (typeof fn === 'function') fn(item.id);
            }
        }

        input.oninput = function() {
            const items = filter(input.value);
            if (input.value.trim() === '') {
                hidden.value = '';
                if (clearBtn) clearBtn.classList.add('hidden');
                if (input.dataset.onchange) {
                    const fn = window[input.dataset.onchange];
                    if (typeof fn === 'function') fn('');
                }
            } else {
                if (clearBtn) clearBtn.classList.remove('hidden');
            }
            renderDropdown(items);
        };

        input.onfocus = function() {
            const items = filter(input.value);
            renderDropdown(items);
        };

        input.onblur = function() {
            setTimeout(() => {
                if (document.activeElement !== input && !wrapper.contains(document.activeElement)) {
                    dropdown.classList.add('hidden');
                    wrapper.classList.remove('is-active');
                    if (hidden.value) {
                        const current = wrapper._divisiItems.find(d => String(d.id) === String(hidden.value));
                        if (current) {
                            input.value = current.nama;
                        } else {
                            input.value = '';
                            hidden.value = '';
                        }
                    } else {
                        input.value = '';
                    }
                    if (!input.value && clearBtn) {
                        clearBtn.classList.add('hidden');
                    }
                }
            }, 200);
        };

        input.onkeydown = function(e) {
            const optionsList = dropdown.querySelectorAll('.autocomplete-option');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (dropdown.classList.contains('hidden')) {
                    renderDropdown(filter(input.value));
                    return;
                }
                activeIndex = Math.min(activeIndex + 1, optionsList.length - 1);
                optionsList.forEach((o, i) => o.classList.toggle('active', i === activeIndex));
                if (optionsList[activeIndex]) {
                    optionsList[activeIndex].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                optionsList.forEach((o, i) => o.classList.toggle('active', i === activeIndex));
                if (optionsList[activeIndex]) {
                    optionsList[activeIndex].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'Enter') {
                if (!dropdown.classList.contains('hidden') && activeIndex >= 0 && optionsList[activeIndex]) {
                    e.preventDefault();
                    const id = optionsList[activeIndex].dataset.id;
                    const nama = optionsList[activeIndex].dataset.nama;
                    const selected = wrapper._divisiItems.find(d => String(d.id) === String(id)) || { id, nama };
                    selectItem(selected);
                }
            } else if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
                wrapper.classList.remove('is-active');
            }
        };

        if (hidden.value && clearBtn) {
            clearBtn.classList.remove('hidden');
        }
    };

    window.updateAutocompleteData = function(inputId, newDivisiData) {
        const wrapper = document.querySelector(`[data-input-id="${inputId}"]`);
        if (!wrapper) return;
        window.initDivisiAutocomplete(inputId, newDivisiData);
    };
})();
</script>
@endonce

<script>
(function() {
    const run = function() {
        if (typeof window.initDivisiAutocomplete === 'function') {
            window.initDivisiAutocomplete('{{ $inputId }}', @json($divisis));
        }
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})();
</script>
