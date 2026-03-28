@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mes audits</h1>
            <p class="text-sm text-gray-500 mt-1">Gérez vos audits de conformité ISO 2700x</p>
        </div>
        <a href="{{ route('audits.create') }}" class="btn btn-primary inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nouvel audit
        </a>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total audits</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">En cours</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['en_cours'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Terminés</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['termine'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Suspendus/Annulés</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['suspendu'] + $stats['annule'] }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Audits List -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Titre</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Entreprise</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Date début</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Statut</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Score</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($audits as $audit)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ $audit->titre }}</p>
                                <p class="text-xs text-gray-500">{{ $audit->references->pluck('code')->implode(', ') }}</p>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-sm text-gray-700">{{ $audit->entreprise->nom }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-sm text-gray-700">{{ $audit->date_debut->format('d/m/Y') }}</span>
                        </td>
                        <td class="py-3 px-4">
                            @php
                                $statusLabels = [
                                    'en_cours' => ['label' => 'En cours', 'class' => 'badge-soft-primary'],
                                    'termine' => ['label' => 'Terminé', 'class' => 'badge-soft-success'],
                                    'suspendu' => ['label' => 'Suspendu', 'class' => 'badge-soft-warning'],
                                    'annule' => ['label' => 'Annulé', 'class' => 'badge-soft-danger']
                                ];
                            @endphp
                            <span class="badge {{ $statusLabels[$audit->status]['class'] }}">
                                {{ $statusLabels[$audit->status]['label'] }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium">{{ $audit->score_total ?? 0 }}%</span>
                                <div class="flex-1 max-w-20">
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-purple-600 rounded-full h-1.5" style="width: {{ $audit->score_total ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($audit->status === 'en_cours')
                                    <a href="{{ route('audit.current') }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                                        Continuer
                                    </a>
                                @endif
                                <a href="{{ route('audits.show', $audit->id) }}" class="text-gray-600 hover:text-gray-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p>Aucun audit trouvé</p>
                            <a href="{{ route('audits.create') }}" class="text-purple-600 hover:text-purple-700 text-sm mt-2 inline-block">
                                Créer votre premier audit
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $audits->links() }}
        </div>
    </div>
</div>
@endsection