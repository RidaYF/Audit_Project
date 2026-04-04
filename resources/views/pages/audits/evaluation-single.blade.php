@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('evaluation.index') }}" class="text-sm text-purple-600 hover:text-purple-700 mb-2 inline-block">
                ← Retour à la liste des contrôles
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Évaluation du contrôle</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $audit->titre }} - {{ $audit->entreprise->nom }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('evidence.collect') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                📎 Voir les preuves
            </a>
        </div>
    </div>

    <!-- Information du contrôle -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-50 to-blue-50 px-6 py-4 border-b">
            <div class="flex items-center justify-between">
                <div>
                    <span class="font-mono text-lg font-bold text-purple-600">{{ $control->code }}</span>
                    <h2 class="text-xl font-bold text-gray-900 mt-1">{{ $control->titre }}</h2>
                </div>
                <div class="text-right">
                    <span class="text-sm text-gray-500">Catégorie</span>
                    <p class="font-medium">{{ $control->categorie ?? 'Non classé' }}</p>
                </div>
            </div>
        </div>
        
        <div class="p-6 space-y-6">
            <!-- Description -->
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Description</h3>
                <p class="text-gray-700">{{ $control->description }}</p>
            </div>
            
            <!-- Objectif -->
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Objectif</h3>
                <p class="text-gray-700">{{ $control->objectif }}</p>
            </div>
            
            <!-- Informations supplémentaires -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Niveau critique</p>
                    <p class="font-semibold 
                        @if($control->niveau_critique == 'critique') text-red-600
                        @elseif($control->niveau_critique == 'eleve') text-orange-600
                        @elseif($control->niveau_critique == 'moyen') text-yellow-600
                        @else text-green-600
                        @endif">
                        {{ ucfirst($control->niveau_critique) }}
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Type de contrôle</p>
                    <p class="font-semibold text-gray-900">{{ ucfirst($control->type_controle) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Pondération</p>
                    <p class="font-semibold text-gray-900">{{ $control->ponderation_defaut }} points</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Temps estimé</p>
                    <p class="font-semibold text-gray-900">{{ $control->temps_estime ?? 'Non défini' }} min</p>
                </div>
            </div>
            
            <!-- Conseils d'audit -->
            @if($control->conseils_audit)
            <div class="bg-blue-50 rounded-lg p-4">
                <h3 class="font-semibold text-blue-800 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Conseils pour l'audit
                </h3>
                <p class="text-blue-700">{{ $control->conseils_audit }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Formulaire d'évaluation -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Formulaire d'évaluation</h2>
        </div>
        
        <form method="POST" action="{{ route('audit.evaluation.update', $evaluation->id) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Statut -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut de conformité *</label>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="statut" value="non_audite" {{ $evaluation->statut == 'non_audite' ? 'checked' : '' }}>
                        <span class="text-sm">Non audité</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-green-50">
                        <input type="radio" name="statut" value="conforme" {{ $evaluation->statut == 'conforme' ? 'checked' : '' }}>
                        <span class="text-sm text-green-700">Conforme ✅</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-red-50">
                        <input type="radio" name="statut" value="non_conforme" {{ $evaluation->statut == 'non_conforme' ? 'checked' : '' }}>
                        <span class="text-sm text-red-700">Non conforme ❌</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-yellow-50">
                        <input type="radio" name="statut" value="partiellement_conforme" {{ $evaluation->statut == 'partiellement_conforme' ? 'checked' : '' }}>
                        <span class="text-sm text-yellow-700">Partiellement conforme ⚠️</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="statut" value="non_applicable" {{ $evaluation->statut == 'non_applicable' ? 'checked' : '' }}>
                        <span class="text-sm">Non applicable ➖</span>
                    </label>
                </div>
            </div>
            
            <!-- Justification -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Justification</label>
                <textarea name="justification" rows="4" 
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Expliquez votre évaluation...">{{ old('justification', $evaluation->justification) }}</textarea>
            </div>
            
            <!-- Recommandation -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recommandation</label>
                <textarea name="recommandation" rows="3" 
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Proposez des actions correctives...">{{ old('recommandation', $evaluation->recommandation) }}</textarea>
            </div>
            
            <!-- Analyse des risques -->
            <div class="border-t pt-4">
                <h3 class="font-semibold text-gray-900 mb-4">Analyse des risques</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gravité du risque</label>
                        <select name="gravite_risque" class="w-full rounded-lg border border-gray-300 px-4 py-2">
                            <option value="">Non évalué</option>
                            <option value="faible" {{ $evaluation->gravite_risque == 'faible' ? 'selected' : '' }}>Faible</option>
                            <option value="moyen" {{ $evaluation->gravite_risque == 'moyen' ? 'selected' : '' }}>Moyen</option>
                            <option value="eleve" {{ $evaluation->gravite_risque == 'eleve' ? 'selected' : '' }}>Élevé</option>
                            <option value="critique" {{ $evaluation->gravite_risque == 'critique' ? 'selected' : '' }}>Critique</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Probabilité du risque</label>
                        <select name="probabilite_risque" class="w-full rounded-lg border border-gray-300 px-4 py-2">
                            <option value="">Non évalué</option>
                            <option value="faible" {{ $evaluation->probabilite_risque == 'faible' ? 'selected' : '' }}>Faible</option>
                            <option value="moyen" {{ $evaluation->probabilite_risque == 'moyen' ? 'selected' : '' }}>Moyen</option>
                            <option value="eleve" {{ $evaluation->probabilite_risque == 'eleve' ? 'selected' : '' }}>Élevé</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Points -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Points obtenus (max: {{ $control->ponderation_defaut }})
                </label>
                <input type="number" name="points_obtenus" step="0.5" min="0" max="{{ $control->ponderation_defaut }}"
                    value="{{ old('points_obtenus', $evaluation->points_obtenus) }}"
                    class="w-full md:w-48 rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <!-- Boutons -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('evaluation.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Enregistrer l'évaluation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection