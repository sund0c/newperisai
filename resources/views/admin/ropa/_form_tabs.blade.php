{{-- Shared tab JS & helpers — di-include dari create dan edit --}}
@push('scripts')
<script>
function switchTab(key) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('text-indigo-600','border-indigo-600');
        b.classList.add('text-gray-500','border-transparent');
    });
    document.getElementById('tab-' + key).classList.remove('hidden');
    const btn = document.getElementById('tab-btn-' + key);
    btn.classList.add('text-indigo-600','border-indigo-600');
    btn.classList.remove('text-gray-500','border-transparent');
}

// ── Asset rows ──────────────────────────────────────────────
const assetOptions = `{!! $assets->map(fn($a) => '<option value="'.$a->id.'">'.$a->kode_aset.' — '.addslashes($a->nama_aset).'</option>')->implode('') !!}`;
const peranAsetOptions = `{!! collect(\App\Models\RopaAsset::PERAN_LABELS)->map(fn($l,$v) => '<option value="'.$v.'">'.$l.'</option>')->implode('') !!}`;

function addAssetRow() {
    const container = document.getElementById('assets-container');
    const idx = container.querySelectorAll('.asset-row').length;
    const div = document.createElement('div');
    div.className = 'asset-row flex flex-wrap items-center gap-2';
    div.innerHTML = `
        <select name="assets[${idx}][asset_instance_id]"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">-- Pilih dari inventaris aset (opsional) --</option>${assetOptions}
        </select>
        <input type="text" name="assets[${idx}][nama_manual]"
            placeholder="atau ketik nama aplikasi manual..."
            class="w-52 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <select name="assets[${idx}][peran_aset]"
            class="w-36 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            ${peranAsetOptions}
        </select>
        <button type="button" onclick="this.closest('.asset-row').remove()"
            class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200
                   text-gray-400 hover:text-red-600 hover:border-red-300 transition-colors flex-shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;
    container.appendChild(div);
}

// ── Recipient rows ──────────────────────────────────────────
const peranRecipientOptions = `{!! collect(\App\Models\RopaRecipient::PERAN_LABELS)->map(fn($l,$v) => '<option value="'.$v.'">'.$l.'</option>')->implode('') !!}`;

function addRecipientRow() {
    const container = document.getElementById('recipients-container');
    const noMsg = document.getElementById('no-recipient-msg');
    if (noMsg) noMsg.remove();
    const idx = container.querySelectorAll('.recipient-row').length;
    const div = document.createElement('div');
    div.className = "recipient-row bg-gray-50 rounded-xl border border-gray-200 p-4";;
    div.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Profil Penerima</label>
                <input type="text" name="recipients[${idx}][profil_penerima]"
                    placeholder="Contoh: KemenPAN-RB, BKPSDM Internal, Vendor Sistem CAT"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tipe</label>
                <select name="recipients[${idx}][tipe]" onchange="togglePeranField(this)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="internal">Internal</option>
                    <option value="eksternal">Eksternal</option>
                </select>
            </div>
            <div class="peran-field hidden">
                <label class="block text-xs font-medium text-gray-500 mb-1">Peran</label>
                <select name="recipients[${idx}][peran]"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">— pilih peran —</option>${peranRecipientOptions}
                </select>
            </div>
            <div class="peran-field hidden">
                <label class="block text-xs font-medium text-gray-500 mb-1">Kontak / PIC</label>
                <input type="text" name="recipients[${idx}][kontak_pic]"
                    placeholder="Contoh: Kepala Pusat Data KemenPAN-RB"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tujuan Pengiriman</label>
                <input type="text" name="recipients[${idx}][tujuan_pengiriman]"
                    placeholder="Contoh: Verifikasi data kependudukan dan integrasi sistem LAPOR!"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Mekanisme Pengiriman</label>
                <input type="text" name="recipients[${idx}][mekanisme_pengiriman]"
                    placeholder="Contoh: API terenkripsi SP4N-LAPOR!, SFTP untuk file batch"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Jenis Data yang Dikirim</label>
                <input type="text" name="recipients[${idx}][jenis_data_dikirim]"
                    placeholder="Contoh: Nama, NIK, alamat domisili, isi pengaduan, kategori layanan"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        <div class="mt-2 flex justify-end">
            <button type="button" onclick="this.closest('.recipient-row').remove()"
                class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus penerima ini</button>
        </div>`;
    container.appendChild(div);
}

