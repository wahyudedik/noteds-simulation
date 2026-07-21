{{-- Toast Container --}}
<div id="toast-container" class="fixed bottom-4 right-4 z-[9999] flex flex-col-reverse gap-2 pointer-events-none"></div>

{{-- Server-side Flash Messages --}}
@foreach(['success', 'error', 'warning', 'info'] as $type)
    @if(session($type))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.showToast && window.showToast('{{ addslashes(session($type)) }}', '{{ $type }}');
            });
        </script>
    @endif
@endforeach

@if(session('status'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.showToast && window.showToast('{{ addslashes(session('status')) }}', 'success');
        });
    </script>
@endif
