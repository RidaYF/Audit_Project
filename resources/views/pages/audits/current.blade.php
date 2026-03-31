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
            <a href="{{ route('evaluation.index') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                Continuer l'évaluation
            </a>
        </div>
    </div>

    <!-- Progress Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Progression globale</p>
            <div class="mt-2">
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-gray-900">{{ $progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 rounded-full h-2 transition-all duration-500" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Taux de conformité</p>
            <div class="mt-2">
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-gray-900">{{ $conformityRate }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 rounded-full h-2 transition-all duration-500" style="width: {{ $conformityRate }}%"></div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Contrôles évalués</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">
                {{ $audit->evaluations->where('statut', '!=', 'non_audite')->count() }}
                <span class="text-sm font-normal text-gray-500">/ {{ $audit->evaluations->count() }}</span>
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Référentiels</p>
            <div class="flex flex-wrap gap-1 mt-2">
                @foreach($audit->references as $ref)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                        {{ $ref->code }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- SECTION CLAUSES ISO 27001 -->
    <!-- ============================================ -->
    @if($audit->clauseEvaluations && $audit->clauseEvaluations->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Clauses ISO 27001 - SMSI
            </h2>
            <a href="{{ route('audits.clauses', $audit->id) }}" class="text-sm text-purple-600 hover:text-purple-700">
                Voir toutes les clauses →
            </a>
        </div>
        <p class="text-sm text-gray-500 mb-4">Évaluation des exigences du Système de Management de la Sécurité de l'Information</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($audit->clauseEvaluations->take(4) as $clauseEval)
                @php
                    $clause = $clauseEval->clause;
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
                <div class="border rounded-lg p-4 hover:shadow-md transition-all">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <span class="font-mono text-lg font-bold text-purple-600">{{ $clause->numero }}</span>
                            <h3 class="font-semibold text-gray-900 mt-1">{{ $clause->titre }}</h3>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full {{ $statusColors[$clauseEval->statut] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $statusLabels[$clauseEval->statut] ?? $clauseEval->statut }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ Str::limit($clause->description, 120) }}</p>
                    @if($clauseEval->score > 0)
                        <div class="mt-3">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-500">Score de conformité</span>
                                <span class="font-medium text-purple-600">{{ $clauseEval->score }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-purple-600 rounded-full h-1.5" style="width: {{ $clauseEval->score }}%"></div>
                            </div>
                        </div>
                    @endif
                    <div class="mt-3">
                        <a href="{{ route('audits.clause.edit', [$audit->id, $clauseEval->id]) }}" 
                           class="text-sm text-purple-600 hover:text-purple-700">
                            Évaluer cette clause →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($audit->clauseEvaluations->count() > 4)
        <div class="text-center mt-4 pt-2">
            <a href="{{ route('audits.clauses', $audit->id) }}" class="text-purple-600 hover:text-purple-700 text-sm">
                Voir les {{ $audit->clauseEvaluations->count() - 4 }} autres clauses...
            </a>
        </div>
        @endif
    </div>
    @endif

    <!-- ============================================ -->
    <!-- SECTION CONTRÔLES ISO 27002 -->
    <!-- ============================================ -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Contrôles de sécurité - ISO 27002
            </h2>
            <a href="{{ route('audits.controls', $audit->id) }}" class="text-sm text-purple-600 hover:text-purple-700">
                Voir tous les contrôles →
            </a>
        </div>
        <p class="text-sm text-gray-500 mb-4">Évaluation des mesures de sécurité de l'information</p>
        
        @forelse($controlsByCategory as $category => $evaluations)
            <div class="mb-6 last:mb-0">
                <h3 class="font-medium text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    {{ $category ?: 'Autres contrôles' }}
                    <span class="text-xs text-gray-500 font-normal">
                        ({{ $evaluations->count() }} contrôles)
                    </span>
                </h3>
                <div class="space-y-2">
                    @foreach($evaluations->take(3) as $evaluation)
                        @php
                            $control = $evaluation->controle;
                            $statusColors = [
                                'conforme' => 'bg-green-100 text-green-700 border-green-200',
                                'non_conforme' => 'bg-red-100 text-red-700 border-red-200',
                                'partiellement_conforme' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                'non_applicable' => 'bg-gray-100 text-gray-700 border-gray-200',
                                'non_audite' => 'bg-blue-100 text-blue-700 border-blue-200'
                            ];
                            $statusLabels = [
                                'conforme' => 'Conforme',
                                'non_conforme' => 'Non conforme',
                                'partiellement_conforme' => 'Partiellement conforme',
                                'non_applicable' => 'Non applicable',
                                'non_audite' => 'Non audité'
                            ];
                        @endphp
                        <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-mono text-sm font-semibold text-blue-600">{{ $control->code }}</span>
                                    <span class="font-medium text-gray-900">{{ $control->titre }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ Str::limit($control->description, 120) }}</p>
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColors[$evaluation->statut] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $statusLabels[$evaluation->statut] ?? $evaluation->statut }}
                                    </span>
                                    @if($control->niveau_critique)
                                        <span class="text-xs px-2 py-0.5 rounded-full 
                                            {{ $control->niveau_critique === 'critique' ? 'bg-red-100 text-red-700' : 
                                               ($control->niveau_critique === 'eleve' ? 'bg-orange-100 text-orange-700' : 
                                               ($control->niveau_critique === 'moyen' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700')) }}">
                                            {{ strtoupper($control->niveau_critique) }}
                                        </span>
                                    @endif
                                    @if($evaluation->points_obtenus > 0)
                                        <span class="text-xs text-gray-500">
                                            Points: {{ $evaluation->points_obtenus }}/{{ $control->ponderation_defaut ?? 1 }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4">
                                <a href="{{ route('audits.control.edit', [$audit->id, $evaluation->id]) }}" 
                                   class="px-3 py-1 text-sm border border-blue-300 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                                    Évaluer
                                </a>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($evaluations->count() > 3)
                        <div class="text-center pt-2">
                            <a href="{{ route('audits.controls', $audit->id) }}?category={{ urlencode($category) }}" 
                               class="text-sm text-blue-600 hover:text-blue-700">
                                Voir les {{ $evaluations->count() - 3 }} autres contrôles de cette catégorie...
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p>Aucun contrôle disponible pour cet audit.</p>
            </div>
        @endforelse
    </div>

    <!-- ============================================ -->
    <!-- SECTION MÉTHODES ISO 27005 (RISQUES) -->
    <!-- ============================================ -->
    @if(isset($riskMethods) && $riskMethods->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Méthodes d'analyse des risques - ISO 27005
            </h2>
            <a href="{{ route('risk.management') }}" class="text-sm text-purple-600 hover:text-purple-700">
                Gérer les risques →
            </a>
        </div>
        <p class="text-sm text-gray-500 mb-4">Méthodologies pour l'évaluation et le traitement des risques</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($riskMethods->take(4) as $riskMethod)
                @php
                    $risk = $riskMethod->controle ?? $riskMethod;
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
                <div class="border rounded-lg p-4 hover:shadow-md transition-all">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <span class="font-mono text-sm font-bold text-orange-600">{{ $risk->code }}</span>
                            <h3 class="font-semibold text-gray-900 mt-1">{{ $risk->titre }}</h3>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full {{ $statusColors[$riskMethod->statut] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $statusLabels[$riskMethod->statut] ?? $riskMethod->statut }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ Str::limit($risk->description, 120) }}</p>
                    <div class="mt-3">
                        <a href="{{ route('risk.management') }}?risk={{ $risk->id }}" 
                           class="text-sm text-orange-600 hover:text-orange-700">
                            Évaluer cette méthode →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Evidence -->
    @if($recentEvidences->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Preuves récemment consultées</h2>
        <div class="space-y-2">
            @foreach($recentEvidences as $evidence)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $evidence['titre'] }}</p>
                        <p class="text-xs text-gray-500">Contrôle {{ $evidence['controle'] }}</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($evidence['date'])->diffForHumans() }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('controls.iso') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center hover:shadow-md transition-all">
            <div class="bg-purple-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="font-medium text-gray-900">Contrôles ISO</p>
            <p class="text-sm text-gray-500">Consulter les contrôles</p>
        </a>
        
        <a href="{{ route('evidence.collect') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center hover:shadow-md transition-all">
            <div class="bg-blue-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <p class="font-medium text-gray-900">Collecte de preuves</p>
            <p class="text-sm text-gray-500">Consulter les preuves</p>
        </a>
        
        <a href="{{ route('evaluation.index') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center hover:shadow-md transition-all">
            <div class="bg-green-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="font-medium text-gray-900">Évaluation</p>
            <p class="text-sm text-gray-500">Évaluer la conformité</p>
        </a>
        
        <a href="{{ route('risk.management') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center hover:shadow-md transition-all">
            <div class="bg-orange-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <p class="font-medium text-gray-900">Risques</p>
            <p class="text-sm text-gray-500">Gestion des risques</p>
        </a>
    </div>
</div>
@endsection