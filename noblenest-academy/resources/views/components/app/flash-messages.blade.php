{{-- Renders session flash messages via the Alpine toast store --}}
@if(session('success') || session('error') || session('status') || session('warning') || session('info'))
<script>
document.addEventListener('alpine:init', function() {
    @if(session('success'))
        Alpine.store('toasts').add({ type: 'success', message: @js(session('success')) });
    @endif
    @if(session('error'))
        Alpine.store('toasts').add({ type: 'error', message: @js(session('error')) });
    @endif
    @if(session('status'))
        Alpine.store('toasts').add({ type: 'info', message: @js(session('status')) });
    @endif
    @if(session('warning'))
        Alpine.store('toasts').add({ type: 'warn', message: @js(session('warning')) });
    @endif
    @if(session('info'))
        Alpine.store('toasts').add({ type: 'info', message: @js(session('info')) });
    @endif
});
</script>
@endif
