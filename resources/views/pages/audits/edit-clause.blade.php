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
        <p class="text-sm text-gray-500">Entreprise: {{ $audit->entreprise->nom }}</p>
    </div>
    
    <!-- Description de la clause -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="font-medium text-gray-900 mb-2">Description de la clause</h3>
        <p class="text-gray-600">{{ $clauseEval->clause->description ?: 'Aucune description disponible' }}</p>
    </div>

    <!-- Formulaire d'évaluation -->
    <form method="POST" action="{{ route('audits.clause.update', [$audit->id, $clauseEval->id]) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        @php
            $entrepriseData = is_array($clauseEval->entreprise_data) 
                ? $clauseEval->entreprise_data 
                : (json_decode($clauseEval->entreprise_data ?? '{}', true) ?: []);
        @endphp

        <!-- SECTION 1: INFORMATIONS COLLECTÉES SUR L'ENTREPRISE (TOUJOURS VISIBLE) -->
        <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-200 p-6">
            <h3 class="font-medium text-blue-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Informations collectées sur l'entreprise (contexte spécifique à cette clause)
            </h3>
            <p class="text-sm text-blue-700 mb-4">Documentez comment l'entreprise répond à cette exigence de la norme.</p>
            
            <!-- Clause 4: Contexte de l'organisation -->
            @if(in_array($clauseEval->clause->numero, ['4', '4.1', '4.2', '4.3']))
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Enjeux internes</label>
                        <textarea name="entreprise_data[enjeux_internes]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Structure organisationnelle, ressources, culture d'entreprise, gouvernance, processus internes...">{{ $entrepriseData['enjeux_internes'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">🌍 Enjeux externes</label>
                        <textarea name="entreprise_data[enjeux_externes]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Environnement légal, technologique, concurrentiel, sociétal, économique...">{{ $entrepriseData['enjeux_externes'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">👥 Parties intéressées</label>
                        <textarea name="entreprise_data[parties_interessees]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Clients, fournisseurs, actionnaires, régulateurs, employés, partenaires...">{{ $entrepriseData['parties_interessees'] ?? '' }}</textarea>
                    </div>
                </div>
            
            <!-- Clause 5: Leadership -->
            @elseif(in_array($clauseEval->clause->numero, ['5', '5.1', '5.2', '5.3']))
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Engagement de la direction</label>
                        <textarea name="entreprise_data[engagement_direction]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Preuves de l'engagement de la direction (politiques, ressources, revues)...">{{ $entrepriseData['engagement_direction'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Politique de sécurité de l'information</label>
                        <textarea name="entreprise_data[politique_securite]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Référence de la politique, date d'approbation, diffusion, révisions...">{{ $entrepriseData['politique_securite'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Rôles et responsabilités</label>
                        <textarea name="entreprise_data[roles_responsabilites]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Définition des rôles et responsabilités sécurité, RSSI, comités...">{{ $entrepriseData['roles_responsabilites'] ?? '' }}</textarea>
                    </div>
                </div>
            
            <!-- Clause 6: Planification -->
            @elseif(in_array($clauseEval->clause->numero, ['6', '6.1', '6.2']))
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Analyse des risques</label>
                        <textarea name="entreprise_data[analyse_risques]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Méthodologie d'analyse, actifs identifiés, menaces, vulnérabilités, impacts...">{{ $entrepriseData['analyse_risques'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Objectifs sécurité</label>
                        <textarea name="entreprise_data[objectifs_securite]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Objectifs SMART, indicateurs de performance (KPI), cibles, échéances...">{{ $entrepriseData['objectifs_securite'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Plan de traitement des risques</label>
                        <textarea name="entreprise_data[plan_traitement]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Contrôles sélectionnés (ISO 27002), responsables, délais, ressources...">{{ $entrepriseData['plan_traitement'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Opportunités identifiées</label>
                        <textarea name="entreprise_data[opportunites]" rows="2" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Opportunités d'amélioration, avantages concurrentiels...">{{ $entrepriseData['opportunites'] ?? '' }}</textarea>
                    </div>
                </div>
            
            <!-- Clause 7: Support -->
            @elseif(in_array($clauseEval->clause->numero, ['7', '7.1', '7.2', '7.3', '7.4', '7.5']))
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Ressources</label>
                        <textarea name="entreprise_data[ressources]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Ressources humaines, financières, technologiques, infrastructures...">{{ $entrepriseData['ressources'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Compétences et formations</label>
                        <textarea name="entreprise_data[competences]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Compétences requises, formations dispensées, sensibilisation...">{{ $entrepriseData['competences'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Communication</label>
                        <textarea name="entreprise_data[communication]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Plan de communication, canaux, destinataires, fréquence...">{{ $entrepriseData['communication'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Documentation</label>
                        <textarea name="entreprise_data[documentation]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Liste des documents maîtrisés, versioning, accès...">{{ $entrepriseData['documentation'] ?? '' }}</textarea>
                    </div>
                </div>
            
            <!-- Clause 8: Fonctionnement -->
            @elseif(in_array($clauseEval->clause->numero, ['8', '8.1', '8.2', '8.3']))
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Processus opérationnels</label>
                        <textarea name="entreprise_data[processus_operationnels]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Description des processus clés, interactions, critères d'acceptation...">{{ $entrepriseData['processus_operationnels'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Contrôles implémentés</label>
                        <textarea name="entreprise_data[controles_implementes]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Contrôles déjà en place, référence ISO 27002, efficacité...">{{ $entrepriseData['controles_implementes'] ?? '' }}</textarea>
                    </div>
                </div>
            
            <!-- Clause 9: Évaluation des performances -->
            @elseif(in_array($clauseEval->clause->numero, ['9', '9.1', '9.2', '9.3']))
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Indicateurs de performance</label>
                        <textarea name="entreprise_data[indicateurs_kpi]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="KPI suivis, résultats, tendances, seuils...">{{ $entrepriseData['indicateurs_kpi'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">🔍 Audits internes réalisés</label>
                        <textarea name="entreprise_data[audits_internes]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Dates, périmètres, résultats, non-conformités...">{{ $entrepriseData['audits_internes'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Revues de direction</label>
                        <textarea name="entreprise_data[revues_direction]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Dates, participants, décisions, plans d'action...">{{ $entrepriseData['revues_direction'] ?? '' }}</textarea>
                    </div>
                </div>
            
            <!-- Clause 10: Amélioration -->
            @elseif(in_array($clauseEval->clause->numero, ['10', '10.1', '10.2']))
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">🔧 Actions correctives</label>
                        <textarea name="entreprise_data[actions_correctives]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Actions correctives en cours/réalisées, efficacité...">{{ $entrepriseData['actions_correctives'] ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-800 mb-1">Amélioration continue</label>
                        <textarea name="entreprise_data[amelioration_continue]" rows="3" 
                                  class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                                  placeholder="Initiatives d'amélioration, leçons apprises, benchmarking...">{{ $entrepriseData['amelioration_continue'] ?? '' }}</textarea>
                    </div>
                </div>
            
            <!-- Autres sous-clauses -->
            @else
                <div>
                    <label class="block text-sm font-medium text-blue-800 mb-1">Informations spécifiques à la clause {{ $clauseEval->clause->numero }}</label>
                    <textarea name="entreprise_data[informations_specifiques]" rows="4" 
                              class="w-full rounded-lg border border-blue-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 bg-white"
                              placeholder="Documentez ici les informations collectées sur l'entreprise concernant cette clause...">{{ $entrepriseData['informations_specifiques'] ?? '' }}</textarea>
                </div>
            @endif
            
            <p class="text-xs text-blue-600 mt-3">Ces informations documentent le contexte de l'entreprise pour cette clause.</p>
        </div>

        <!-- SECTION 2: ÉVALUATION DE CONFORMITÉ (STATUT) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="font-medium text-gray-900 mb-4"> Évaluation de conformité</h3>
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
                    <textarea name="justification" rows="3" 
                              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                              placeholder="Expliquez votre évaluation...">{{ $clauseEval->justification }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recommandations</label>
                    <textarea name="recommandation" rows="3" 
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