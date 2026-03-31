@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('audits.index') }}" class="text-sm text-purple-600 hover:text-purple-700 mb-2 inline-block">
                ← Retour aux audits
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $audit->titre }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $audit->entreprise->nom }}</p>
        </div>
        <div class="flex gap-2">
            @if($audit->status === 'en_cours')
                <a href="{{ route('audit.current', ['audit_id' => $audit->id]) }}" class="btn btn-primary">
                    Continuer l'audit
                </a>
            @endif
            <a href="{{ route('audits.edit', $audit->id) }}" class="btn btn-outline">
                Modifier
            </a>
        </div>
    </div>
    
    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card">
            <p class="text-sm text-gray-500">Statut</p>
            @php
                $statusLabels = [
                    'en_cours' => ['label' => 'En cours', 'class' => 'badge-soft-primary'],
                    'termine' => ['label' => 'Terminé', 'class' => 'badge-soft-success'],
                    'suspendu' => ['label' => 'Suspendu', 'class' => 'badge-soft-warning'],
                    'annule' => ['label' => 'Annulé', 'class' => 'badge-soft-danger']
                ];
            @endphp
            <span class="badge {{ $statusLabels[$audit->status]['class'] }} mt-1">
                {{ $statusLabels[$audit->status]['label'] }}
            </span>
        </div>
        
        <div class="card">
            <p class="text-sm text-gray-500">Progression</p>
            <div class="mt-2">
                <div class="flex justify-between text-sm mb-1">
                    <span>{{ $progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 rounded-full h-2" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <p class="text-sm text-gray-500">Taux de conformité</p>
            <div class="mt-2">
                <div class="flex justify-between text-sm mb-1">
                    <span>{{ $conformityRate }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 rounded-full h-2" style="width: {{ $conformityRate }}%"></div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <p class="text-sm text-gray-500">Référentiels</p>
            <div class="flex flex-wrap gap-1 mt-1">
                @foreach($audit->references as $ref)
                    <span class="badge-soft-primary text-xs">{{ $ref->code }}</span>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Description -->
    @if($audit->description)
    <div class="card">
        <h3 class="font-semibold text-gray-900 mb-2">Description</h3>
        <p class="text-gray-600">{{ $audit->description }}</p>
    </div>
    @endif
    
    <!-- Dates -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="card">
            <p class="text-sm text-gray-500">Date de début</p>
            <p class="text-gray-900 font-medium">{{ $audit->date_debut->format('d/m/Y H:i') }}</p>
        </div>
        @if($audit->date_fin)
        <div class="card">
            <p class="text-sm text-gray-500">Date de fin prévue</p>
            <p class="text-gray-900 font-medium">{{ $audit->date_fin->format('d/m/Y H:i') }}</p>
        </div>
        @endif
    </div>
    
    <!-- Section: Clauses ISO 27001 -->
    @if($audit->clauseEvaluations && $audit->clauseEvaluations->count() > 0)
    <div class="card">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Clauses ISO 27001</h2>
            <a href="{{ route('audits.clauses', $audit->id) }}" class="text-sm text-purple-600 hover:text-purple-700">
                Gérer toutes les clauses →
            </a>
        </div>
        <div class="space-y-3">
            @foreach($audit->clauseEvaluations->take(5) as $clauseEval)
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-mono text-sm font-semibold text-purple-600">
                                {{ $clauseEval->clause->numero }}
                            </span>
                            <span class="font-medium text-gray-900">
                                {{ $clauseEval->clause->titre }}
                            </span>
                            @php
                                $statutClasses = [
                                    'non_audite' => 'badge-soft-gray',
                                    'conforme' => 'badge-soft-success',
                                    'non_conforme' => 'badge-soft-danger',
                                    'partiellement_conforme' => 'badge-soft-warning',
                                    'non_applicable' => 'badge-soft-secondary'
                                ];
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $statutClasses[$clauseEval->statut] ?? 'badge-soft-gray' }}">
                                {{ str_replace('_', ' ', $clauseEval->statut) }}
                            </span>
                        </div>
                        @if($clauseEval->justification)
                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($clauseEval->justification, 150) }}</p>
                        @endif
                    </div>
                    <a href="{{ route('audits.clause.edit', [$audit->id, $clauseEval->id]) }}" 
                       class="text-purple-600 hover:text-purple-800 ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
            @if($audit->clauseEvaluations->count() > 5)
            <div class="text-center pt-2">
                <a href="{{ route('audits.clauses', $audit->id) }}" class="text-purple-600 hover:text-purple-700 text-sm">
                    Voir les {{ $audit->clauseEvaluations->count() - 5 }} autres clauses...
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif
    
    <!-- Section: Contrôles ISO 27002 -->
    @if($audit->evaluations && $audit->evaluations->count() > 0)
    <div class="card">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Contrôles ISO 27002</h2>
            <a href="{{ route('audits.controls', $audit->id) }}" class="text-sm text-purple-600 hover:text-purple-700">
                Gérer tous les contrôles →
            </a>
        </div>
        <div class="space-y-3">
            @foreach($audit->evaluations->take(5) as $evaluation)
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-mono text-sm font-semibold text-blue-600">
                                {{ $evaluation->controle->code }}
                            </span>
                            <span class="font-medium text-gray-900">
                                {{ $evaluation->controle->titre }}
                            </span>
                            @php
                                $statutClasses = [
                                    'non_audite' => 'badge-soft-gray',
                                    'conforme' => 'badge-soft-success',
                                    'non_conforme' => 'badge-soft-danger',
                                    'partiellement_conforme' => 'badge-soft-warning',
                                    'non_applicable' => 'badge-soft-secondary'
                                ];
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $statutClasses[$evaluation->statut] ?? 'badge-soft-gray' }}">
                                {{ str_replace('_', ' ', $evaluation->statut) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">{{ Str::limit($evaluation->controle->description, 200) }}</p>
                        @if($evaluation->justification)
                        <p class="text-xs text-gray-500 mt-2">Justification: {{ Str::limit($evaluation->justification, 100) }}</p>
                        @endif
                    </div>
                    <a href="{{ route('audits.control.edit', [$audit->id, $evaluation->id]) }}" 
                       class="text-blue-600 hover:text-blue-800 ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
            @if($audit->evaluations->count() > 5)
            <div class="text-center pt-2">
                <a href="{{ route('audits.controls', $audit->id) }}" class="text-purple-600 hover:text-purple-700 text-sm">
                    Voir les {{ $audit->evaluations->count() - 5 }} autres contrôles...
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif
    
    <!-- Actions rapides -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('controls.iso') }}" class="card text-center hover:shadow-md transition-all">
            <div class="bg-purple-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="font-medium text-gray-900">Contrôles ISO</p>
            <p class="text-sm text-gray-500">Consulter les contrôles</p>
        </a>
        
        <a href="{{ route('evidence.collect') }}" class="card text-center hover:shadow-md transition-all">
            <div class="bg-blue-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <p class="font-medium text-gray-900">Collecte de preuves</p>
            <p class="text-sm text-gray-500">Consulter les preuves</p>
        </a>
        
        <a href="{{ route('evaluation.index') }}" class="card text-center hover:shadow-md transition-all">
            <div class="bg-green-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="font-medium text-gray-900">Évaluation</p>
            <p class="text-sm text-gray-500">Évaluer la conformité</p>
        </a>
        
        <a href="{{ route('report.index') }}" class="card text-center hover:shadow-md transition-all">
            <div class="bg-orange-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="font-medium text-gray-900">Rapport</p>
            <p class="text-sm text-gray-500">Générer le rapport</p>
        </a>
    </div>
</div>
@endsection