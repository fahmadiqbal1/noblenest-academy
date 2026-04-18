@props([
    'src'   => '',
    'alt'   => '',
    'eager' => false,
])

<img
    src="{{ $src }}"
    alt="{{ $alt }}"
    @if(!$eager) loading="lazy" decoding="async" @endif
    {{ $attributes }}
/>
