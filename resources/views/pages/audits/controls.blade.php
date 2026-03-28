@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('audit.current') }}" class="text-sm text-purple-600 hover:text-purple-700 mb-2 inline-block">
                ← Retour à l'audit
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Contrôles ISO 27002</h1>
            <p class="text-sm text-gray-500 mt-1">Liste des contrôles de sécurité selon la norme ISO 27002</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="card">
            <p class="text-sm text-gray-500">Total contrôles</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Critiques</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['critique'] }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Élevés</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['eleve'] }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Moyens</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['moyen'] }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Faibles</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['faible'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('controls.iso') }}" 
               class="px-3 py-1 rounded-full text-sm {{ !$currentSection ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Tous
            </a>
            @foreach($categories as $category)
                <a href="{{ route('controls.iso', ['section' => $category]) }}" 
                   class="px-3 py-1 rounded-full text-sm {{ $currentSection == $category ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $category }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Controls List -->
    <div class="space-y-3">
        @forelse($controls as $control)
            @php
                $riskColors = [
                    'critique' => 'bg-red-100 text-red-700',
                    'eleve' => 'bg-orange-100 text-orange-700',
                    'moyen' => 'bg-yellow-100 text-yellow-700',
                    'faible' => 'bg-green-100 text-green-700'
                ];
                $typeColors = [
                    'preventif' => 'bg-blue-100 text-blue-700',
                    'detectif' => 'bg-purple-100 text-purple-700',
                    'correctif' => 'bg-green-100 text-green-700',
                    'analyse' => 'bg-orange-100 text-orange-700'
                ];
            @endphp
            <div class="card hover:shadow-md transition-all">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-2">
                            <span class="font-mono text-lg font-bold text-purple-600">{{ $control->code }}</span>
                            <span class="badge {{ $riskColors[$control->niveau_critique] ?? 'bg-gray-100' }}">
                                {{ ucfirst($control->niveau_critique) }}
                            </span>
                            <span class="badge {{ $typeColors[$control->type_controle] ?? 'bg-gray-100' }}">
                                {{ ucfirst($control->type_controle) }}
                            </span>
                            @if($control->categorie)
                                <span class="badge-soft-info text-xs">{{ $control->categorie }}</span>
                            @endif
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $control->titre }}</h3>
                        <p class="text-sm text-gray-600 mb-3">{{ $control->description }}</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Objectif:</span>
                                <p class="text-gray-700 mt-1">{{ Str::limit($control->objectif, 150) }}</p>
                            </div>
                            @if($control->conseils_audit)
                            <div>
                                <span class="text-gray-500">Conseils d'audit:</span>
                                <p class="text-gray-700 mt-1">{{ Str::limit($control->conseils_audit, 150) }}</p>
                            </div>
                            @endif
                        </div>
                        
                        <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                            @if($control->ponderation_defaut)
                                <span>⚖️ Pondération: {{ $control->ponderation_defaut }}</span>
                            @endif
                            @if($control->temps_estime)
                                <span>⏱️ Temps estimé: {{ $control->temps_estime }} min</span>
                            @endif
                            @if($control->clause)
                                <span>📋 Clause: {{ $control->clause->numero }} - {{ $control->clause->titre }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="ml-4">
                        <a href="{{ route('controls.iso.show', $control->id) }}" 
                           class="btn btn-outline-primary btn-sm">
                            Voir détails
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="card text-center py-8">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500">Aucun contrôle trouvé</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $controls->links() }}
    </div>
</div>
@endsection