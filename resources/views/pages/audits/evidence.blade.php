@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('audit.current') }}" class="text-sm text-purple-600 hover:text-purple-700 mb-2 inline-block">
                ← Retour à l'audit
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Collecte de preuves</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $audit->titre }} - {{ $audit->entreprise->nom }}</p>
        </div>
    </div>

    <div class="card">
        <div class="space-y-4">
            @forelse($preuves as $code => $preuvesList)
                <div class="border rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b">
                        <h3 class="font-semibold text-gray-900">Contrôle {{ $code }}</h3>
                    </div>
                    <div class="divide-y">
                        @foreach($preuvesList as $preuve)
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="badge-soft-info text-xs">{{ $preuve->type_preuve }}</span>
                                            <h4 class="font-medium text-gray-900">{{ $preuve->titre }}</h4>
                                        </div>
                                        <p class="text-sm text-gray-600">{{ $preuve->contenu }}</p>
                                        @if($preuve->source)
                                            <p class="text-xs text-gray-400 mt-1">Source: {{ $preuve->source }}</p>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-sm"
                                                onclick="markAsConsulted('{{ $preuve->id }}')">
                                            Marquer comme consultée
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p>Aucune preuve disponible</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
function markAsConsulted(preuveId) {
    // Implémentation pour marquer la preuve comme consultée
    alert('Preuve marquée comme consultée');
}
</script>
@endpush
@endsection