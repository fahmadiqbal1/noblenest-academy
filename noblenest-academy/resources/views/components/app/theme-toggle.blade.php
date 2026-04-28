@php
    $user  = auth()->user();
    $role  = $user->role ?? null;
    $theme = session('theme', $role === 'Parent' ? 'professional' : ($role === 'Student' ? 'playful' : 'professional'));
    $isPlayful = $theme === 'playful';
@endphp

<form method="POST" action="/theme-toggle" class="inline-flex">
    @csrf
    <x-ui.switch
        name="theme"
        :checked="$isPlayful"
        label="{{ $isPlayful ? 'Playful' : 'Professional' }}"
        x-on:click="$el.closest('form').submit()"
        title="Switch theme"
    />
</form>
