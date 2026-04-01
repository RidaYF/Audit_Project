@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('audits.index') }}" class="text-sm text-purple-600 hover:text-purple-700 mb-2 inline-block">
                ← Retour aux audits
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Créer un nouvel audit</h1>
            <p class="text-sm text-gray-500 mt-1">Configurez votre audit de conformité ISO 2700x</p>
        </div>
    </div>

    <!-- Formulaire -->
    <form method="POST" action="{{ route('audits.store') }}" class="space-y-6" x-data="auditForm()" x-init="init()">
        @csrf
        
        <!-- Informations générales -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Titre de l'audit <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="titre" required 
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                           placeholder="Ex: Audit de certification ISO 27001 - TechInnov Solutions"
                           value="{{ old('titre') }}">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Entreprise à auditer <span class="text-red-500">*</span>
                    </label>
                    <select name="entreprise_id" required 
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                            @change="loadEnterpriseData($event.target.value)">
                        <option value="">Sélectionner une entreprise</option>
                        @foreach($entreprises as $entreprise)
                            <option value="{{ $entreprise->id }}" {{ old('entreprise_id') == $entreprise->id ? 'selected' : '' }}>
                                {{ $entreprise->nom }} ({{ $entreprise->secteur }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Info entreprise sélectionnée -->
                <div x-show="selectedEnterprise" x-cloak class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-2">Contexte de l'entreprise</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Secteur :</span>
                            <span class="text-gray-700 ml-2" x-text="selectedEnterprise.secteur"></span>
                        </div>
                        <div>
                            <span class="text-gray-500">Taille :</span>
                            <span class="text-gray-700 ml-2" x-text="selectedEnterprise.taille"></span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500">Systèmes clés :</span>
                            <span class="text-gray-700 ml-2" x-text="selectedEnterprise.systemes_cles"></span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500">Actifs critiques :</span>
                            <span class="text-gray-700 ml-2" x-text="selectedEnterprise.actifs_critiques"></span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                              placeholder="Objectifs, périmètre, contexte de l'audit...">{{ old('description') }}</textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date de début <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="date_debut" required 
                               class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                               value="{{ old('date_debut', now()->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin prévue</label>
                        <input type="datetime-local" name="date_fin" 
                               class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                               value="{{ old('date_fin') }}">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sélection des référentiels ISO -->
        <p>I'M HERE </p>
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Référentiels ISO à auditer</h2>
            <p class="text-sm text-gray-500 mb-4">Sélectionnez les normes qui seront vérifiées lors de l'audit</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($references as $reference)
                <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                       :class="{ 'border-purple-500 bg-purple-50': selectedReferences.includes('{{ $reference->id }}') }">
                    <input type="checkbox" name="reference_ids[]" value="{{ $reference->id }}"
                           class="mt-1 mr-3 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                           x-model="selectedReferences"
                           @change="loadReferenceData('{{ $reference->id }}', $event.target.checked)">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-900">{{ $reference->code }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full 
                                {{ $reference->type_norme == 'management' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $reference->type_norme == 'control' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $reference->type_norme == 'risk' ? 'bg-orange-100 text-orange-700' : '' }}">
                                {{ $reference->type_norme == 'management' ? 'Management' : ($reference->type_norme == 'control' ? 'Contrôles' : 'Risques') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $reference->nom }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $reference->description }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        
        <!-- Structure ISO chargée dynamiquement -->
        <div x-show="loadedReferences.length > 0" x-cloak>
            <!-- Clauses ISO 27001 -->
            <div x-show="iso27001Data" class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    ISO 27001 - Clauses du SMSI
                </h2>
                <p class="text-sm text-gray-500 mb-4">Les exigences du Système de Management de la Sécurité de l'Information</p>
                
                <div class="space-y-3">
                    <template x-for="clause in iso27001Data.clauses" :key="clause.id">
                        <div class="border rounded-lg overflow-hidden">
                            <div class="flex items-center justify-between p-3 bg-gray-50 cursor-pointer"
                                 @click="toggleClause(clause.id)">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-sm font-semibold text-purple-600" x-text="clause.numero"></span>
                                    <span class="font-medium text-gray-900" x-text="clause.titre"></span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" 
                                     :class="{ 'rotate-180': openClauses.includes(clause.id) }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <div x-show="openClauses.includes(clause.id)" x-cloak class="p-3 border-t">
                                <p class="text-sm text-gray-600 mb-3" x-text="clause.description"></p>
                                <div x-show="clause.children && clause.children.length > 0">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Sous-clauses :</h4>
                                    <div class="space-y-2 ml-4">
                                        <template x-for="child in clause.children" :key="child.id">
                                            <div class="flex items-start gap-2">
                                                <span class="font-mono text-xs text-purple-500" x-text="child.numero"></span>
                                                <span class="text-sm text-gray-600" x-text="child.titre"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            
            <!-- Contrôles ISO 27002 -->
            <div x-show="iso27002Data" class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    ISO 27002 - Contrôles de sécurité
                </h2>
                <p class="text-sm text-gray-500 mb-4">Sélectionnez les contrôles à inclure dans l'audit</p>
                
                <div class="space-y-3">
                    <template x-for="control in iso27002Data.controls" :key="control.id">
                        <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                               :class="{ 'border-blue-500 bg-blue-50': selectedControls.includes(control.id) }">
                            <input type="checkbox" name="control_ids[]" :value="control.id"
                                   class="mt-1 mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                   x-model="selectedControls">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-sm font-semibold text-blue-600" x-text="control.code"></span>
                                    <span class="font-medium text-gray-900" x-text="control.titre"></span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1" x-text="control.description"></p>
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="text-xs px-2 py-0.5 rounded-full" 
                                          :class="control.niveau_critique === 'critique' ? 'bg-red-100 text-red-700' :
                                                  control.niveau_critique === 'eleve' ? 'bg-orange-100 text-orange-700' :
                                                  control.niveau_critique === 'moyen' ? 'bg-yellow-100 text-yellow-700' :
                                                  'bg-green-100 text-green-700'"
                                          x-text="control.niveau_critique.toUpperCase()">
                                    </span>
                                    <span class="text-xs text-gray-500" x-text="control.categorie"></span>
                                    <span class="text-xs text-gray-500" x-text="control.type_controle"></span>
                                </div>
                            </div>
                        </label>
                    </template>
                </div>
            </div>
            
            <!-- Méthodes d'analyse de risques ISO 27005 -->
            <div x-show="iso27005Data" class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    ISO 27005 - Méthodes d'analyse de risques
                </h2>
                <p class="text-sm text-gray-500 mb-4">Méthodologies pour l'évaluation et le traitement des risques</p>
                
                <div class="space-y-3">
                    <template x-for="risk in iso27005Data.risks" :key="risk.id">
                        <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                               :class="{ 'border-orange-500 bg-orange-50': selectedRisks.includes(risk.id) }">
                            <input type="checkbox" name="risk_ids[]" :value="risk.id"
                                   class="mt-1 mr-3 rounded border-gray-300 text-orange-600 focus:ring-orange-500"
                                   x-model="selectedRisks">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-sm font-semibold text-orange-600" x-text="risk.code"></span>
                                    <span class="font-medium text-gray-900" x-text="risk.titre"></span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1" x-text="risk.description"></p>
                                <p class="text-xs text-gray-500 mt-1" x-text="risk.objectif"></p>
                            </div>
                        </label>
                    </template>
                </div>
            </div>
        </div>
        
        <!-- Résumé de l'audit -->
        <div class="card bg-purple-50 border-purple-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Résumé de l'audit</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-purple-600" x-text="selectedReferences.length"></p>
                    <p class="text-sm text-gray-600">Référentiels</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-600" x-text="selectedControls.length"></p>
                    <p class="text-sm text-gray-600">Contrôles ISO 27002</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-orange-600" x-text="selectedRisks.length"></p>
                    <p class="text-sm text-gray-600">Méthodes de risques</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600" x-text="selectedClausesCount"></p>
                    <p class="text-sm text-gray-600">Clauses ISO 27001</p>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('audits.index') }}" class="btn btn-outline">Annuler</a>
            <button type="submit" class="btn btn-primary">
                Créer l'audit
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function auditForm() {
    return {
        selectedReferences: [],
        selectedControls: [],
        selectedRisks: [],
        openClauses: [],
        selectedEnterprise: null,
        loadedReferences: [],
        iso27001Data: null,
        iso27002Data: null,
        iso27005Data: null,
        
        get selectedClausesCount() {
            if (!this.iso27001Data) return 0;
            return this.iso27001Data.clauses.length;
        },
        
        async init() {
            // Charger les données d'entreprise si déjà sélectionnée
            const enterpriseId = document.querySelector('[name="entreprise_id"]').value;
            if (enterpriseId) {
                await this.loadEnterpriseData(enterpriseId);
            }
            
            // Charger les références pré-sélectionnées
            const preselectedRefs = Array.from(document.querySelectorAll('input[name="reference_ids[]"]:checked')).map(cb => cb.value);
            if (preselectedRefs.length > 0) {
                for (const refId of preselectedRefs) {
                    await this.loadReferenceData(refId, true);
                }
            }
        },
        
        async loadEnterpriseData(enterpriseId) {
            if (!enterpriseId) {
                this.selectedEnterprise = null;
                return;
            }
            
            try {
                const response = await fetch(`/api/enterprises/${enterpriseId}`);
                this.selectedEnterprise = await response.json();
            } catch (error) {
                console.error('Erreur lors du chargement de l\'entreprise:', error);
            }
        },
        
        async loadReferenceData(referenceId, isChecked) {
            if (isChecked && !this.loadedReferences.includes(referenceId)) {
                this.loadedReferences.push(referenceId);
                
                try {
                    const response = await fetch(`/api/references/${referenceId}/structure`);
                    const data = await response.json();
                    
                    if (data.type === 'ISO27001') {
                        this.iso27001Data = data;
                    } else if (data.type === 'ISO27002') {
                        this.iso27002Data = data;
                    } else if (data.type === 'ISO27005') {
                        this.iso27005Data = data;
                    }
                } catch (error) {
                    console.error('Erreur lors du chargement de la référence:', error);
                }
            } else if (!isChecked) {
                this.loadedReferences = this.loadedReferences.filter(id => id !== referenceId);
                
                // Vérifier si on doit supprimer les données
                const hasIso27001 = this.selectedReferences.includes('{{ $references->where('code', 'ISO27001')->first()->id ?? '' }}');
                const hasIso27002 = this.selectedReferences.includes('{{ $references->where('code', 'ISO27002')->first()->id ?? '' }}');
                const hasIso27005 = this.selectedReferences.includes('{{ $references->where('code', 'ISO27005')->first()->id ?? '' }}');
                
                if (!hasIso27001) this.iso27001Data = null;
                if (!hasIso27002) this.iso27002Data = null;
                if (!hasIso27005) this.iso27005Data = null;
            }
        },
        
        toggleClause(clauseId) {
            const index = this.openClauses.indexOf(clauseId);
            if (index > -1) {
                this.openClauses.splice(index, 1);
            } else {
                this.openClauses.push(clauseId);
            }
        },

        selectAllControls() {
            if (this.iso27002Data && this.iso27002Data.controls) {
                this.selectedControls = this.iso27002Data.controls.map(c => c.id);
            }
        },

        deselectAllControls() {
            this.selectedControls = [];
        },

        selectAllRisks() {
            if (this.iso27005Data && this.iso27005Data.risks) {
                this.selectedRisks = this.iso27005Data.risks.map(r => r.id);
            }
        },

        deselectAllRisks() {
            this.selectedRisks = [];
        }
        }
}
</script>
@endpush
@endsection