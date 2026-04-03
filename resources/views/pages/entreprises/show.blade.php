@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                @if($entreprise->image_logo)
                    <img src="{{ asset('storage/' . $entreprise->image_logo) }}" alt="{{ $entreprise->nom }}" class="w-12 h-12 rounded-full object-cover">
                @else
                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $entreprise->nom }}</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $entreprise->secteur }} • 
                        @php
                            $tailleLabels = ['TPE' => 'TPE', 'PME' => 'PME', 'ETI' => 'ETI', 'GE' => 'Grande Entreprise'];
                        @endphp
                        {{ $tailleLabels[$entreprise->taille] }}
                        @if($entreprise->localisation)
                             • {{ $entreprise->localisation }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('entreprises.edit', $entreprise->id) }}" class="btn btn-secondary inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Modifier
            </a>
            <a href="{{ route('entreprises.index') }}" class="btn btn-secondary inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Chiffre d'affaires</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $entreprise->chiffre_affaires ?? 'Non renseigné' }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Effectif</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $entreprise->effectif ? number_format($entreprise->effectif) : 'Non renseigné' }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Statut</p>
                    <p class="text-2xl font-bold {{ $entreprise->is_active ? 'text-green-600' : 'text-red-600' }}">
                        {{ $entreprise->is_active ? 'Active' : 'Inactive' }}
                    </p>
                </div>
                <div class="{{ $entreprise->is_active ? 'bg-green-100' : 'bg-red-100' }} rounded-full p-3">
                    @if($entreprise->is_active)
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Créée le</p>
                    <p class="text-lg font-bold text-gray-900">{{ $entreprise->created_at->format('d/m/Y') }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Description détaillée -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Colonne gauche -->
        <div class="space-y-6">
            @if($entreprise->contexte_metier)
                <div class="card">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Contexte métier</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($entreprise->contexte_metier)) !!}
                    </div>
                </div>
            @endif
            
            @if($entreprise->systemes_cles)
                <div class="card">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Systèmes clés</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($entreprise->systemes_cles)) !!}
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Colonne droite -->
        <div class="space-y-6">
            @if($entreprise->departements)
                <div class="card">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Départements</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($entreprise->departements)) !!}
                    </div>
                </div>
            @endif
            
            @if($entreprise->actifs_critiques)
                <div class="card">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Actifs critiques</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($entreprise->actifs_critiques)) !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Audits associés -->
    @if(isset($audits) && $audits->count() > 0)
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Audits associés</h3>
                <a href="{{ route('audits.create', ['entreprise_id' => $entreprise->id]) }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                    + Nouvel audit
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 px-3 text-sm font-semibold text-gray-600">Titre</th>
                            <th class="text-left py-2 px-3 text-sm font-semibold text-gray-600">Date début</th>
                            <th class="text-left py-2 px-3 text-sm font-semibold text-gray-600">Statut</th>
                            <th class="text-left py-2 px-3 text-sm font-semibold text-gray-600">Score</th>
                            <th class="text-right py-2 px-3 text-sm font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($audits as $audit)
                        <tr class="border-b border-gray-100">
                            <td class="py-2 px-3 text-sm text-gray-700">{{ $audit->titre }}</td>
                            <td class="py-2 px-3 text-sm text-gray-700">{{ $audit->date_debut->format('d/m/Y') }}</td>
                            <td class="py-2 px-3">
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
                            <td class="py-2 px-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium">{{ $audit->score_total ?? 0 }}%</span>
                                    <div class="flex-1 max-w-20">
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-purple-600 rounded-full h-1.5" style="width: {{ $audit->score_total ?? 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2 px-3 text-right">
                                <a href="{{ route('audits.show', $audit->id) }}" class="text-gray-600 hover:text-gray-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    
    <!-- Informations système -->
    <div class="text-xs text-gray-500 text-right">
        Dernière modification : {{ $entreprise->updated_at->format('d/m/Y H:i') }}
        @if($entreprise->deleted_at)
            <br>Supprimée le : {{ $entreprise->deleted_at->format('d/m/Y H:i') }}
        @endif
    </div>
</div>
@endsection