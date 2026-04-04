@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-8 uppercase tracking-widest border-b-2 border-blue-600 pb-2">
         Tableau de Bord des Statistiques d'Audit
    </h1>

    {{-- 1. Cartes de Score Rapides --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
            <p class="text-sm text-gray-500 uppercase font-bold">Total Missions</p>
            <p class="text-3xl font-black text-gray-800">{{ $stats['total_audits'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500">
            <p class="text-sm text-gray-500 uppercase font-bold">Conformité Globale</p>
            <p class="text-3xl font-black text-green-600">{{ $stats['global_conformity_rate'] }}%</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-500">
            <p class="text-sm text-gray-500 uppercase font-bold">Contrôles Évalués</p>
            <p class="text-3xl font-black text-gray-800">{{ $stats['total_controls'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500">
            <p class="text-sm text-gray-500 uppercase font-bold">Non-Conformités</p>
            <p class="text-3xl font-black text-red-600">{{ $stats['top_non_conformities']->sum('total') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- 2. Graphique : Répartition des Statuts --}}
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Répartition par Statut d'Audit</h3>
            <canvas id="statusChart" height="200"></canvas>
        </div>

        {{-- 3. Liste : Top 5 des failles récurrentes --}}
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="text-lg font-bold text-gray-700 mb-4 text-red-600">🚨 Top 5 des Non-Conformités</h3>
            <div class="space-y-4">
                @forelse($stats['top_non_conformities'] as $nc)
                <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700 truncate w-64">
                        {{ $nc->controle->titre ?? 'Contrôle #'.$nc->controle_id }}
                    </span>
                    <span class="bg-red-600 text-white text-xs px-2 py-1 rounded-full font-bold">
                        {{ $nc->total }} fois
                    </span>
                </div>
                @empty
                <p class="text-gray-400 italic">Aucune non-conformité détectée.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Script pour les graphiques --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique des statuts (Ex: En cours, Terminé)
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($stats['audits_by_status']->keys()) !!},
            datasets: [{
                data: {!! json_encode($stats['audits_by_status']->values()) !!},
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection