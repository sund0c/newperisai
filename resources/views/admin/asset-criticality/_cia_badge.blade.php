{{-- resources/views/admin/asset-criticality/_cia_badge.blade.php --}}
{{-- Usage: @include('admin.asset-criticality._cia_badge', ['val' => $c->confidentiality]) --}}
@php
    $map = [
        1 => ['label' => 'R', 'class' => 'bg-green-100 text-green-700'],
        2 => ['label' => 'S', 'class' => 'bg-amber-100 text-amber-700'],
        3 => ['label' => 'T', 'class' => 'bg-red-100 text-red-700'],
    ];
    $cfg = $map[$val] ?? ['label' => '-', 'class' => 'bg-gray-100 text-gray-500'];
@endphp
<span class="inline-flex h-6 w-6 items-center justify-center rounded-md text-xs font-bold {{ $cfg['class'] }}"
    title="{{ ['1' => 'Rendah', '2' => 'Sedang', '3' => 'Tinggi'][$val] ?? '-' }}">
    {{ $cfg['label'] }}
</span>
