{{-- Mensagens flash de sucesso, erro e aviso --}}

@if(session('success'))
    <div class="mb-4 flex items-start gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
        <span class="mt-0.5">✅</span>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
        <span class="mt-0.5">❌</span>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if(session('warning'))
    <div class="mb-4 flex items-start gap-3 px-4 py-3 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg text-sm">
        <span class="mt-0.5">⚠️</span>
        <span>{{ session('warning') }}</span>
    </div>
@endif

@if(session('info'))
    <div class="mb-4 flex items-start gap-3 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm">
        <span class="mt-0.5">ℹ️</span>
        <span>{{ session('info') }}</span>
    </div>
@endif
