@extends('layouts.app')

@section('content')
<style>
    .a4-container { background: #525659; min-height: 100vh; padding: 40px 0; }
    .a4-page {
        background: white;
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto 30px auto;
        padding: 20mm;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
        position: relative;
    }
    @media print {
        body { background: none; }
        .a4-container { background: none; padding: 0; }
        .a4-page { margin: 0; box-shadow: none; width: 100%; }
        .no-print { display: none; }
    }
    .badge-conforme { background: #10b981; color: white; padding: 2px 8px; border-radius: 9999px; font-size: 10px; }
    .badge-non-conforme { background: #ef4444; color: white; padding: 2px 8px; border-radius: 9999px; font-size: 10px; }
    .badge-partiel { background: #f59e0b; color: white; padding: 2px 8px; border-radius: 9999px; font-size: 10px; }
    .badge-audite { background: #6b7280; color: white; padding: 2px 8px; border-radius: 9999px; font-size: 10px; }
    .badge-traite { background: #10b981; color: white; padding: 2px 8px; border-radius: 9999px; font-size: 10px; }
    .badge-en-cours { background: #f59e0b; color: white; padding: 2px 8px; border-radius: 9999px; font-size: 10px; }
    .badge-a-traiter { background: #ef4444; color: white; padding: 2px 8px; border-radius: 9999px; font-size: 10px; }
</style>

<div class="a4-container">
    <form action="{{ route('report.store', $audit->id) }}" method="POST">
        @csrf

        {{-- PAGE DE GARDE --}}
        <div class="a4-page">
            <div class="flex justify-between items-start mb-20">
                <div class="text-gray-400 uppercase text-sm">Rapport d'Audit - {{ date('Y') }}</div>
                <div class="w-32 h-32 bg-gray-100 border flex items-center justify-center text-gray-400 text-xs text-center p-2">
                    [Logo Expert]
                </div>
            </div>

            <div class="text-center mt-40">
                <h1 class="text-3xl font-bold text-blue-900 uppercase tracking-widest">Rapport d'Audit de la Sécurité</h1>
                <h2 class="text-xl text-gray-600 mt-4">Système d'Information de :</h2>
                <div class="mt-6 text-2xl font-semibold text-gray-800 underline">
                    {{ $audit->entreprise->nom ?? 'NOM_ORGANISME_AUDITÉ' }}
                </div>
            </div>

            <div class="absolute bottom-20 left-20 right-20">
                <div class="grid grid-cols-2 gap-10 border-t pt-10">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">Date de l'audit</p>
                        <p class="text-sm">{{ $audit->date_debut->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">Expert Auditeur</p>
                        <p class="text-sm">{{ auth()->user()->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- PAGE 1 : SYNTHÈSE DES RÉSULTATS --}}
        <div class="a4-page">
            <h2 class="text-xl font-bold text-blue-900 mb-6">1. Synthèse des résultats de l'audit</h2>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="p-4 bg-blue-50 border-l-4 border-blue-900">
                    <h3 class="font-bold mb-2">Statistiques globales</h3>
                    <p>Total contrôles: <strong>{{ $data['stats']['total_controls'] }}</strong></p>
                    <p class="text-green-600">Conformes: <strong>{{ $data['stats']['conforme'] }}</strong></p>
                    <p class="text-red-600">Non conformes: <strong>{{ $data['stats']['non_conforme'] }}</strong></p>
                    <p class="text-orange-600">Partiellement conformes: <strong>{{ $data['stats']['partiel'] }}</strong></p>
                    <p class="text-gray-500">Non applicables: <strong>{{ $data['stats']['non_applicable'] }}</strong></p>
                    <p class="text-gray-500">Non audités: <strong>{{ $data['stats']['non_audite'] }}</strong></p>
                    <p class="text-blue-600 font-bold mt-2">Taux de conformité: {{ $data['stats']['conformity_rate'] }}%</p>
                    <p class="text-purple-600">Progression: {{ $data['stats']['progress_rate'] }}%</p>
                    <p class="text-green-600">Score global: {{ $data['stats']['global_score'] }}%</p>
                </div>
                <div class="p-4 bg-gray-50 border-l-4 border-gray-400">
                    <h3 class="font-bold mb-2">Niveaux de risque</h3>
                    <p class="text-red-600">Critiques: <strong>{{ $data['risks_by_level']['critique'] }}</strong></p>
                    <p class="text-orange-600">Élevés: <strong>{{ $data['risks_by_level']['eleve'] }}</strong></p>
                    <p class="text-yellow-600">Moyens: <strong>{{ $data['risks_by_level']['moyen'] }}</strong></p>
                    <p class="text-green-600">Faibles: <strong>{{ $data['risks_by_level']['faible'] }}</strong></p>
                </div>
            </div>

            <!-- État de maturité -->
            <div class="mb-6">
                <h3 class="text-md font-bold text-blue-800 mb-3">État de maturité de la sécurité</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="w-full">
                        <table class="w-full border text-sm">
                            <tr class="bg-blue-900 text-white">
                                <th class="p-2">Domaine</th>
                                <th class="p-2">Score / 5</th>
                            </tr>
                            <tr>
                                <td class="border p-2">Mesures Organisationnelles</td>
                                <td class="border p-2 text-center font-bold">{{ $data['stats']['scores']['organisationnel'] ?? 0 }} / 5</td>
                            </tr>
                            <tr>
                                <td class="border p-2">Mesures liées aux Personnes</td>
                                <td class="border p-2 text-center font-bold">{{ $data['stats']['scores']['personnes'] ?? 0 }} / 5</td>
                            </tr>
                            <tr>
                                <td class="border p-2">Mesures Physiques</td>
                                <td class="border p-2 text-center font-bold">{{ $data['stats']['scores']['physique'] ?? 0 }} / 5</td>
                            </tr>
                            <tr>
                                <td class="border p-2">Mesures Technologiques</td>
                                <td class="border p-2 text-center font-bold">{{ $data['stats']['scores']['technologique'] ?? 0 }} / 5</td>
                            </tr>
                        </table>
                    </div>
                    <div class="bg-gray-100 p-4 rounded text-center">
                        <p class="text-sm text-gray-600">Score global de maturité</p>
                        <p class="text-3xl font-bold text-blue-600">
                            {{ round(array_sum($data['stats']['scores']) / 4, 1) }} / 5
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- PAGE 2 : NON-CONFORMITÉS AVEC TRAITEMENT --}}
        <div class="a4-page">
            <h2 class="text-xl font-bold text-blue-900 mb-6">2. Non-conformités et plans d'action</h2>
            
            @if($data['non_conformites']->count() > 0)
                @foreach($data['non_conformites'] as $nc)
                <div class="border {{ $nc['statut_traitement'] == 'valide' ? 'border-green-300 bg-green-50' : 'border-red-200 bg-red-50' }} p-4 rounded mb-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-mono font-bold text-red-800 text-lg">{{ $nc['code'] }}</span>
                                @if($nc['statut_traitement'])
                                    <span class="
                                        @if($nc['statut_traitement'] == 'valide') badge-traite
                                        @elseif($nc['statut_traitement'] == 'en_cours') badge-en-cours
                                        @else badge-a-traiter
                                        @endif
                                    ">
                                        @if($nc['statut_traitement'] == 'valide') ✓ Traité
                                        @elseif($nc['statut_traitement'] == 'en_cours') 🔄 En cours
                                        @else ⏳ À traiter
                                        @endif
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-gray-900 mt-1">{{ $nc['titre'] }}</h3>
                            <p class="text-sm text-gray-700 mt-2">{{ Str::limit($nc['description'], 200) }}</p>
                            
                            @if($nc['justification'])
                            <div class="mt-3 bg-white p-2 rounded">
                                <p class="text-sm font-semibold">Justification:</p>
                                <p class="text-sm">{{ $nc['justification'] }}</p>
                            </div>
                            @endif
                            
                            @if($nc['plan_action'])
                            <div class="mt-3 bg-blue-50 p-3 rounded">
                                <p class="text-sm font-semibold text-blue-800">📋 Plan d'action:</p>
                                <p class="text-sm">{{ $nc['plan_action'] }}</p>
                                @if($nc['responsable'])
                                <p class="text-xs mt-1 text-gray-600">👤 Responsable: {{ $nc['responsable'] }}</p>
                                @endif
                                @if($nc['date_echeance'])
                                <p class="text-xs text-gray-600">📅 Échéance: {{ \Carbon\Carbon::parse($nc['date_echeance'])->format('d/m/Y') }}</p>
                                @endif
                            </div>
                            @endif
                            
                            @if($nc['recommandation'])
                            <div class="mt-2 bg-green-50 p-2 rounded">
                                <p class="text-sm font-semibold text-green-700">💡 Recommandation:</p>
                                <p class="text-sm text-green-700">{{ $nc['recommandation'] }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="text-right ml-4">
                            <span class="badge-non-conforme">Non conforme</span>
                            @if($nc['niveau_risque'])
                            <p class="text-xs text-red-600 mt-1 font-bold">{{ strtoupper($nc['niveau_risque']) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center p-8 bg-green-50 rounded">
                    <p class="text-green-600">✓ Aucune non-conformité détectée</p>
                    <p class="text-sm text-gray-500 mt-2">Tous les contrôles sont conformes aux exigences</p>
                </div>
            @endif
        </div>

        {{-- PAGE 3 : SYNTHÈSE DES TRAITEMENTS --}}
        @if($data['non_conformites']->count() > 0)
        <div class="a4-page">
            <h2 class="text-xl font-bold text-blue-900 mb-6">3. Synthèse des traitements</h2>
            
            <div class="grid grid-cols-4 gap-3 mb-6">
                <div class="bg-red-100 p-3 rounded text-center">
                    <p class="text-sm text-red-600">À traiter</p>
                    <p class="text-2xl font-bold text-red-700">{{ $data['traitement_stats']['a_traiter'] }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded text-center">
                    <p class="text-sm text-orange-600">En cours</p>
                    <p class="text-2xl font-bold text-orange-700">{{ $data['traitement_stats']['en_cours'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded text-center">
                    <p class="text-sm text-blue-600">Traités</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $data['traitement_stats']['traite'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded text-center">
                    <p class="text-sm text-green-600">Validés</p>
                    <p class="text-2xl font-bold text-green-700">{{ $data['traitement_stats']['valide'] }}</p>
                </div>
            </div>

            <h3 class="text-md font-bold text-blue-800 mb-3">Tableau de suivi des actions</h3>
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-2 text-left">Contrôle</th>
                        <th class="border p-2 text-left">Plan d'action</th>
                        <th class="border p-2 text-left">Responsable</th>
                        <th class="border p-2 text-center">Échéance</th>
                        <th class="border p-2 text-center">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['non_conformites'] as $nc)
                    <tr>
                        <td class="border p-2 font-mono">{{ $nc['code'] }}</td>
                        <td class="border p-2">{{ Str::limit($nc['plan_action'] ?? 'Non défini', 50) }}</td>
                        <td class="border p-2">{{ $nc['responsable'] ?? 'Non assigné' }}</td>
                        <td class="border p-2 text-center">{{ $nc['date_echeance'] ? \Carbon\Carbon::parse($nc['date_echeance'])->format('d/m/Y') : 'Non définie' }}</td>
                        <td class="border p-2 text-center">
                            @if($nc['statut_traitement'] == 'valide')
                                <span class="badge-traite">Validé</span>
                            @elseif($nc['statut_traitement'] == 'traite')
                                <span class="badge-traite">Traité</span>
                            @elseif($nc['statut_traitement'] == 'en_cours')
                                <span class="badge-en-cours">En cours</span>
                            @else
                                <span class="badge-a-traiter">À traiter</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- PAGE 4 : DÉTAIL DES CONTRÔLES PAR CATÉGORIE --}}
        <div class="a4-page">
            <h2 class="text-xl font-bold text-blue-900 mb-6">4. Détail des résultats par catégorie</h2>
            
            @foreach($data['controls_by_category'] as $category => $categoryData)
            <div class="mb-8">
                <h3 class="font-bold text-md bg-blue-900 text-white p-2 rounded mb-3">{{ $category }}</h3>
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border p-2 text-left">Code</th>
                            <th class="border p-2 text-left">Contrôle</th>
                            <th class="border p-2 text-center w-24">Statut</th>
                            <th class="border p-2 text-center w-20">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categoryData['controls'] as $evaluation)
                        <tr>
                            <td class="border p-2 font-mono">{{ $evaluation->controle->code }}</td>
                            <td class="border p-2">{{ Str::limit($evaluation->controle->titre, 80) }}</td>
                            <td class="border p-2 text-center">
                                @if($evaluation->statut == 'conforme')
                                    <span class="badge-conforme">Conforme</span>
                                @elseif($evaluation->statut == 'non_conforme')
                                    <span class="badge-non-conforme">Non conforme</span>
                                @elseif($evaluation->statut == 'partiellement_conforme')
                                    <span class="badge-partiel">Partiel</span>
                                @else
                                    <span class="badge-audite">Non audité</span>
                                @endif
                            </td>
                            <td class="border p-2 text-center">
                                {{ $evaluation->points_obtenus }}/{{ $evaluation->controle->ponderation_defaut ?? 1 }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-2 text-right">
                    <span class="text-xs text-gray-500">Taux de conformité: {{ $categoryData['taux_conformite'] }}% ({{ $categoryData['conforme'] }}/{{ $categoryData['total'] - $categoryData['non_applicable'] }})</span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- PAGE 5 : PLAN D'ACTION GLOBAL --}}
        <div class="a4-page">
            <h2 class="text-xl font-bold text-blue-900 mb-6">5. Plan d'action global</h2>

            <div class="p-4 bg-blue-50 border border-blue-200 text-sm mb-6 italic">
                <p>Le présent plan d'action est établi en commun accord avec <strong>{{ $audit->entreprise->nom }}</strong>.</p>
            </div>

            <table class="w-full border-collapse border border-gray-400 text-sm">
                <thead>
                    <tr class="bg-blue-900 text-white">
                        <th class="border p-2">Action corrective</th>
                        <th class="border p-2">Priorité</th>
                        <th class="border p-2">Responsable</th>
                        <th class="border p-2">Délai</th>
                        <th class="border p-2">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['non_conformites'] as $nc)
                    <tr>
                        <td class="border p-2">
                            <span class="font-mono">{{ $nc['code'] }}</span><br>
                            <span class="text-xs">{{ Str::limit($nc['titre'], 40) }}</span>
                        </td>
                        <td class="border p-2 text-center">
                            @if($nc['priorite'] == 'haute' || $nc['niveau_risque'] == 'critique')
                                <span class="text-red-600 font-bold">Urgente</span>
                            @elseif($nc['priorite'] == 'moyenne' || $nc['niveau_risque'] == 'eleve')
                                <span class="text-orange-600 font-bold">Haute</span>
                            @else
                                <span class="text-yellow-600">Normale</span>
                            @endif
                        </td>
                        <td class="border p-2">{{ $nc['responsable'] ?? 'À définir' }}</td>
                        <td class="border p-2">{{ $nc['date_echeance'] ? \Carbon\Carbon::parse($nc['date_echeance'])->format('d/m/Y') : 'À définir' }}</td>
                        <td class="border p-2 text-center">
                            @if($nc['statut_traitement'] == 'valide')
                                <span class="badge-traite">Validé</span>
                            @elseif($nc['statut_traitement'] == 'traite')
                                <span class="badge-traite">Traité</span>
                            @elseif($nc['statut_traitement'] == 'en_cours')
                                <span class="badge-en-cours">En cours</span>
                            @else
                                <span class="badge-a-traiter">À traiter</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="border p-4 text-center text-green-600">
                            ✓ Aucune action corrective nécessaire - Tous les contrôles sont conformes
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-10 text-center border-t pt-10">
                <div class="grid grid-cols-2 gap-20">
                    <div>
                        <p class="text-xs font-bold underline mb-10">Pour l'Organisme Audité</p>
                        <div class="h-20 border border-dashed border-gray-400"></div>
                        <p class="text-xs mt-2 text-gray-500">Date et signature</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold underline mb-10">Pour l'Expert Auditeur</p>
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <div class="h-16 border border-dashed border-gray-400 mt-2"></div>
                        <p class="text-xs mt-2 text-gray-500">Date : {{ date('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- BARRE D'ACTIONS FIXE --}}
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t p-4 no-print shadow-lg z-50">
            <div class="container mx-auto flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    📄 Rapport d'audit - {{ $audit->entreprise->nom }}
                </div>
                <div class="flex space-x-4">
                    <button type="button" onclick="window.print()" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition">
                        🖨️ Imprimer / PDF
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                        💾 Enregistrer le rapport
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection