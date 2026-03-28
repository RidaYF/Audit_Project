@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('controls.iso') }}" class="text-sm text-purple-600 hover:text-purple-700 mb-2 inline-block">
                ← Retour aux contrôles
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $control->code }} - {{ $control->titre }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $control->categorie ?? 'Sans catégorie' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('evaluation.index') }}?controle={{ $control->id }}" class="btn btn-primary">
                Évaluer ce contrôle
            </a>
        </div>
    </div>

    <!-- Control Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Description</h2>
                <p class="text-gray-700">{{ $control->description }}</p>
            </div>
            
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Objectif</h2>
                <p class="text-gray-700">{{ $control->objectif }}</p>
            </div>
            
            @if($control->conseils_audit)
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Conseils d'audit</h2>
                <p class="text-gray-700">{{ $control->conseils_audit }}</p>
            </div>
            @endif
        </div>
        
        <div class="space-y-6">
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations</h2>
                <div class="space-y-3">
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
                        <span class="badge {{ $riskColors[$control->niveau_critique] ?? 'bg-gray-100' }} mt-1">
                            {{ ucfirst($control->niveau_critique) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Type de contrôle</p>
                        <span class="badge-soft-info mt-1">{{ ucfirst($control->type_controle) }}</span>
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
            
            @if($control->preuves->count() > 0)
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Preuves associées</h2>
                <div class="space-y-3">
                    @foreach($control->preuves as $preuve)
                        <div class="border-b border-gray-100 last:border-0 pb-3 last:pb-0">
                            <p class="font-medium text-gray-900">{{ $preuve->titre }}</p>
                            <p class="text-sm text-gray-600">{{ $preuve->contenu }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="badge-soft-info text-xs">{{ $preuve->type_preuve }}</span>
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
    @if($audits->count() > 0)
    <div class="card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Évaluations dans les audits</h2>
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
                @endphp
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">{{ $auditItem->titre }}</p>
                        <p class="text-sm text-gray-500">{{ $auditItem->entreprise->nom }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="badge {{ $statusColors[$evaluation->statut] ?? 'bg-gray-100' }}">
                            {{ ucfirst(str_replace('_', ' ', $evaluation->statut)) }}
                        </span>
                        <a href="{{ route('audits.show', $auditItem->id) }}" class="text-purple-600 hover:text-purple-700">
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