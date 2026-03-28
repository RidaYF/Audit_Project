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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card">
            <p class="text-sm text-gray-500">Risques critiques</p>
            <p class="text-2xl font-bold text-red-600">{{ $risks->where('niveau_risque_calcule', 'critique')->count() }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Risques élevés</p>
            <p class="text-2xl font-bold text-orange-600">{{ $risks->where('niveau_risque_calcule', 'eleve')->count() }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Total non-conformités</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $risks->count() }}</p>
        </div>
    </div>

    @forelse($risks as $risk)
        @php
            $control = $risk->controle;
            $riskLevelColors = [
                'critique' => 'bg-red-100 text-red-700 border-red-200',
                'eleve' => 'bg-orange-100 text-orange-700 border-orange-200',
                'moyen' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                'faible' => 'bg-green-100 text-green-700 border-green-200'
            ];
        @endphp
        <div class="card border-l-4 {{ $riskLevelColors[$risk->niveau_risque_calcule] ?? 'border-gray-300' }}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-mono text-sm font-semibold text-purple-600">{{ $control->code }}</span>
                        <span class="badge {{ $riskLevelColors[$risk->niveau_risque_calcule] ?? 'bg-gray-100' }}">
                            {{ ucfirst($risk->niveau_risque_calcule ?? 'Non évalué') }}
                        </span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $control->titre }}</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ $control->description }}</p>
                    
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <p class="text-xs text-gray-500">Gravité</p>
                            <p class="text-sm font-medium">{{ ucfirst($risk->gravite_risque ?? 'Non définie') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Probabilité</p>
                            <p class="text-sm font-medium">{{ ucfirst($risk->probabilite_risque ?? 'Non définie') }}</p>
                        </div>
                    </div>
                    
                    @if($risk->justification)
                        <div class="bg-gray-50 rounded-lg p-3 mb-3">
                            <p class="text-xs text-gray-500 mb-1">Justification:</p>
                            <p class="text-sm text-gray-700">{{ $risk->justification }}</p>
                        </div>
                    @endif
                    
                    @if($risk->recommandation)
                        <div class="bg-blue-50 rounded-lg p-3">
                            <p class="text-xs text-blue-600 mb-1">Recommandation:</p>
                            <p class="text-sm text-blue-800">{{ $risk->recommandation }}</p>
                        </div>
                    @endif
                </div>
                <div class="ml-4">
                    <a href="{{ route('evaluation.index') }}?controle={{ $control->id }}" 
                       class="btn btn-outline-primary btn-sm">
                        Traiter le risque
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="card text-center py-8">
            <svg class="w-12 h-12 mx-auto text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500">Aucune non-conformité détectée pour le moment</p>
            <p class="text-sm text-gray-400 mt-1">Continuez l'évaluation des contrôles</p>
        </div>
    @endforelse
</div>
@endsection