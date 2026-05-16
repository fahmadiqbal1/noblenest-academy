@props([
    'name'   => null,
    'src'    => null,
    'size'   => 'md',
    'status' => null,
    'ring'   => false,
])

@php
    $sizes = [
        'xs' => ['outer' => 'w-6 h-6',   'text' => 'text-xs',  'status' => 'w-1.5 h-1.5'],
        'sm' => ['outer' => 'w-8 h-8',   'text' => 'text-xs',  'status' => 'w-2 h-2'],
        'md' => ['outer' => 'w-10 h-10',  'text' => 'text-sm',  'status' => 'w-2.5 h-2.5'],
        'lg' => ['outer' => 'w-14 h-14',  'text' => 'text-base','status' => 'w-3 h-3'],
        'xl' => ['outer' => 'w-20 h-20',  'text' => 'text-xl',  'status' => 'w-3.5 h-3.5'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];

    // Deterministic background color from name hash
    $colors = [
        '#7C3AED','#29ABE2','#FF8C42','#44BB77','#F43F5E','#F59E0B','#3B82F6','#EC4899',
    ];
    $bgColor = '#7C3AED';
    if ($name) {
        $hash = 0;
        foreach (str_split($name) as $char) { $hash = ($hash * 31 + ord($char)) % 8; }
        $bgColor = $colors[$hash];
    }

    // Initials
    $initials = '';
    if ($name && !$src) {
        $parts = explode(' ', trim($name));
        $initials = strtoupper(substr($parts[0], 0, 1));
        if (count($parts) > 1) $initials .= strtoupper(substr(end($parts), 0, 1));
    }

    $statusColors = [
        'online'  => 'bg-emerald-500',
        'offline' => 'bg-gray-400',
        'away'    => 'bg-amber-400',
    ];

    $ringClass = $ring ? 'ring-2 ring-[var(--color-brand-300)] ring-offset-2' : '';
@endphp

<span {{ $attributes->class(['relative inline-flex shrink-0']) }}>
    @if($src)
        <img
            src="{{ $src }}"
            alt="{{ $name ?? 'Avatar' }}"
            class="{{ $s['outer'] }} rounded-full object-cover $ringClass"
        />
    @else
        <span
            class="{{ $s['outer'] }} $ringClass rounded-full inline-flex items-center justify-center font-bold text-white $s['text']"
            style="background-color: {{ $bgColor }};"
            aria-label="{{ $name ?? 'Avatar' }}"
        >
            {{ $initials }}
        </span>
    @endif

    @if($status && isset($statusColors[$status]))
        <span
            class="absolute bottom-0 right-0 {{ $s['status'] }} rounded-full $statusColors[$status] ring-2 ring-white"
            aria-label="Status: {{ $status }}"
        ></span>
    @endif
</span>
