@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-4 md:gap-6">
    <!-- Welcome Section -->
    <div class="col-span-12">
        <div class="bg-gradient-to-r from-iso-red to-iso-red-dark rounded-xl p-6 text-white">
            <h1 class="text-2xl font-bold mb-2">Bienvenue sur le Simulateur d'Audit ISO 2700x</h1>
            <p class="text-white/80">Préparez-vous à l'audit de conformité avec des scénarios réalistes et des contrôles ISO standards.</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-span-12 md:col-span-3">
        <div class="card lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Audits totaux</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_audits'] }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-iso-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-12 md:col-span-3">
        <div class="card lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Audits en cours</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['in_progress_audits'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-12 md:col-span-3">
        <div class="card lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Taux de conformité</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['conformity_rate'] }}%</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-12 md:col-span-3">
        <div class="card lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Contrôles ISO</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_controls'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Audit Section -->
    @if($currentAudit)
    <div class="col-span-12">
        <div class="card">
            <h2 class="text-lg font-semibold mb-4">Audit en cours</h2>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $currentAudit->titre }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $currentAudit->entreprise->nom }}</p>
                    </div>
                    <span class="badge badge-primary">En cours</span>
                </div>
                <div class="mt-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span>Progression</span>
                        <span>{{ $progress ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-iso-red rounded-full h-2 transition-all duration-500" style="width: {{ $progress ?? 0 }}%"></div>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('audit.current') }}" class="btn btn-primary btn-sm">Continuer l'audit</a>
                    <a href="{{ route('evaluation.index') }}" class="btn btn-outline btn-sm">Évaluer les contrôles</a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Audits -->
    <div class="col-span-12 lg:col-span-6">
        <div class="card">
            <h2 class="text-lg font-semibold mb-4">Audits récents</h2>
            <div class="space-y-3">
                @forelse($stats['recent_audits'] as $audit)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div>
                        <p class="font-medium text-gray-900">{{ $audit->titre }}</p>
                        <p class="text-sm text-gray-600">{{ $audit->entreprise->nom }} • {{ $audit->created_at->format('d/m/Y') }}</p>
                    </div>
                    <span class="badge {{ $audit->status === 'termine' ? 'badge-soft-success' : 'badge-soft-primary' }}">
                        {{ $audit->status === 'termine' ? 'Terminé' : 'En cours' }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Aucun audit récent</p>
                @endforelse
            </div>
            <div class="mt-4">
                <a href="{{ route('audit.history') }}" class="text-iso-red hover:text-iso-red-dark text-sm font-medium">Voir tout l'historique →</a>
            </div>
        </div>
    </div>

    <!-- Controls Status -->
    <div class="col-span-12 lg:col-span-6">
        <div class="card">
            <h2 class="text-lg font-semibold mb-4">Statut des contrôles</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Conformes</span>
                    <div class="flex items-center gap-2">
                        <div class="w-48 bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 rounded-full h-2" style="width: {{ $stats['controls_status']['conforme'] > 0 ? ($stats['controls_status']['conforme'] / max(array_sum($stats['controls_status']), 1) * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-medium">{{ $stats['controls_status']['conforme'] }}</span>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Non conformes</span>
                    <div class="flex items-center gap-2">
                        <div class="w-48 bg-gray-200 rounded-full h-2">
                            <div class="bg-red-500 rounded-full h-2" style="width: {{ $stats['controls_status']['non_conforme'] > 0 ? ($stats['controls_status']['non_conforme'] / max(array_sum($stats['controls_status']), 1) * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-medium">{{ $stats['controls_status']['non_conforme'] }}</span>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Partiellement conformes</span>
                    <div class="flex items-center gap-2">
                        <div class="w-48 bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-500 rounded-full h-2" style="width: {{ $stats['controls_status']['partiellement_conforme'] > 0 ? ($stats['controls_status']['partiellement_conforme'] / max(array_sum($stats['controls_status']), 1) * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-medium">{{ $stats['controls_status']['partiellement_conforme'] }}</span>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('controls.iso') }}" class="text-iso-red hover:text-iso-red-dark text-sm font-medium">Voir tous les contrôles →</a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-span-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('controls.iso') }}" class="card text-center hover:shadow-md transition-all">
                <div class="bg-red-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-iso-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            
            <a href="{{ route('report.index') }}" class="card text-center hover:shadow-md transition-all">
                <div class="bg-green-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="font-medium text-gray-900">Rapports</p>
                <p class="text-sm text-gray-500">Générer des rapports</p>
            </a>
            
            <a href="{{ route('statistics.index') }}" class="card text-center hover:shadow-md transition-all">
                <div class="bg-purple-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <p class="font-medium text-gray-900">Statistiques</p>
                <p class="text-sm text-gray-500">Analyser la conformité</p>
            </a>
        </div>
    </div>
</div>
@endsection