function togglePeranField(select) {
    const isEksternal = select.value === 'eksternal';
    select.closest('.recipient-row').querySelectorAll('.peran-field')
        .forEach(f => f.classList.toggle('hidden', !isEksternal));
}

// ── Ordered List Helper ─────────────────────────────────────
function createOrderedList(containerId, items) {
    items = items || [];
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    if (items.length === 0) {
        addOrderedItem(containerId);
    } else {
        items.forEach(text => addOrderedItem(containerId, text));
    }
}

function addOrderedItem(containerId, value) {
    value = value || '';
    const container = document.getElementById(containerId);
    const fieldName = container.dataset.field;
    const div = document.createElement('div');
    div.className = 'ol-item flex items-center gap-2 mb-1.5';
    div.innerHTML =
        '<span class="ol-num w-5 text-xs font-bold text-gray-400 text-right flex-shrink-0"></span>' +
        '<input type="text" name="' + fieldName + '[]" value="' + value.replace(/"/g, '&quot;') + '" ' +
        'placeholder="Ketik langkah pengamanan..." ' +
        'class="flex-1 px-3 py-1.5 border border-gray-300 rounded-lg text-sm ' +
        'focus:outline-none focus:ring-2 focus:ring-indigo-500">' +
        '<button type="button" onclick="removeOrderedItem(this, \'' + containerId + '\')" ' +
        'class="w-6 h-6 flex items-center justify-center rounded text-gray-300 ' +
        'hover:text-red-500 transition-colors flex-shrink-0">' +
        '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>' +
        '</svg></button>';
    container.appendChild(div);
    renumberList(containerId);
}

function removeOrderedItem(btn, containerId) {
    const container = document.getElementById(containerId);
    if (container.querySelectorAll('.ol-item').length > 1) {
        btn.closest('.ol-item').remove();
        renumberList(containerId);
    }
}

function renumberList(containerId) {
    document.querySelectorAll('#' + containerId + ' .ol-num').forEach(function(el, i) {
        el.textContent = (i + 1) + '.';
    });
}

// restore tab dari hash URL
const hashTab = window.location.hash?.replace('#','');
if (hashTab && document.getElementById('tab-' + hashTab)) switchTab(hashTab);

// ── Init ordered lists dari data-old ─────────────────────────
document.querySelectorAll('.ol-container').forEach(function(container) {
    let items = [];
    try { items = JSON.parse(container.dataset.old || '[]'); } catch(e) {}
    const isReadonly = container.dataset.readonly === 'true';
    createOrderedList(container.id, items);
    if (isReadonly) {
        container.querySelectorAll('input').forEach(i => i.setAttribute('readonly', true));
        container.querySelectorAll('button').forEach(b => b.style.display = 'none');
    }
});

// ── Auto-sync indikator data_spesifik dari checkbox data spesifik ──
function syncDataSpesifikIndicator() {
    const dataSpesifikChecks = document.querySelectorAll('input[name="data_spesifik[]"]:checked');
    const indikatorCb = document.getElementById('indikator_data_spesifik');
    if (!indikatorCb) return;
    const hasSpesifik = dataSpesifikChecks.length > 0;
    indikatorCb.checked = hasSpesifik;
    // Prevent manual toggle
    indikatorCb.onclick = e => e.preventDefault();
}

// Attach listener ke semua checkbox data spesifik
document.querySelectorAll('input[name="data_spesifik[]"]').forEach(cb => {
    cb.addEventListener('change', syncDataSpesifikIndicator);
});

// Run on load
syncDataSpesifikIndicator();
</script>
@endpush
