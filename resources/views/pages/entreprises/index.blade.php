@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Entreprises</h1>
            <p class="text-sm text-gray-500 mt-1">Gérez votre portefeuille d'entreprises</p>
        </div>
        <a href="{{ route('entreprises.create') }}" class="btn btn-primary inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nouvelle entreprise
        </a>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total entreprises</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Secteurs d'activité</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['secteurs'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Effectif total</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['effectif_total']) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Entreprises actives</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['actives'] }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search and Filter -->
    <div class="card">
        <form method="GET" action="{{ route('entreprises.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Rechercher par nom..." 
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div class="w-full sm:w-48">
                <select name="secteur" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">Tous les secteurs</option>
                    <option value="Technologie" {{ request('secteur') == 'Technologie' ? 'selected' : '' }}>Technologie</option>
                    <option value="Finance" {{ request('secteur') == 'Finance' ? 'selected' : '' }}>Finance</option>
                    <option value="Santé" {{ request('secteur') == 'Santé' ? 'selected' : '' }}>Santé</option>
                    <option value="Industrie" {{ request('secteur') == 'Industrie' ? 'selected' : '' }}>Industrie</option>
                    <option value="Commerce" {{ request('secteur') == 'Commerce' ? 'selected' : '' }}>Commerce</option>
                </select>
            </div>
            <div class="w-full sm:w-48">
                <select name="taille" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">Toutes les tailles</option>
                    <option value="TPE" {{ request('taille') == 'TPE' ? 'selected' : '' }}>TPE</option>
                    <option value="PME" {{ request('taille') == 'PME' ? 'selected' : '' }}>PME</option>
                    <option value="ETI" {{ request('taille') == 'ETI' ? 'selected' : '' }}>ETI</option>
                    <option value="GE" {{ request('taille') == 'GE' ? 'selected' : '' }}>Grande Entreprise</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">
                Filtrer
            </button>
        </form>
    </div>
    
    <!-- Entreprises List -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Logo</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Nom</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Secteur</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Taille</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Localisation</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Effectif</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Statut</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entreprises as $entreprise)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4">
                            @if($entreprise->image_logo)
                                <img src="{{ asset('storage/' . $entreprise->image_logo) }}" alt="{{ $entreprise->nom }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ $entreprise->nom }}</p>
                                @if($entreprise->chiffre_affaires)
                                    <p class="text-xs text-gray-500">CA: {{ $entreprise->chiffre_affaires }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-sm text-gray-700">{{ $entreprise->secteur }}</span>
                        </td>
                        <td class="py-3 px-4">
                            @php
                                $tailleLabels = [
                                    'TPE' => ['label' => 'TPE', 'class' => 'badge-soft-info'],
                                    'PME' => ['label' => 'PME', 'class' => 'badge-soft-primary'],
                                    'ETI' => ['label' => 'ETI', 'class' => 'badge-soft-warning'],
                                    'GE' => ['label' => 'Grande Entreprise', 'class' => 'badge-soft-success']
                                ];
                            @endphp
                            <span class="badge {{ $tailleLabels[$entreprise->taille]['class'] }}">
                                {{ $tailleLabels[$entreprise->taille]['label'] }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-sm text-gray-700">{{ $entreprise->localisation ?? 'Non spécifiée' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-sm text-gray-700">{{ $entreprise->effectif ?? 'N/C' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            @if($entreprise->is_active)
                                <span class="badge badge-soft-success">Active</span>
                            @else
                                <span class="badge badge-soft-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('entreprises.show', $entreprise->id) }}" class="text-gray-600 hover:text-gray-800" title="Voir détails">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('entreprises.edit', $entreprise->id) }}" class="text-blue-600 hover:text-blue-800" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('entreprises.destroy', $entreprise->id) }}" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer l\'entreprise "{{ $entreprise->nom }}" ? Cette action est irréversible et supprimera tous les audits associés.')" 
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <p>Aucune entreprise trouvée</p>
                            <a href="{{ route('entreprises.create') }}" class="text-purple-600 hover:text-purple-700 text-sm mt-2 inline-block">
                                Créer votre première entreprise
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $entreprises->links() }}
        </div>
    </div>
</div>
@endsection