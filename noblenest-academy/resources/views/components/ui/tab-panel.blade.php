@props([
    'id' => '',
])

<div
    role="tabpanel"
    id="panel-{{ $id }}"
    aria-labelledby="tab-{{ $id }}"
    x-show="active === '{{ $id }}'"
    x-cloak
    {{ $attributes }}
>
    {{ $slot }}
</div>
