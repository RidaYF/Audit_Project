@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('controls.iso') }}" class="text-sm text-orange-600 hover:text-orange-700 mb-2 inline-block">
                ← Retour aux contrôles
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $control->code }} - {{ $control->titre }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $control->categorie ?? 'Sans catégorie' }}</p>
        </div>
        <div class="flex gap-2">
            @if(isset($currentAudit))
                <a href="{{ route('audits.control.edit', [$currentAudit->id, $currentEvaluation->id ?? $control->id]) }}" 
                   class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                    Modifier l'évaluation
                </a>
            @endif
            <a href="{{ route('evaluation.index') }}?controle={{ $control->id }}" 
               class="px-4 py-2 border border-orange-600 text-orange-600 rounded-lg hover:bg-orange-50 transition-colors">
                Évaluer ce contrôle
            </a>
        </div>
    </div>

    <!-- Control Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Description
                </h2>
                <p class="text-gray-700">{{ $control->description }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Objectif
                </h2>
                <p class="text-gray-700">{{ $control->objectif }}</p>
            </div>
            
            @if($control->conseils_audit)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Conseils d'audit
                </h2>
                <p class="text-gray-700">{{ $control->conseils_audit }}</p>
            </div>
            @endif
        </div>
        
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Informations
                </h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Niveau critique</p>
                        @php
                            $riskColors = [
                                'critique' => 'bg-red-100 text-red-700',
                                'eleve' => 'bg-orange-100 text-orange-700',
                                'moyen' => 'bg-yellow-100 text-yellow-700',
                                'faible' => 'bg-green-100 text-green-700'
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $riskColors[$control->niveau_critique] ?? 'bg-gray-100' }} mt-1">
                            {{ ucfirst($control->niveau_critique) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Type de contrôle</p>
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 mt-1">
                            {{ ucfirst($control->type_controle) }}
                        </span>
                    </div>
                    @if($control->ponderation_defaut)
                    <div>
                        <p class="text-sm text-gray-500">Pondération par défaut</p>
                        <p class="font-medium text-gray-900">{{ $control->ponderation_defaut }}</p>
                    </div>
                    @endif
                    @if($control->temps_estime)
                    <div>
                        <p class="text-sm text-gray-500">Temps estimé</p>
                        <p class="font-medium text-gray-900">{{ $control->temps_estime }} minutes</p>
                    </div>
                    @endif
                    @if($control->clause)
                    <div>
                        <p class="text-sm text-gray-500">Clause associée</p>
                        <p class="font-medium text-gray-900">{{ $control->clause->numero }} - {{ $control->clause->titre }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            @if($control->preuves && $control->preuves->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Preuves associées
                </h2>
                <div class="space-y-3">
                    @foreach($control->preuves as $preuve)
                        <div class="border-b border-gray-100 last:border-0 pb-3 last:pb-0">
                            <p class="font-medium text-gray-900">{{ $preuve->titre }}</p>
                            <p class="text-sm text-gray-600">{{ Str::limit($preuve->contenu, 100) }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $preuve->type_preuve }}</span>
                                @if($preuve->points_max)
                                    <span class="text-xs text-gray-500">Max: {{ $preuve->points_max }} pts</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Audits ayant évalué ce contrôle -->
    @if(isset($audits) && $audits->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Évaluations dans les audits
        </h2>
        <div class="space-y-3">
            @foreach($audits as $auditItem)
                @php
                    $evaluation = $auditItem->evaluations->first();
                    $statusColors = [
                        'conforme' => 'bg-green-100 text-green-700',
                        'non_conforme' => 'bg-red-100 text-red-700',
                        'partiellement_conforme' => 'bg-yellow-100 text-yellow-700',
                        'non_applicable' => 'bg-gray-100 text-gray-700',
                        'non_audite' => 'bg-blue-100 text-blue-700'
                    ];
                    $statusLabels = [
                        'conforme' => 'Conforme',
                        'non_conforme' => 'Non conforme',
                        'partiellement_conforme' => 'Partiellement conforme',
                        'non_applicable' => 'Non applicable',
                        'non_audite' => 'Non audité'
                    ];
                @endphp
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">{{ $auditItem->titre }}</p>
                        <p class="text-sm text-gray-500">{{ $auditItem->entreprise->nom }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$evaluation->statut] ?? 'bg-gray-100' }}">
                            {{ $statusLabels[$evaluation->statut] ?? $evaluation->statut }}
                        </span>
                        <a href="{{ route('audits.show', $auditItem->id) }}" class="text-orange-600 hover:text-orange-700">
                            Voir
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection