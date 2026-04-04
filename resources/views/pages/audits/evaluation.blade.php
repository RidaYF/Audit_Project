@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('audit.current') }}" class="text-sm text-purple-600 hover:text-purple-700 mb-2 inline-block">
                ← Retour à l'audit
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Évaluation de conformité</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $audit->titre }} - {{ $audit->entreprise->nom }}</p>
        </div>
        <div class="flex gap-2">
            <button onclick="showTab('controls')" id="tabControlsBtn" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                Contrôles ISO 27002
            </button>
            <button onclick="showTab('clauses')" id="tabClausesBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Clauses ISO 27001
            </button>
        </div>
    </div>

    <!-- Contrôles ISO 27002 -->
    <div id="controlsTab" class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Contrôles de sécurité</h2>
                <span class="text-sm text-gray-500">{{ $evaluations->count() }} contrôles</span>
            </div>
            
            @forelse($evaluations as $evaluation)
                @php
                    $control = $evaluation->controle;
                    $statusColors = [
                        'conforme' => 'bg-green-100 text-green-700',
                        'non_conforme' => 'bg-red-100 text-red-700',
                        'partiellement_conforme' => 'bg-yellow-100 text-yellow-700',
                        'non_applicable' => 'bg-gray-100 text-gray-700',
                        'non_audite' => 'bg-blue-100 text-blue-700'
                    ];
                @endphp
                <div class="card" id="evaluation-{{ $evaluation->id }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="font-mono text-sm font-semibold text-purple-600">{{ $control->code }}</span>
                                <span class="badge {{ $statusColors[$evaluation->statut] ?? 'bg-gray-100' }}">
                                    {{ ucfirst(str_replace('_', ' ', $evaluation->statut)) }}
                                </span>
                                @if($control->niveau_critique)
                                    <span class="text-xs px-2 py-0.5 rounded-full 
                                        {{ $control->niveau_critique === 'critique' ? 'bg-red-100 text-red-700' : 
                                           ($control->niveau_critique === 'eleve' ? 'bg-orange-100 text-orange-700' : 
                                           ($control->niveau_critique === 'moyen' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700')) }}">
                                        {{ strtoupper($control->niveau_critique) }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $control->titre }}</h3>
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($control->description, 200) }}</p>
                            
                            @if($evaluation->justification)
                                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                    <p class="text-xs text-gray-500 mb-1">Justification:</p>
                                    <p class="text-sm text-gray-700">{{ Str::limit($evaluation->justification, 150) }}</p>
                                </div>
                            @endif
                            
                            <div class="flex gap-2">
                                <button type="button" 
                                        class="btn btn-outline-primary btn-sm"
                                        onclick="openEvaluationModal({{ $evaluation->id }}, '{{ $control->code }}', '{{ $control->titre }}', 'control')">
                                    Évaluer ce contrôle
                                </button>
                                <a href="{{ route('evidence.collect') }}" class="btn btn-outline btn-sm">
                                    Voir les preuves
                                </a>
                            </div>
                        </div>
                        <div class="ml-4 text-right">
                            @if($evaluation->points_obtenus > 0)
                                <p class="text-sm font-medium text-purple-600">{{ $evaluation->points_obtenus }}/{{ $control->ponderation_defaut ?? 1 }} pts</p>
                            @endif
                            @if($evaluation->niveau_risque_calcule)
                                <p class="text-xs text-gray-500 mt-1">Risque: {{ ucfirst($evaluation->niveau_risque_calcule) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <p>Aucun contrôle disponible pour cet audit.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Clauses ISO 27001 -->
    <div id="clausesTab" class="space-y-4" style="display: none;">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Clauses ISO 27001</h2>
                <span class="text-sm text-gray-500">{{ $clauseEvaluations->count() }} clauses</span>
            </div>
            
            @forelse($clauseEvaluations as $clauseEval)
                @php
                    $clause = $clauseEval->clause;
                    $statusColors = [
                        'conforme' => 'bg-green-100 text-green-700',
                        'non_conforme' => 'bg-red-100 text-red-700',
                        'partiellement_conforme' => 'bg-yellow-100 text-yellow-700',
                        'non_applicable' => 'bg-gray-100 text-gray-700',
                        'non_audite' => 'bg-blue-100 text-blue-700'
                    ];
                    $importanceColors = [
                        'critique' => 'bg-red-100 text-red-700',
                        'eleve' => 'bg-orange-100 text-orange-700',
                        'moyen' => 'bg-yellow-100 text-yellow-700',
                        'faible' => 'bg-green-100 text-green-700'
                    ];
                @endphp
                <div class="card" id="clause-{{ $clauseEval->id }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="font-mono text-sm font-semibold text-blue-600">{{ $clause->numero }}</span>
                                <span class="badge {{ $statusColors[$clauseEval->statut] ?? 'bg-gray-100' }}">
                                    {{ ucfirst(str_replace('_', ' ', $clauseEval->statut)) }}
                                </span>
                                @if($clause->niveau_importance)
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $importanceColors[$clause->niveau_importance] ?? 'bg-gray-100' }}">
                                        {{ ucfirst($clause->niveau_importance) }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $clause->titre }}</h3>
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($clause->description, 200) }}</p>
                            
                            @if($clauseEval->justification)
                                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                    <p class="text-xs text-gray-500 mb-1">Justification:</p>
                                    <p class="text-sm text-gray-700">{{ Str::limit($clauseEval->justification, 150) }}</p>
                                </div>
                            @endif
                            
                            <div class="flex gap-2">
                                <button type="button" 
                                        class="btn btn-outline-primary btn-sm"
                                        onclick="openEvaluationModal({{ $clauseEval->id }}, '{{ $clause->numero }}', '{{ $clause->titre }}', 'clause')">
                                    Évaluer cette clause
                                </button>
                            </div>
                        </div>
                        <div class="ml-4 text-right">
                            @if($clauseEval->score > 0)
                                <p class="text-sm font-medium text-blue-600">{{ $clauseEval->score }}/100 pts</p>
                            @endif
                            @if($clauseEval->niveau_conformite)
                                <p class="text-xs text-gray-500 mt-1">Niveau: {{ ucfirst($clauseEval->niveau_conformite) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <p>Aucune clause disponible pour cet audit.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal d'évaluation (Contrôles et Clauses) -->
<div id="evaluationModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Évaluation</h3>
                <button onclick="closeEvaluationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Formulaire pour les contrôles -->
            <form id="controlEvaluationForm" method="POST" style="display: none;">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="control">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" required class="w-full rounded-lg border border-gray-300 px-3 py-2">
                            <option value="non_audite">Non audité</option>
                            <option value="conforme">Conforme</option>
                            <option value="non_conforme">Non conforme</option>
                            <option value="partiellement_conforme">Partiellement conforme</option>
                            <option value="non_applicable">Non applicable</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Justification</label>
                        <textarea name="justification" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recommandation</label>
                        <textarea name="recommandation" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gravité du risque</label>
                            <select name="gravite_risque" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                                <option value="">Non applicable</option>
                                <option value="faible">Faible</option>
                                <option value="moyen">Moyen</option>
                                <option value="eleve">Élevé</option>
                                <option value="critique">Critique</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Probabilité du risque</label>
                            <select name="probabilite_risque" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                                <option value="">Non applicable</option>
                                <option value="faible">Faible</option>
                                <option value="moyen">Moyen</option>
                                <option value="eleve">Élevé</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Points obtenus</label>
                        <input type="number" name="points_obtenus" step="0.5" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEvaluationModal()" class="btn btn-outline">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer l'évaluation</button>
                </div>
            </form>
            
            <!-- Formulaire pour les clauses -->
            <form id="clauseEvaluationForm" method="POST" style="display: none;">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="clause">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" required class="w-full rounded-lg border border-gray-300 px-3 py-2">
                            <option value="non_audite">Non audité</option>
                            <option value="conforme">Conforme</option>
                            <option value="non_conforme">Non conforme</option>
                            <option value="partiellement_conforme">Partiellement conforme</option>
                            <option value="non_applicable">Non applicable</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Justification</label>
                        <textarea name="justification" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recommandation</label>
                        <textarea name="recommandation" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Niveau de conformité</label>
                        <select name="niveau_conformite" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                            <option value="">Non défini</option>
                            <option value="faible">Faible</option>
                            <option value="moyen">Moyen</option>
                            <option value="eleve">Élevé</option>
                            <option value="excellent">Excellent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Score (%)</label>
                        <input type="number" name="score" min="0" max="100" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEvaluationModal()" class="btn btn-outline">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer l'évaluation</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentEvaluationId = null;
let currentType = null;

function showTab(tab) {
    const controlsTab = document.getElementById('controlsTab');
    const clausesTab = document.getElementById('clausesTab');
    const controlsBtn = document.getElementById('tabControlsBtn');
    const clausesBtn = document.getElementById('tabClausesBtn');
    
    if (tab === 'controls') {
        controlsTab.style.display = 'block';
        clausesTab.style.display = 'none';
        controlsBtn.className = 'px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700';
        clausesBtn.className = 'px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300';
    } else {
        controlsTab.style.display = 'none';
        clausesTab.style.display = 'block';
        controlsBtn.className = 'px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300';
        clausesBtn.className = 'px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700';
    }
}

function openEvaluationModal(evaluationId, code, titre, type) {
    currentEvaluationId = evaluationId;
    currentType = type;
    
    document.getElementById('modalTitle').innerHTML = `Évaluation - ${code}: ${titre}`;
    
    if (type === 'control') {
        document.getElementById('controlEvaluationForm').style.display = 'block';
        document.getElementById('clauseEvaluationForm').style.display = 'none';
        document.getElementById('controlEvaluationForm').action = `/audit/evaluations/${evaluationId}`;
    } else {
        document.getElementById('controlEvaluationForm').style.display = 'none';
        document.getElementById('clauseEvaluationForm').style.display = 'block';
        document.getElementById('clauseEvaluationForm').action = `/audit/clause-evaluation/${evaluationId}`;
    }
    
    document.getElementById('evaluationModal').classList.remove('hidden');
    document.getElementById('evaluationModal').classList.add('flex');
}

function closeEvaluationModal() {
    document.getElementById('evaluationModal').classList.add('hidden');
    document.getElementById('evaluationModal').classList.remove('flex');
    currentEvaluationId = null;
    currentType = null;
}
</script>
@endpush
@endsection