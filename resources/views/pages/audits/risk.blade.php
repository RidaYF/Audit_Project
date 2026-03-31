@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('audit.current') }}" class="text-sm text-purple-600 hover:text-purple-700 mb-2 inline-block">
                ← Retour à l'audit
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des risques (ISO 27005)</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $audit->titre }} - {{ $audit->entreprise->nom }}</p>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Total non-conformités</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">ISO 27001</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['par_reference']['ISO27001'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">ISO 27002</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['par_reference']['ISO27002'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Autres</p>
            <p class="text-2xl font-bold text-gray-600">{{ $stats['par_reference']['Inconnu'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Liste des non-conformités des clauses -->
    @forelse($nonConformites as $clauseEval)
        @php
            $clause = $clauseEval->clause;
            $reference = $clause->reference ?? null;
            $referenceColor = $reference && $reference->code === 'ISO27001' 
                ? 'border-blue-500 bg-blue-50' 
                : ($reference && $reference->code === 'ISO27002' 
                    ? 'border-purple-500 bg-purple-50' 
                    : 'border-gray-500 bg-gray-50');
            
            $statusColor = match($clauseEval->statut_traitement) {
                'valide' => 'bg-green-100 text-green-700',
                'traite' => 'bg-blue-100 text-blue-700',
                'en_cours' => 'bg-yellow-100 text-yellow-700',
                default => 'bg-red-100 text-red-700'
            };
            
            $priorityIcon = match($clauseEval->priorite) {
                'haute' => '🔴',
                'moyenne' => '🟠',
                'basse' => '🟢',
                default => '⚪'
            };
        @endphp
        <div class="bg-white rounded-lg shadow-sm border-l-4 {{ $referenceColor }} p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="font-mono text-sm font-semibold {{ $reference && $reference->code === 'ISO27001' ? 'text-blue-600' : ($reference && $reference->code === 'ISO27002' ? 'text-purple-600' : 'text-gray-600') }}">
                            {{ $reference->code ?? 'ISO' }} - {{ $clause->numero ?? 'N/A' }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                            Non-conformité
                        </span>
                        @if($clause->niveau_importance)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium 
                            @if($clause->niveau_importance == 'critique') bg-red-100 text-red-700
                            @elseif($clause->niveau_importance == 'eleve') bg-orange-100 text-orange-700
                            @elseif($clause->niveau_importance == 'moyen') bg-yellow-100 text-yellow-700
                            @else bg-green-100 text-green-700
                            @endif">
                            {{ ucfirst($clause->niveau_importance) }}
                        </span>
                        @endif
                        @if($clauseEval->priorite)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                            {{ $priorityIcon }} {{ ucfirst(str_replace('_', ' ', $clauseEval->statut_traitement ?? 'À traiter')) }}
                        </span>
                        @endif
                    </div>
                    
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $clause->titre ?? 'Titre non disponible' }}</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ $clause->description ?? 'Description non disponible' }}</p>
                    
                    @if($clauseEval->justification)
                        <div class="bg-gray-50 rounded-lg p-3 mb-3">
                            <p class="text-xs text-gray-500 mb-1">Justification de la non-conformité:</p>
                            <p class="text-sm text-gray-700">{{ $clauseEval->justification }}</p>
                        </div>
                    @endif
                    
                    @if($clauseEval->recommandation)
                        <div class="bg-blue-50 rounded-lg p-3 mb-3">
                            <p class="text-xs text-blue-600 mb-1">Actions correctives recommandées:</p>
                            <p class="text-sm text-blue-800">{{ $clauseEval->recommandation }}</p>
                        </div>
                    @endif
                    
                    @if($clauseEval->plan_action)
                        <div class="bg-green-50 rounded-lg p-3">
                            <p class="text-xs text-green-600 mb-1">Plan d'action:</p>
                            <p class="text-sm text-green-800">{{ $clauseEval->plan_action }}</p>
                            @if($clauseEval->responsable)
                            <p class="text-xs text-green-600 mt-2">Responsable: {{ $clauseEval->responsable }}</p>
                            @endif
                            @if($clauseEval->date_echeance)
                            <p class="text-xs text-green-600">Échéance: {{ \Carbon\Carbon::parse($clauseEval->date_echeance)->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    @endif
                    
                    <div class="mt-3 flex items-center gap-4 text-xs text-gray-500">
                        <span>Évalué le: {{ $clauseEval->updated_at->format('d/m/Y H:i') }}</span>
                        @if($clauseEval->score)
                        <span>Score: {{ $clauseEval->score }}/100</span>
                        @endif
                    </div>
                </div>
                <div class="ml-4">
                    <a href="{{ route('audits.clause.edit-risk', ['auditId' => $audit->id, 'clauseEvalId' => $clauseEval->id]) }}" 
                       class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg border border-purple-600 text-purple-600 hover:bg-purple-50">
                        Traiter le risque
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 text-center py-8">
            <svg class="w-12 h-12 mx-auto text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500">Aucune non-conformité détectée pour le moment</p>
            <p class="text-sm text-gray-400 mt-1">Toutes les clauses sont conformes</p>
        </div>
    @endforelse
</div>
@endsection