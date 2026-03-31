{{-- resources/views/pages/audits/clauses.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('audits.show', $audit->id) }}" class="text-sm text-purple-600 hover:text-purple-700 mb-2 inline-block">
                ← Retour à l'audit
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Clauses ISO 27001</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $audit->titre }} - {{ $audit->entreprise->nom }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('audits.show', $audit->id) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Retour
            </a>
        </div>
    </div>

    <!-- Progress Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-500">Total clauses</p>
                <p class="text-2xl font-bold text-gray-900">{{ $clauseEvaluations->count() }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Évaluées</p>
                <p class="text-2xl font-bold text-green-600">{{ $clauseEvaluations->where('statut', '!=', 'non_audite')->count() }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Conformes</p>
                <p class="text-2xl font-bold text-green-600">{{ $clauseEvaluations->where('statut', 'conforme')->count() }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Non conformes</p>
                <p class="text-2xl font-bold text-red-600">{{ $clauseEvaluations->where('statut', 'non_conforme')->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Clauses List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N°</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($clauseEvaluations as $clauseEval)
                        @php
                            $clause = $clauseEval->clause;
                            $statusColors = [
                                'conforme' => 'bg-green-100 text-green-700',
                                'non_conforme' => 'bg-red-100 text-red-700',
                                'partiellement_conforme' => 'bg-yellow-100 text-yellow-700',
                                'non_applicable' => 'bg-gray-100 text-gray-700',
                                'non_audite' => 'bg-blue-100 text-blue-700'
                            ];
                            $statusLabels = [
                                'conforme' => 'Conforme',
                                'non_conforme' => 'Non conforme',
                                'partiellement_conforme' => 'Partiellement conforme',
                                'non_applicable' => 'Non applicable',
                                'non_audite' => 'Non audité'
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm font-semibold text-purple-600">{{ $clause->numero }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $clause->titre }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($clause->description, 100) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$clauseEval->statut] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $statusLabels[$clauseEval->statut] ?? $clauseEval->statut }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($clauseEval->score > 0)
                                    <div class="text-sm font-medium text-gray-900">{{ $clauseEval->score }}%</div>
                                    <div class="w-20 bg-gray-200 rounded-full h-1.5 mt-1">
                                        <div class="bg-purple-600 rounded-full h-1.5" style="width: {{ $clauseEval->score }}%"></div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('audits.clause.edit', [$audit->id, $clauseEval->id]) }}" 
                                   class="text-purple-600 hover:text-purple-900">
                                    Évaluer
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end">
        <a href="{{ route('audits.show', $audit->id) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            Retour à l'audit
        </a>
    </div>
</div>
@endsection