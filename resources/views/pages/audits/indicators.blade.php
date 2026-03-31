@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('audit.current') }}" class="text-sm text-purple-600 hover:text-purple-700 mb-2 inline-block">
                ← Retour à l'audit
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Indicateurs de contrôle</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $audit->titre }} - {{ $audit->entreprise->nom }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('audit.indicators') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                Rafraîchir
            </a>
        </div>
    </div>

    <!-- Cartes KPI principales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 text-white">
            <p class="text-sm opacity-90">Score global</p>
            <p class="text-3xl font-bold">{{ $scoreTotal }}%</p>
            <div class="mt-2 w-full bg-blue-400 rounded-full h-2">
                <div class="bg-white rounded-full h-2" style="width: {{ $scoreTotal }}%"></div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-4 text-white">
            <p class="text-sm opacity-90">Taux de conformité</p>
            <p class="text-3xl font-bold">{{ $conformityRate }}%</p>
            <p class="text-xs mt-1">{{ $controlsByStatus['conforme'] }} / {{ $totalControls }} contrôles</p>
        </div>
        
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-4 text-white">
            <p class="text-sm opacity-90">Progression</p>
            <p class="text-3xl font-bold">{{ $progressRate }}%</p>
            <p class="text-xs mt-1">{{ $totalControls - $controlsByStatus['non_audite'] }} / {{ $totalControls }} évalués</p>
        </div>
        
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-4 text-white">
            <p class="text-sm opacity-90">Non-conformités</p>
            <p class="text-3xl font-bold">{{ $controlsByStatus['non_conforme'] }}</p>
            <p class="text-xs mt-1">{{ $controlsByRisk['critique'] }} critiques</p>
        </div>
    </div>

    <!-- Statistiques par statut -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Statut des contrôles -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">État des contrôles</h2>
            <div class="space-y-3">
                @foreach($controlsByStatus as $status => $count)
                    @php
                        $percentage = $totalControls > 0 ? round(($count / $totalControls) * 100, 1) : 0;
                        $colors = [
                            'non_audite' => 'bg-gray-500',
                            'conforme' => 'bg-green-500',
                            'non_conforme' => 'bg-red-500',
                            'partiellement_conforme' => 'bg-yellow-500',
                            'non_applicable' => 'bg-blue-500'
                        ];
                        $labels = [
                            'non_audite' => 'Non audité',
                            'conforme' => 'Conforme',
                            'non_conforme' => 'Non conforme',
                            'partiellement_conforme' => 'Partiellement conforme',
                            'non_applicable' => 'Non applicable'
                        ];
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ $labels[$status] }}</span>
                            <span class="font-medium">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="{{ $colors[$status] }} rounded-full h-2" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Niveaux de risque -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Niveaux de risque</h2>
            <div class="space-y-3">
                @foreach($controlsByRisk as $risk => $count)
                    @php
                        $percentage = $totalControls > 0 ? round(($count / $totalControls) * 100, 1) : 0;
                        $colors = [
                            'critique' => 'bg-red-500',
                            'eleve' => 'bg-orange-500',
                            'moyen' => 'bg-yellow-500',
                            'faible' => 'bg-green-500',
                            'non_evalue' => 'bg-gray-500'
                        ];
                        $labels = [
                            'critique' => 'Critique',
                            'eleve' => 'Élevé',
                            'moyen' => 'Moyen',
                            'faible' => 'Faible',
                            'non_evalue' => 'Non évalué'
                        ];
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ $labels[$risk] }}</span>
                            <span class="font-medium">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="{{ $colors[$risk] }} rounded-full h-2" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Scores par catégorie -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Scores par catégorie</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($scoreByCategory as $category => $score)
                <div class="border rounded-lg p-4">
                    <p class="text-sm text-gray-500">{{ $category }}</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $score }}%</p>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-purple-600 rounded-full h-1.5" style="width: {{ $score }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Détails par catégorie -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Détail par catégorie de contrôle</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Conformes</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Non-conformes</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Taux</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($controlsByCategory as $category => $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $category }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-600">{{ $data['total'] }}</td>
                            <td class="px-6 py-4 text-sm text-center text-green-600">{{ $data['conformes'] }}</td>
                            <td class="px-6 py-4 text-sm text-center text-red-600">{{ $data['non_conformes'] }}</td>
                            <td class="px-6 py-4 text-sm text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-600 rounded-full h-2" style="width: {{ $data['taux_conformite'] }}%"></div>
                                    </div>
                                    <span class="font-medium">{{ $data['taux_conformite'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Clauses ISO 27001 -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Clauses ISO 27001</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-2">État des clauses</p>
                <div class="space-y-3">
                    @foreach($clausesByStatus as $status => $count)
                        @php
                            $percentage = $totalClauses > 0 ? round(($count / $totalClauses) * 100, 1) : 0;
                            $colors = [
                                'non_audite' => 'bg-gray-500',
                                'conforme' => 'bg-green-500',
                                'non_conforme' => 'bg-red-500',
                                'partiellement_conforme' => 'bg-yellow-500',
                                'non_applicable' => 'bg-blue-500'
                            ];
                            $labels = [
                                'non_audite' => 'Non audité',
                                'conforme' => 'Conforme',
                                'non_conforme' => 'Non conforme',
                                'partiellement_conforme' => 'Partiellement conforme',
                                'non_applicable' => 'Non applicable'
                            ];
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ $labels[$status] }}</span>
                                <span class="font-medium">{{ $count }} ({{ $percentage }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $colors[$status] }} rounded-full h-2" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center justify-center">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-32 h-32 rounded-full border-8 border-purple-200">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-purple-600">{{ $clauseConformityRate }}%</p>
                            <p class="text-xs text-gray-500">Conformité</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-gray-600">
                        {{ $clausesByStatus['conforme'] }} clauses conformes sur {{ $totalClauses }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chronologie de l'audit -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Chronologie</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Date de début</p>
                    <p class="font-medium">{{ $audit->date_debut->format('d/m/Y') }}</p>
                    <p class="text-xs text-gray-400">Il y a {{ $daysSinceStart }} jours</p>
                </div>
            </div>
            @if($daysRemaining !== null)
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full {{ $daysRemaining > 0 ? 'bg-orange-100' : 'bg-red-100' }} flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $daysRemaining > 0 ? 'text-orange-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Date de fin prévue</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($audit->date_fin)->format('d/m/Y') }}</p>
                    @if($daysRemaining > 0)
                        <p class="text-xs text-green-600">Plus que {{ $daysRemaining }} jours</p>
                    @else
                        <p class="text-xs text-red-600">Dépassé de {{ abs($daysRemaining) }} jours</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection