{{-- resources/views/pages/audits/edit-control.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('audits.show', $audit->id) }}" class="text-sm text-orange-600 hover:text-orange-700 mb-2 inline-block">
                ← Retour à l'audit
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Évaluer le contrôle</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $evaluation->controle->code }} - {{ $evaluation->controle->titre }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('controles.edit', $evaluation->controle->id) }}" 
               class="px-4 py-2 border border-orange-600 text-orange-600 rounded-lg hover:bg-orange-50 transition-colors">
                Modifier les infos du contrôle
            </a>
        </div>
    </div>

    <!-- Description du contrôle -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-start mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Description du contrôle</h2>
            <span class="text-xs text-gray-400">Données maîtres - modifiables par les administrateurs</span>
        </div>
        <p class="text-gray-700">{{ $evaluation->controle->description }}</p>
        
        @if($evaluation->controle->objectif)
        <div class="mt-4">
            <h3 class="font-medium text-gray-900 mb-2">Objectif</h3>
            <p class="text-gray-700">{{ $evaluation->controle->objectif }}</p>
        </div>
        @endif
        
        @if($evaluation->controle->conseils_audit)
        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
            <h3 class="font-medium text-blue-800 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Conseil d'audit
            </h3>
            <p class="text-blue-700">{{ $evaluation->controle->conseils_audit }}</p>
        </div>
        @endif
        
        <div class="flex items-center gap-4 mt-4 pt-3 border-t">
            <span class="text-sm text-gray-500">Niveau critique:</span>
            @php
                $riskColors = [
                    'critique' => 'bg-red-100 text-red-700',
                    'eleve' => 'bg-orange-100 text-orange-700',
                    'moyen' => 'bg-yellow-100 text-yellow-700',
                    'faible' => 'bg-green-100 text-green-700'
                ];
            @endphp
            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $riskColors[$evaluation->controle->niveau_critique] ?? 'bg-gray-100' }}">
                {{ ucfirst($evaluation->controle->niveau_critique) }}
            </span>
            
            <span class="text-sm text-gray-500 ml-4">Type:</span>
            <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                {{ ucfirst($evaluation->controle->type_controle) }}
            </span>
            
            @if($evaluation->controle->ponderation_defaut)
                <span class="text-sm text-gray-500 ml-4">Pondération:</span>
                <span class="text-sm font-medium">{{ $evaluation->controle->ponderation_defaut }}</span>
            @endif
        </div>
    </div>

    <!-- Formulaire d'évaluation -->
    <form method="POST" action="{{ route('audits.control.update', [$audit->id, $evaluation->id]) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Évaluation du contrôle pour cet audit</h2>
            <p class="text-sm text-gray-500 mb-4">Ces informations sont spécifiques à l'audit "{{ $audit->titre }}"</p>
            
            <div class="space-y-4">
                <!-- Statut -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut <span class="text-red-500">*</span></label>
                    <select name="statut" required class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-orange-500 focus:ring-orange-500">
                        <option value="non_audite" {{ $evaluation->statut == 'non_audite' ? 'selected' : '' }}>Non audité</option>
                        <option value="conforme" {{ $evaluation->statut == 'conforme' ? 'selected' : '' }}>Conforme</option>
                        <option value="non_conforme" {{ $evaluation->statut == 'non_conforme' ? 'selected' : '' }}>Non conforme</option>
                        <option value="partiellement_conforme" {{ $evaluation->statut == 'partiellement_conforme' ? 'selected' : '' }}>Partiellement conforme</option>
                        <option value="non_applicable" {{ $evaluation->statut == 'non_applicable' ? 'selected' : '' }}>Non applicable</option>
                    </select>
                </div>
                
                <!-- Grille des risques -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gravité du risque</label>
                        <select name="gravite_risque" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-orange-500 focus:ring-orange-500">
                            <option value="">-- Sélectionner --</option>
                            <option value="faible" {{ $evaluation->gravite_risque == 'faible' ? 'selected' : '' }}>Faible</option>
                            <option value="moyen" {{ $evaluation->gravite_risque == 'moyen' ? 'selected' : '' }}>Moyen</option>
                            <option value="eleve" {{ $evaluation->gravite_risque == 'eleve' ? 'selected' : '' }}>Élevé</option>
                            <option value="critique" {{ $evaluation->gravite_risque == 'critique' ? 'selected' : '' }}>Critique</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Probabilité du risque</label>
                        <select name="probabilite_risque" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-orange-500 focus:ring-orange-500">
                            <option value="">-- Sélectionner --</option>
                            <option value="faible" {{ $evaluation->probabilite_risque == 'faible' ? 'selected' : '' }}>Faible</option>
                            <option value="moyen" {{ $evaluation->probabilite_risque == 'moyen' ? 'selected' : '' }}>Moyen</option>
                            <option value="eleve" {{ $evaluation->probabilite_risque == 'eleve' ? 'selected' : '' }}>Élevé</option>
                        </select>
                    </div>
                </div>
                
                <!-- Points obtenus -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Points obtenus</label>
                    <input type="number" name="points_obtenus" min="0" step="0.5" 
                           value="{{ $evaluation->points_obtenus }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-orange-500 focus:ring-orange-500">
                    <p class="text-xs text-gray-500 mt-1">Pondération maximale: {{ $evaluation->controle->ponderation_defaut ?? 1 }} point(s)</p>
                </div>
                
                <!-- Justification -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Justification</label>
                    <textarea name="justification" rows="4" 
                              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-orange-500 focus:ring-orange-500"
                              placeholder="Expliquez votre évaluation...">{{ $evaluation->justification }}</textarea>
                </div>
                
                <!-- Recommandations -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recommandations</label>
                    <textarea name="recommandation" rows="4" 
                              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-orange-500 focus:ring-orange-500"
                              placeholder="Proposez des recommandations d'amélioration...">{{ $evaluation->recommandation }}</textarea>
                </div>
            </div>
        </div>

        <!-- Preuves associées (consultées pendant l'audit) -->
        @if($evaluation->controle->preuves && $evaluation->controle->preuves->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Preuves consultées lors de l'audit</h2>
            <p class="text-sm text-gray-500 mb-4">Cochez les preuves que vous avez consultées pour ce contrôle</p>
            <div class="space-y-3">
                @foreach($evaluation->controle->preuves as $preuve)
                    @php
                        $preuvesConsultees = json_decode($evaluation->preuves_consultees, true) ?? [];
                        $isChecked = in_array($preuve->titre, $preuvesConsultees);
                    @endphp
                    <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $isChecked ? 'bg-green-50 border-green-200' : '' }}">
                        <input type="checkbox" name="preuves_consultees[]" value="{{ $preuve->titre }}"
                               {{ $isChecked ? 'checked' : '' }}
                               class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $preuve->titre }}</p>
                            <p class="text-sm text-gray-600">{{ $preuve->contenu }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $preuve->type_preuve }}</span>
                                @if($preuve->points_max)
                                    <span class="text-xs text-gray-500">Points: {{ $preuve->points_max }}</span>
                                @endif
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
            <p class="text-xs text-gray-500 mt-3">Ces preuves sont les documents/éléments que vous avez consultés pour justifier votre évaluation.</p>
        </div>
        @endif

        <!-- Actions -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('audits.show', $audit->id) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                Enregistrer l'évaluation
            </button>
        </div>
    </form>
</div>
@endsection