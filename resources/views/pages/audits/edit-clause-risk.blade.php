@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('risk.management') }}" class="text-sm text-purple-600 hover:text-purple-700 mb-2 inline-block">
                ← Retour à la gestion des risques
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Traitement du risque</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $audit->titre }} - {{ $audit->entreprise->nom }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gradient-to-r from-red-50 to-orange-50">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-mono text-lg font-bold text-red-600">
                            {{ $clauseEval->clause->reference->code ?? 'ISO' }} - {{ $clauseEval->clause->numero }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                            Non-conformité
                        </span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $clauseEval->clause->titre }}</h2>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Évalué le</p>
                    <p class="font-medium">{{ $clauseEval->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('audits.clause.update-risk', ['auditId' => $audit->id, 'clauseEvalId' => $clauseEval->id]) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Description de la clause -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-2">Description de la clause</h3>
                <p class="text-gray-700">{{ $clauseEval->clause->description }}</p>
            </div>

            <!-- Situation actuelle (non-conformité) -->
            <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                <h3 class="font-semibold text-red-800 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Non-conformité identifiée
                </h3>
                @if($clauseEval->justification)
                    <p class="text-red-700">{{ $clauseEval->justification }}</p>
                @else
                    <p class="text-red-700 italic">Aucune justification fournie</p>
                @endif
            </div>

            <!-- Formulaire de traitement -->
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-900 border-b pb-2">Traitement du risque</h3>

                <!-- Plan d'action -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Plan d'action correctif <span class="text-red-500">*</span>
                    </label>
                    <textarea name="plan_action" rows="4" 
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                        placeholder="Décrivez les actions à mettre en place pour corriger cette non-conformité...">{{ old('plan_action', $clauseEval->plan_action ?? '') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Décrivez les mesures correctives à prendre</p>
                </div>

                <!-- Responsable -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Responsable de l'action
                    </label>
                    <input type="text" name="responsable" 
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                        placeholder="Nom du responsable"
                        value="{{ old('responsable', $clauseEval->responsable ?? '') }}">
                </div>

                <!-- Date d'échéance -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Date d'échéance
                    </label>
                    <input type="date" name="date_echeance" 
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                        value="{{ old('date_echeance', $clauseEval->date_echeance ? date('Y-m-d', strtotime($clauseEcheance)) : '') }}">
                </div>

                <!-- Niveau de priorité -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Niveau de priorité
                    </label>
                    <select name="priorite" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                        <option value="haute" {{ old('priorite', $clauseEval->priorite ?? '') == 'haute' ? 'selected' : '' }}>🔴 Haute - À traiter immédiatement</option>
                        <option value="moyenne" {{ old('priorite', $clauseEval->priorite ?? '') == 'moyenne' ? 'selected' : '' }}>🟠 Moyenne - À traiter rapidement</option>
                        <option value="basse" {{ old('priorite', $clauseEval->priorite ?? '') == 'basse' ? 'selected' : '' }}>🟢 Basse - À planifier</option>
                    </select>
                </div>

                <!-- Commentaires supplémentaires -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Commentaires supplémentaires
                    </label>
                    <textarea name="commentaires" rows="3" 
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                        placeholder="Informations complémentaires...">{{ old('commentaires', $clauseEval->commentaires ?? '') }}</textarea>
                </div>

                <!-- Statut du traitement -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Statut du traitement
                    </label>
                    <select name="statut_traitement" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                        <option value="a_traiter" {{ old('statut_traitement', $clauseEval->statut_traitement ?? '') == 'a_traiter' ? 'selected' : '' }}>À traiter</option>
                        <option value="en_cours" {{ old('statut_traitement', $clauseEval->statut_traitement ?? '') == 'en_cours' ? 'selected' : '' }}>En cours de traitement</option>
                        <option value="traite" {{ old('statut_traitement', $clauseEval->statut_traitement ?? '') == 'traite' ? 'selected' : '' }}>Traité</option>
                        <option value="valide" {{ old('statut_traitement', $clauseEval->statut_traitement ?? '') == 'valide' ? 'selected' : '' }}>Validé - Risque résolu</option>
                    </select>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('risk.management') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Enregistrer le plan d'action
                </button>
            </div>
        </form>
    </div>

    <!-- Historique des actions -->
    @if(isset($clauseEval->actions) && $clauseEval->actions->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-semibold text-gray-900">Historique des actions</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($clauseEval->actions as $action)
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">{{ $action->created_at->format('d/m/Y H:i') }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium 
                        @if($action->type == 'plan_action') bg-blue-100 text-blue-700
                        @elseif($action->type == 'commentaire') bg-gray-100 text-gray-700
                        @else bg-green-100 text-green-700
                        @endif">
                        {{ ucfirst($action->type) }}
                    </span>
                </div>
                <p class="text-sm text-gray-700">{{ $action->contenu }}</p>
                @if($action->utilisateur)
                <p class="text-xs text-gray-500 mt-1">Par: {{ $action->utilisateur->name }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection