{{-- resources/views/pages/audits/edit-clause.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('audits.clauses', $audit->id) }}" class="text-sm text-purple-600 hover:text-purple-700 mb-2 inline-block">
            ← Retour aux clauses
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Évaluer la clause</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $clauseEval->clause->numero }} - {{ $clauseEval->clause->titre }}</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="font-medium text-gray-900 mb-2">Description</h3>
        <p class="text-gray-600">{{ $clauseEval->clause->description ?: 'Aucune description disponible' }}</p>
    </div>
    
    <form method="POST" action="{{ route('audits.clause.update', [$audit->id, $clauseEval->id]) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                        <option value="non_audite" {{ $clauseEval->statut == 'non_audite' ? 'selected' : '' }}>Non audité</option>
                        <option value="conforme" {{ $clauseEval->statut == 'conforme' ? 'selected' : '' }}>Conforme</option>
                        <option value="non_conforme" {{ $clauseEval->statut == 'non_conforme' ? 'selected' : '' }}>Non conforme</option>
                        <option value="partiellement_conforme" {{ $clauseEval->statut == 'partiellement_conforme' ? 'selected' : '' }}>Partiellement conforme</option>
                        <option value="non_applicable" {{ $clauseEval->statut == 'non_applicable' ? 'selected' : '' }}>Non applicable</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Niveau de conformité</label>
                    <select name="niveau_conformite" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                        <option value="">-- Sélectionner --</option>
                        <option value="faible" {{ $clauseEval->niveau_conformite == 'faible' ? 'selected' : '' }}>Faible</option>
                        <option value="moyen" {{ $clauseEval->niveau_conformite == 'moyen' ? 'selected' : '' }}>Moyen</option>
                        <option value="eleve" {{ $clauseEval->niveau_conformite == 'eleve' ? 'selected' : '' }}>Élevé</option>
                        <option value="excellent" {{ $clauseEval->niveau_conformite == 'excellent' ? 'selected' : '' }}>Excellent</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Score (%)</label>
                    <input type="number" name="score" min="0" max="100" step="5" 
                           value="{{ $clauseEval->score }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Justification</label>
                    <textarea name="justification" rows="4" 
                              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                              placeholder="Expliquez votre évaluation...">{{ $clauseEval->justification }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recommandations</label>
                    <textarea name="recommandation" rows="4" 
                              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                              placeholder="Proposez des recommandations d'amélioration...">{{ $clauseEval->recommandation }}</textarea>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end gap-3">
            <a href="{{ route('audits.clauses', $audit->id) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Enregistrer</button>
        </div>
    </form>
</div>
@endsection