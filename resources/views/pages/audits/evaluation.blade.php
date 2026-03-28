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
    </div>

    <div class="space-y-4">
        @foreach($evaluations as $evaluation)
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
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-mono text-sm font-semibold text-purple-600">{{ $control->code }}</span>
                            <span class="badge {{ $statusColors[$evaluation->statut] ?? 'bg-gray-100' }}">
                                {{ ucfirst(str_replace('_', ' ', $evaluation->statut)) }}
                            </span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $control->titre }}</h3>
                        <p class="text-sm text-gray-600 mb-3">{{ $control->description }}</p>
                        
                        @if($evaluation->justification)
                            <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                <p class="text-xs text-gray-500 mb-1">Justification:</p>
                                <p class="text-sm text-gray-700">{{ $evaluation->justification }}</p>
                            </div>
                        @endif
                        
                        <div class="flex gap-2">
                            <button type="button" 
                                    class="btn btn-outline-primary btn-sm"
                                    onclick="openEvaluationModal({{ $evaluation->id }}, '{{ $control->code }}', '{{ $control->titre }}')">
                                Évaluer ce contrôle
                            </button>
                            <a href="{{ route('evidence.collect') }}" class="btn btn-outline btn-sm">
                                Voir les preuves
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal d'évaluation -->
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
            <form id="evaluationForm" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT">
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
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentEvaluationId = null;

function openEvaluationModal(evaluationId, code, titre) {
    currentEvaluationId = evaluationId;
    document.getElementById('modalTitle').innerHTML = `Évaluation - ${code}: ${titre}`;
    document.getElementById('evaluationForm').action = `/audit/evaluations/${evaluationId}`;
    document.getElementById('evaluationModal').classList.remove('hidden');
    document.getElementById('evaluationModal').classList.add('flex');
}

function closeEvaluationModal() {
    document.getElementById('evaluationModal').classList.add('hidden');
    document.getElementById('evaluationModal').classList.remove('flex');
}
</script>
@endpush
@endsection