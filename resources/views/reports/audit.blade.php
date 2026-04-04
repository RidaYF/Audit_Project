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
    textarea { border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px; width: 100%; font-size: 14px; }
</style>

<div class="a4-container">
    <form action="{{ route('report.store', $audit->id) }}" method="POST">
        @csrf

        {{-- PAGE DE GARDE --}}
        <div class="a4-page">
            <div class="flex justify-between items-start mb-20">
                <div class="text-gray-400 uppercase text-sm">Modèle de Rapport d'Audit v2.1</div>
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
                        <label class="block text-xs font-bold text-gray-500 uppercase">Version du document</label>
                        <input type="text" name="version" value="{{ $rapport->version ?? '2.1' }}" class="w-full border-b focus:border-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase">Expert Auditeur</label>
                        <input type="text" name="expert_nom" value="{{ $rapport->expert_nom ?? auth()->user()->name }}" class="w-full border-b focus:border-blue-500 outline-none">
                    </div>
                </div>
            </div>
        </div>

        {{-- PAGE 1 : AVANT-PROPOS & ADMINISTRATION --}}
<div class="a4-page">
    <h2 class="text-xl font-bold text-blue-900 mb-6">1. Avant-propos</h2>

    {{-- 1.1 Confidentialité --}}
    <section class="mb-8">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">1.1 Confidentialité du document</h3>
        <div class="bg-gray-50 p-4 border-l-4 border-blue-800">
            <p class="text-sm mb-2 font-medium">Le présent document est confidentiel et sa confidentialité consiste à :</p>
            <textarea name="confidentialite_texte" rows="6" class="w-full bg-transparent border-none focus:ring-0 text-sm leading-relaxed text-gray-700 italic">
- La non divulgation desdites informations confidentielles auprès de tierce partie,
- La non reproduction des informations dites confidentielles, sauf accord de l’organisme audité ({{ $audit->entreprise->nom }}),
- Ne pas profiter ou faire profiter tierce partie du contenu de ces informations en matière de savoir-faire,
- Considérer toutes les informations relatives à la production et au système d’information de l’organisme audité déclarées Confidentielles.
            </textarea>
        </div>
    </section>

    {{-- 1.2 Historique des modifications --}}
    <section class="mb-8">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">1.2 Historique des modifications</h3>
        <table class="w-full border-collapse border border-gray-300 text-xs">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 p-2 w-16">Version</th>
                    <th class="border border-gray-300 p-2 w-24">Date</th>
                    <th class="border border-gray-300 p-2 w-32">Auteur</th>
                    <th class="border border-gray-300 p-2">Modifications</th>
                </tr>
            </thead>
            <tbody>
                @php $historique = $rapport->planning_reel ?? [['v' => '2.1', 'd' => date('d/m/Y'), 'a' => $audit->auditeur->name, 'm' => 'Version initiale']]; @endphp
                @foreach(range(0, 2) as $i)
                <tr>
                    <td class="border border-gray-300 p-0"><input type="text" name="planning_reel[{{$i}}][v]" value="{{ $historique[$i]['v'] ?? '' }}" class="w-full p-2 border-none"></td>
                    <td class="border border-gray-300 p-0"><input type="text" name="planning_reel[{{$i}}][d]" value="{{ $historique[$i]['d'] ?? '' }}" class="w-full p-2 border-none"></td>
                    <td class="border border-gray-300 p-0"><input type="text" name="planning_reel[{{$i}}][a]" value="{{ $historique[$i]['a'] ?? '' }}" class="w-full p-2 border-none"></td>
                    <td class="border border-gray-300 p-0"><input type="text" name="planning_reel[{{$i}}][m]" value="{{ $historique[$i]['m'] ?? '' }}" class="w-full p-2 border-none"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    {{-- 1.3 Diffusion du document --}}
    <section class="mb-8">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">1.3 Diffusion du document</h3>
        
        {{-- Coté Expert --}}
        <div class="mb-4">
            <p class="text-xs font-bold bg-blue-50 p-1 mb-1 border">Diffusion (côté {{ auth()->user()->name ?? 'Expert Auditeur' }})</p>
            <table class="w-full border-collapse border border-gray-300 text-[10px]">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 p-1">Nom</th>
                        <th class="border border-gray-300 p-1">Prénom</th>
                        <th class="border border-gray-300 p-1">Titre</th>
                        <th class="border border-gray-300 p-1">Tél</th>
                        <th class="border border-gray-300 p-1">Mail</th>
                    </tr>
                </thead>
                <tbody>
                    @php $expert_diff = $rapport->equipe_expert ?? [[]]; @endphp
                    @foreach(range(0, 2) as $i)
                    <tr>
                        <td class="border border-gray-300 p-0"><input type="text" name="equipe_expert[{{$i}}][nom]" value="{{ $expert_diff[$i]['nom'] ?? '' }}" class="w-full p-1 border-none"></td>
                        <td class="border border-gray-300 p-0"><input type="text" name="equipe_expert[{{$i}}][prenom]" value="{{ $expert_diff[$i]['prenom'] ?? '' }}" class="w-full p-1 border-none"></td>
                        <td class="border border-gray-300 p-0"><input type="text" name="equipe_expert[{{$i}}][titre]" value="{{ $expert_diff[$i]['titre'] ?? '' }}" class="w-full p-1 border-none"></td>
                        <td class="border border-gray-300 p-0"><input type="text" name="equipe_expert[{{$i}}][tel]" value="{{ $expert_diff[$i]['tel'] ?? '' }}" class="w-full p-1 border-none"></td>
                        <td class="border border-gray-300 p-0"><input type="text" name="equipe_expert[{{$i}}][mail]" value="{{ $expert_diff[$i]['mail'] ?? '' }}" class="w-full p-1 border-none"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Coté Organisme Audité --}}
        <div>
            <p class="text-xs font-bold bg-green-50 p-1 mb-1 border">Diffusion (côté {{ $audit->entreprise->nom }})</p>
            <table class="w-full border-collapse border border-gray-300 text-[10px]">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 p-1">Nom</th>
                        <th class="border border-gray-300 p-1">Prénom</th>
                        <th class="border border-gray-300 p-1">Titre</th>
                        <th class="border border-gray-300 p-1">Tél</th>
                        <th class="border border-gray-300 p-1">Mail</th>
                    </tr>
                </thead>
                <tbody>
                    @php $client_diff = $rapport->equipe_audite ?? [[]]; @endphp
                    @foreach(range(0, 2) as $i)
                    <tr>
                        <td class="border border-gray-300 p-0"><input type="text" name="equipe_audite[{{$i}}][nom]" value="{{ $client_diff[$i]['nom'] ?? '' }}" class="w-full p-1 border-none"></td>
                        <td class="border border-gray-300 p-0"><input type="text" name="equipe_audite[{{$i}}][prenom]" value="{{ $client_diff[$i]['prenom'] ?? '' }}" class="w-full p-1 border-none"></td>
                        <td class="border border-gray-300 p-0"><input type="text" name="equipe_audite[{{$i}}][titre]" value="{{ $client_diff[$i]['titre'] ?? '' }}" class="w-full p-1 border-none"></td>
                        <td class="border border-gray-300 p-0"><input type="text" name="equipe_audite[{{$i}}][tel]" value="{{ $client_diff[$i]['tel'] ?? '' }}" class="w-full p-1 border-none"></td>
                        <td class="border border-gray-300 p-0"><input type="text" name="equipe_audite[{{$i}}][mail]" value="{{ $client_diff[$i]['mail'] ?? '' }}" class="w-full p-1 border-none"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
{{-- PAGE 2 : CADRE DE LA MISSION & DÉFINITIONS --}}
        <div class="a4-page">
            {{-- Chapitre 2 --}}
            <section id="chapitre-2" class="mb-10">
                <h2 class="text-xl font-bold text-blue-900 mb-6 uppercase">2. Cadre de la mission</h2>
                <div class="space-y-4">
                    <p class="text-sm text-gray-600 italic">Rappelez ici les objectifs et les limites de l'audit.</p>
                    <textarea name="cadre_mission" rows="10" class="leading-relaxed">
- Rappeler le cadre de la mission d’audit : Évaluation de la sécurité du SI de {{ $audit->entreprise->nom ?? 'l\'organisme' }}.
- Indiquer si la présente mission est un audit exhaustif ou audit de suivi (mission sur 3 années).
- Présenter l’objectif de cette mission d’audit (Etat de conformité par rapport à un standard, recommandations, plan d’action).
- Indiquer, le cas échéant, si la présente mission rentre dans le cadre de la préparation à la certification ISO 27001 ou dans une autre démarche de conformité.
- Indiquer, le cas échéant, le(s) standard(s) métier de référence par rapport auquel est réalisée la présente mission d’audit.
- Indiquer les limites et les contraintes de l’audit (échantillonnage, changements depuis l’audit réalisés le {{ $audit->created_at->format('d/m/Y') }}, etc).
                    </textarea>
                </div>
            </section>

            {{-- Chapitre 3 --}}
            <section id="chapitre-3" class="mb-10">
                <h2 class="text-xl font-bold text-blue-900 mb-6 uppercase">3. Termes et définitions</h2>
                <textarea name="methodologie" rows="6" placeholder="Donner les définitions des termes utilisés dans le présent rapport.">
- SI : Système d'Information.
- PSSI : Politique de Sécurité des Systèmes d'Information.
- Audit : Processus méthodique, indépendant et documenté.
- DIC : Disponibilité, Intégrité et Confidentialité des données.
                </textarea>
            </section>

            {{-- Chapitre 4 --}}
            <section id="chapitre-4">
                <h2 class="text-xl font-bold text-blue-900 mb-6 uppercase">4. Références</h2>
                <textarea name="outils_utilises" rows="5" placeholder="Indiquer tous les documents de référence utilisés.">
- Norme ISO/CEI 27001:2022.
- Guide d'audit de la DGSSI.
- Documentation interne de {{ $audit->entreprise->nom ?? 'l\'organisme' }}.
                </textarea>
            </section>
        </div>

        {{-- PAGE 5 : PRÉSENTATION DE L'ORGANISME --}}
        <div class="a4-page">
            <section id="chapitre-5">
                <h2 class="text-xl font-bold text-blue-900 mb-6 uppercase">5. Présentation de l’organisme audité</h2>
                
                {{-- 5.1 Fiche d'identité automatisée --}}
                <div class="mb-8">
                    <h3 class="text-md font-bold text-blue-800 mb-3 italic underline">5.1 Fiche d'identité (Annexe 1)</h3>
                    <div class="grid grid-cols-2 gap-px bg-gray-300 border border-gray-300">
                        <div class="bg-gray-100 p-2 font-bold text-xs">Nom de l'organisme</div>
                        <div class="bg-white p-2 text-xs">{{ $audit->entreprise->nom }}</div>
                        
                        <div class="bg-gray-100 p-2 font-bold text-xs">Secteur d'activité</div>
                        <div class="bg-white p-2 text-xs">{{ $audit->entreprise->secteur }}</div>
                        
                        <div class="bg-gray-100 p-2 font-bold text-xs">Taille / Effectif</div>
                        <div class="bg-white p-2 text-xs">{{ $audit->entreprise->taille }} ({{ $audit->entreprise->effectif ?? 'N/A' }} employés)</div>
                        
                        <div class="bg-gray-100 p-2 font-bold text-xs">Localisation / Étendue</div>
                        <div class="bg-white p-2 text-xs">{{ $audit->entreprise->localisation ?? 'Non spécifiée' }}</div>

                        <div class="bg-gray-100 p-2 font-bold text-xs">Chiffre d'affaires</div>
                        <div class="bg-white p-2 text-xs">{{ $audit->entreprise->chiffre_affaires ?? 'Confidentiel' }}</div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-xs font-bold text-blue-800 mb-1 italic">Missions et Contexte Métier :</label>
                        <textarea name="entreprise_contexte" rows="4" class="text-xs bg-blue-50 border-blue-100">
{{ $audit->entreprise->contexte_metier ?? 'Décrire les missions principales ici...' }}
                        </textarea>
                    </div>

                    <div class="mt-2 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-blue-800 mb-1 italic">Systèmes Clés :</label>
                            <div class="p-2 border rounded bg-gray-50 text-[10px] min-h-[60px]">
                                {{ $audit->entreprise->systemes_cles ?? 'Aucun système clé listé.' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-blue-800 mb-1 italic">Départements :</label>
                            <div class="p-2 border rounded bg-gray-50 text-[10px] min-h-[60px]">
                                {{ $audit->entreprise->departements ?? 'Information non disponible.' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 5.2 Cartographie --}}
                <div class="mb-8">
                    <h3 class="text-md font-bold text-blue-800 mb-2 italic underline">5.2 Cartographie des processus et flux</h3>
                    <p class="text-[11px] text-gray-600 mb-2">Ci-dessous la représentation des processus métier et des flux de données associés :</p>
                    <div class="h-48 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center bg-gray-50">
                        @if($audit->entreprise->image_logo)
                            <img src="{{ Storage::url($audit->entreprise->image_logo) }}" class="h-16 opacity-20 grayscale mb-2">
                        @endif
                        <span class="text-gray-400 text-[10px] uppercase tracking-widest">[ Insérer Schéma des Flux / Cartographie Applicative ]</span>
                    </div>
                </div>

                {{-- 5.3 Tableau DIC --}}
                <div>
                    <h3 class="text-md font-bold text-blue-800 mb-2 italic underline">5.3 Exigences de sécurité par processus (Annexe 2)</h3>
                    <p class="text-[10px] text-gray-500 mb-3 italic">Identification des processus critiques basée sur les actifs critiques listés : 
                        <span class="text-blue-900 font-medium">{{ $audit->entreprise->actifs_critiques }}</span>
                    </p>
                    
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-blue-900 text-white text-[10px] uppercase">
                                <th class="border border-gray-300 p-2 w-1/3">Processus Métier</th>
                                <th class="border border-gray-300 p-2">Disp. (D)</th>
                                <th class="border border-gray-300 p-2">Intég. (I)</th>
                                <th class="border border-gray-300 p-2">Conf. (C)</th>
                                <th class="border border-gray-300 p-2">Criticité / Justification</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                // On peut pré-remplir avec des lignes vides ou parser les actifs_critiques
                                $lignes = 5; 
                            @endphp
                            @foreach(range(0, $lignes - 1) as $i)
                            <tr>
                                <td class="border p-0"><input type="text" name="proc_dic[{{$i}}][p]" class="w-full p-2 border-none text-[10px]" placeholder="Nom du processus..."></td>
                                <td class="border p-0 text-center">
                                    <select name="proc_dic[{{$i}}][d]" class="w-full border-none text-[10px] bg-transparent">
                                        <option value="H">Haut</option><option value="M">Moyen</option><option value="B">Bas</option>
                                    </select>
                                </td>
                                <td class="border p-0 text-center">
                                    <select name="proc_dic[{{$i}}][i]" class="w-full border-none text-[10px] bg-transparent">
                                        <option value="H">Haut</option><option value="M">Moyen</option><option value="B">Bas</option>
                                    </select>
                                </td>
                                <td class="border p-0 text-center">
                                    <select name="proc_dic[{{$i}}][c]" class="w-full border-none text-[10px] bg-transparent">
                                        <option value="H">Haut</option><option value="M">Moyen</option><option value="B">Bas</option>
                                    </select>
                                </td>
                                <td class="border p-0"><input type="text" name="proc_dic[{{$i}}][crit]" class="w-full p-2 border-none text-[10px]" placeholder="Ex: Majeure"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        {{-- PAGE 4 : CHAMP D'AUDIT --}}
        <div class="a4-page">
            <section id="chapitre-6">
                <h2 class="text-xl font-bold text-blue-900 mb-6 uppercase">6. Champ d’audit</h2>

                {{-- 6.1 Périmètre géographique --}}
                <div class="mb-8">
                    <h3 class="text-md font-bold text-blue-800 mb-2 italic underline">6.1 Périmètre géographique</h3>
                    <p class="text-[11px] mb-2 text-gray-600">Liste des structures et sites physiques inclus dans l'audit :</p>
                    <table class="w-full border-collapse border border-gray-300 mb-3">
                        <thead>
                            <tr class="bg-gray-100 text-[10px]">
                                <th class="border border-gray-300 p-2">Structure / Site</th>
                                <th class="border border-gray-300 p-2">Localisation</th>
                                <th class="border border-gray-300 p-2">Responsable de site</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(range(0, 2) as $i)
                            <tr>
                                <td class="border p-0"><input type="text" name="perimetre[{{$i}}][s]" class="w-full p-2 border-none text-[10px]" value="{{ $i == 0 ? $audit->entreprise->nom : '' }}"></td>
                                <td class="border p-0"><input type="text" name="perimetre[{{$i}}][l]" class="w-full p-2 border-none text-[10px]" value="{{ $i == 0 ? $audit->entreprise->localisation : '' }}"></td>
                                <td class="border p-0"><input type="text" name="perimetre[{{$i}}][r]" class="w-full p-2 border-none text-[10px]"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <textarea name="justification_perimetre" rows="3" class="text-[10px] bg-gray-50" placeholder="Justifier ici le choix du périmètre et les critères d’échantillonnage..."></textarea>
                </div>

                {{-- 6.2 Description des SI --}}
                <div class="mb-8">
                    <h3 class="text-md font-bold text-blue-800 mb-2 italic underline">6.2 Description des systèmes d’information</h3>
                    <p class="text-[11px] mb-2 text-gray-600 font-semibold">Composants du SI de {{ $audit->entreprise->nom }} (Annexe 3) :</p>
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100 text-[10px]">
                                <th class="border border-gray-300 p-2">Composant (Serveur, App, BD...)</th>
                                <th class="border border-gray-300 p-2">Description / Rôle</th>
                                <th class="border border-gray-300 p-2">Statut (Inclus/Exclu)</th>
                                <th class="border border-gray-300 p-2">Justification d'exclusion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(range(0, 3) as $i)
                            <tr>
                                <td class="border p-0"><input type="text" name="composants[{{$i}}][c]" class="w-full p-2 border-none text-[10px]"></td>
                                <td class="border p-0"><input type="text" name="composants[{{$i}}][d]" class="w-full p-2 border-none text-[10px]"></td>
                                <td class="border p-0 text-center">
                                    <select name="composants[{{$i}}][s]" class="w-full border-none text-[10px] bg-transparent">
                                        <option value="inclus">Inclus</option><option value="exclu">Exclu</option>
                                    </select>
                                </td>
                                <td class="border p-0"><input type="text" name="composants[{{$i}}][j]" class="w-full p-2 border-none text-[10px]"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- 6.3 Schéma synoptique --}}
                <div>
                    <h3 class="text-md font-bold text-blue-800 mb-2 italic underline">6.3 Schéma synoptique de l’architecture réseau</h3>
                    <div class="h-64 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center bg-gray-50 relative">
                        <span class="text-gray-400 text-[10px] uppercase tracking-widest text-center">
                            [ Insérer l'Architecture Réseau de {{ $audit->entreprise->nom }} ]<br>
                            (LAN, WAN, Segmentation, DMZ...)
                        </span>
                    </div>
                </div>
            </section>
        </div>

        {{-- PAGE 5 : MÉTHODOLOGIE D'AUDIT --}}
        <div class="a4-page">
            <section id="chapitre-7">
                <h2 class="text-xl font-bold text-blue-900 mb-6 uppercase">7. Méthodologie d’audit</h2>
                
                <div class="mb-6">
                    <textarea name="methodologie_details" rows="8" class="text-sm leading-relaxed">
L'audit est réalisé selon une approche basée sur les risques. La maturité des contrôles est évaluée sur les 4 domaines du référentiel DGSSI :
1. Mesures organisationnelles (Gouvernance, PSSI, etc.)
2. Mesures liées aux personnes (Sensibilisation, RH)
3. Mesures d'ordre physique (Accès aux locaux, protection matériel)
4. Mesures technologiques (Sécurité réseau, serveurs, terminaux)
                    </textarea>
                </div>

                {{-- Outils d'audit --}}
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-blue-800 mb-2 italic">Outils d'audit utilisés :</h3>
                    <table class="w-full border-collapse border border-gray-300 text-[10px]">
                        <tr class="bg-blue-50">
                            <th class="border p-1">Outils</th><th class="border p-1">Version</th><th class="border p-1">License</th><th class="border p-1">Fonctionnalités</th><th class="border p-1">Objet</th>
                        </tr>
                        @foreach(range(0, 2) as $i)
                        <tr>
                            <td class="border p-0"><input type="text" name="outils[{{$i}}][o]" class="w-full p-1 border-none text-[10px]"></td>
                            <td class="border p-0"><input type="text" name="outils[{{$i}}][v]" class="w-full p-1 border-none text-[10px]"></td>
                            <td class="border p-0"><input type="text" name="outils[{{$i}}][l]" class="w-full p-1 border-none text-[10px]"></td>
                            <td class="border p-0"><input type="text" name="outils[{{$i}}][f]" class="w-full p-1 border-none text-[10px]"></td>
                            <td class="border p-0"><input type="text" name="outils[{{$i}}][obj]" class="w-full p-1 border-none text-[10px]"></td>
                        </tr>
                        @endforeach
                    </table>
                </div>

                {{-- Checklists --}}
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-blue-800 mb-2 italic">Checklists utilisées :</h3>
                    <table class="w-full border-collapse border border-gray-300 text-[10px]">
                        <tr class="bg-blue-50 text-[9px]">
                            <th class="border p-1 w-1/4">Titre du document</th><th class="border p-1">Version</th><th class="border p-1">Source</th><th class="border p-1">Description</th><th class="border p-1">Objet</th>
                        </tr>
                        @foreach(range(0, 1) as $i)
                        <tr>
                            <td class="border p-0"><input type="text" name="checklists[{{$i}}][t]" class="w-full p-1 border-none text-[10px]"></td>
                            <td class="border p-0"><input type="text" name="checklists[{{$i}}][v]" class="w-full p-1 border-none text-[10px]"></td>
                            <td class="border p-0"><input type="text" name="checklists[{{$i}}][s]" class="w-full p-1 border-none text-[10px]"></td>
                            <td class="border p-0"><input type="text" name="checklists[{{$i}}][d]" class="w-full p-1 border-none text-[10px]"></td>
                            <td class="border p-0"><input type="text" name="checklists[{{$i}}][obj]" class="w-full p-1 border-none text-[10px]"></td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </section>
        </div>

        {{-- PAGE 6 : ÉQUIPES DU PROJET & PLANNING --}}
        <div class="a4-page">
            <section id="equipes">
                <h3 class="text-md font-bold text-blue-800 mb-4 italic underline">L'équipe du projet côté {{ auth()->user()->name }} (Expert Auditeur)</h3>
                <table class="w-full border-collapse border border-gray-300 text-[10px] mb-8">
                    <tr class="bg-gray-100">
                        <th class="border p-2">Nom Prénom</th><th class="border p-2">Qualité</th><th class="border p-2">Qualification / Certif</th><th class="border p-2">Intervention</th>
                    </tr>
                    @foreach(range(0, 2) as $i)
                    <tr>
                        <td class="border p-0"><input type="text" name="team_exp[{{$i}}][n]" class="w-full p-2 border-none text-[10px]" value="{{ $i == 0 ? auth()->user()->name : '' }}"></td>
                        <td class="border p-0"><input type="text" name="team_exp[{{$i}}][q]" class="w-full p-2 border-none text-[10px]" placeholder="{{ $i == 0 ? 'Chef de projet' : 'Membre' }}"></td>
                        <td class="border p-0"><input type="text" name="team_exp[{{$i}}][c]" class="w-full p-2 border-none text-[10px]"></td>
                        <td class="border p-0"><input type="text" name="team_exp[{{$i}}][i]" class="w-full p-2 border-none text-[10px]"></td>
                    </tr>
                    @endforeach
                </table>

                <h3 class="text-md font-bold text-blue-800 mb-4 italic underline">L'équipe du projet côté {{ $audit->entreprise->nom }}</h3>
                <table class="w-full border-collapse border border-gray-300 text-[10px] mb-10">
                    <tr class="bg-gray-100">
                        <th class="border p-2">Nom Prénom</th><th class="border p-2">Qualité</th><th class="border p-2">Fonction</th>
                    </tr>
                    @foreach(range(0, 2) as $i)
                    <tr>
                        <td class="border p-0"><input type="text" name="team_cli[{{$i}}][n]" class="w-full p-2 border-none text-[10px]"></td>
                        <td class="border p-0"><input type="text" name="team_cli[{{$i}}][q]" class="w-full p-2 border-none text-[10px]"></td>
                        <td class="border p-0"><input type="text" name="team_cli[{{$i}}][f]" class="w-full p-2 border-none text-[10px]"></td>
                    </tr>
                    @endforeach
                </table>

                <h3 class="text-md font-bold text-blue-800 mb-2 italic underline">Planning réel d'exécution (Annexe 4)</h3>
                <p class="text-[10px] text-gray-500 mb-2 italic underline">Récapitulatif des phases de la mission pour {{ $audit->entreprise->nom }} :</p>
                <table class="w-full border-collapse border border-gray-300 text-[10px]">
                    <tr class="bg-blue-900 text-white uppercase">
                        <th class="border p-2 w-1/4">Phase / Activité</th>
                        <th class="border p-2">Date Début</th>
                        <th class="border p-2">Date Fin</th>
                        <th class="border p-2">Livrables / État</th>
                    </tr>
                    @php 
                        $phases = ['Lancement', 'Collecte de données', 'Audit technique', 'Analyse & Rédaction', 'Restitution']; 
                    @endphp
                    @foreach($phases as $index => $phase)
                    <tr>
                        <td class="border p-2 bg-gray-50 font-semibold">{{ $phase }}</td>
                        <td class="border p-0"><input type="date" name="plan[{{$index}}][start]" class="w-full p-2 border-none text-[10px]"></td>
                        <td class="border p-0"><input type="date" name="plan[{{$index}}][end]" class="w-full p-2 border-none text-[10px]"></td>
                        <td class="border p-0"><input type="text" name="plan[{{$index}}][out]" class="w-full p-2 border-none text-[10px]"></td>
                    </tr>
                    @endforeach
                </table>
            </section>
        </div>
        {{-- PAGE 7 : SYNTHÈSE DES RÉSULTATS --}}
        <div class="a4-page">
            <section id="chapitre-8">
                <h2 class="text-xl font-bold text-blue-900 mb-6 uppercase">8. Synthèse des résultats de l’audit</h2>

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="p-3 bg-blue-50 border-l-4 border-blue-900 text-[10px]">
                        <h4 class="font-bold mb-1">Critères et Référentiels</h4>
                        <p>L'audit a été réalisé conformément au <strong>Référentiel National de la Sécurité des SI (DNSSI)</strong> et aux standards ISO/IEC 27001/27002.</p>
                    </div>
                    <div class="p-3 bg-gray-50 border-l-4 border-gray-400 text-[10px]">
                        <h4 class="font-bold mb-1">Limites de l'audit</h4>
                        <p>Les résultats sont basés sur un échantillonnage représentatif à l'instant T. Les changements post-audit ne sont pas couverts.</p>
                    </div>
                </div>

                {{-- Évaluation du dernier plan d'action (Annexe 5) --}}
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-blue-800 mb-2 italic underline">8.1 Évaluation du dernier plan d'action (Annexe 5)</h3>
                    <table class="w-full border-collapse border border-gray-300 text-[9px]">
                        <tr class="bg-gray-100 text-center">
                            <th class="border p-1">Action Prévue (Audit Précédent)</th>
                            <th class="border p-1">Statut (Réalisé/En cours/Non)</th>
                            <th class="border p-1">Efficacité constatée</th>
                            <th class="border p-1">Commentaires</th>
                        </tr>
                        @foreach(range(0, 2) as $i)
                        <tr>
                            <td class="border p-1"><input type="text" name="prev_actions[{{$i}}][a]" class="w-full border-none"></td>
                            <td class="border p-1 text-center">
                                <select name="prev_actions[{{$i}}][s]" class="bg-transparent border-none">
                                    <option value="R">Réalisé</option><option value="EC">En cours</option><option value="N">Non réalisé</option>
                                </select>
                            </td>
                            <td class="border p-1"><input type="text" name="prev_actions[{{$i}}][e]" class="w-full border-none"></td>
                            <td class="border p-1"><input type="text" name="prev_actions[{{$i}}][c]" class="w-full border-none"></td>
                        </tr>
                        @endforeach
                    </table>
                </div>

                {{-- État de maturité (Annexe 6) --}}
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-blue-800 mb-2 italic underline">8.2 État de maturité de la sécurité (Annexe 6)</h3>
                    <div class="flex gap-4 items-start">
                        <div class="w-1/2 h-40 border flex items-center justify-center bg-white relative">
                            {{-- Placeholder pour un Radar Chart ou Graphique de maturité --}}
                            <span class="text-[9px] text-gray-400 uppercase text-center">[ Graphique Radar de Maturité :<br>Organisationnel, Personnes, Physique, Technique ]</span>
                        </div>
                        <div class="w-1/2">
                            <table class="w-full border text-[9px]">
                                <tr class="bg-blue-900 text-white"><th class="p-1">Domaine</th><th class="p-1">Score / 5</th></tr>
                                <tr><td class="border p-1">Mesures Organisationnelles</td><td class="border p-1 text-center">/ 5</td></tr>
                                <tr><td class="border p-1">Mesures liées aux Personnes</td><td class="border p-1 text-center">/ 5</td></tr>
                                <tr><td class="border p-1">Mesures Physiques</td><td class="border p-1 text-center">/ 5</td></tr>
                                <tr><td class="border p-1">Mesures Technologiques</td><td class="border p-1 text-center">/ 5</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Alerte Vulnérabilités Critiques (Annexe 8) --}}
                <div class="p-3 border-2 border-red-500 bg-red-50 rounded text-[10px]">
                    <h4 class="text-red-700 font-bold uppercase flex items-center">
                        <span class="mr-2">⚠️</span> Signalement de vulnérabilités très critiques (Annexe 8)
                    </h4>
                    <p class="mt-1 italic font-semibold">Note : Les failles pouvant affecter le cyberespace national sont signalées immédiatement à la DGSSI et à {{ $audit->entreprise->nom }}.</p>
                </div>
            </section>
        </div>

        {{-- PAGE 8 : PRÉSENTATION DÉTAILLÉE (Exemple Domaine 5) --}}
        <div class="a4-page">
            <section id="chapitre-9">
                <h2 class="text-xl font-bold text-blue-900 mb-6 uppercase">9. Présentation détaillée des résultats</h2>

                @php
                    $domaines = [
                        '5' => 'Mesures de sécurité organisationnelles',
                        '6' => 'Mesures de sécurité applicables aux personnes',
                        '7' => 'Mesures de sécurité physique',
                        '8' => 'Mesures de sécurité technologiques'
                    ];
                @endphp

                @foreach($domaines as $num => $titre)
                <div class="mb-10">
                    <div class="bg-blue-900 text-white p-2 font-bold text-sm uppercase mb-4">
                        {{ $num }}. {{ $titre }}
                    </div>

                    {{-- Tableau des constats --}}
                    <table class="w-full border-collapse border border-gray-400 text-[9px] mb-4">
                        <thead>
                            <tr class="bg-gray-200 uppercase">
                                <th class="border border-gray-400 p-2 w-1/4">Critères d'audit</th>
                                <th class="border border-gray-400 p-2">Résultats / Constats (Vérifications effectuées)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border p-2 font-semibold">Critère {{ $num }}.1 : Politiques / Procédures</td>
                                <td class="border p-2">
                                    <div class="mb-2"><strong>Bonnes pratiques :</strong><br>
                                        <textarea name="bp_{{$num}}_1" rows="2" class="w-full text-[9px] mt-1" placeholder="Lister ici les points forts..."></textarea>
                                    </div>
                                    <div><strong>Vulnérabilités :</strong><br>
                                        <textarea name="vuln_{{$num}}_1" rows="2" class="w-full text-[9px] mt-1" placeholder="Lister ici les écarts..."></textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Focus sur une Vulnérabilité (Le modèle fiche détaillée) --}}
                    <div class="border border-red-200 bg-red-50 p-3 rounded mb-6">
                        <h4 class="text-[10px] font-bold text-red-800 border-b border-red-200 mb-2 pb-1 uppercase italic">Fiche de Vulnérabilité - Focus Domaine {{ $num }}</h4>
                        <div class="grid grid-cols-3 gap-2 text-[9px]">
                            <div class="col-span-2">
                                <label class="font-bold">Référence :</label> <input type="text" name="v_ref_{{$num}}" class="bg-white border p-1 ml-2" placeholder="Ex: VULN-{{$num}}-001">
                            </div>
                            <div>
                                <label class="font-bold">Criticité :</label> 
                                <select class="text-[9px] p-1 border">
                                    <option>Critique</option><option>Élevée</option><option>Moyenne</option><option>Faible</option>
                                </select>
                            </div>
                            <div class="col-span-3">
                                <label class="font-bold">Description :</label>
                                <textarea class="w-full bg-white mt-1 p-1" rows="2"></textarea>
                            </div>
                            <div class="col-span-3">
                                <label class="font-bold">Composante(s) impactée(s) :</label>
                                <input type="text" class="w-full bg-white mt-1 p-1" value="{{ $audit->entreprise->actifs_critiques }}">
                            </div>
                            <div class="col-span-3">
                                <label class="font-bold text-green-700">Recommandation :</label>
                                <textarea class="w-full bg-white mt-1 p-1 border-green-200" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </section>
        </div>
        {{-- PAGE 9 : APPRÉCIATION DES RISQUES --}}
        <div class="a4-page">
            <section id="chapitre-10">
                <h2 class="text-xl font-bold text-blue-900 mb-6 uppercase">10. Appréciation des risques</h2>

                <div class="mb-6">
                    <h3 class="text-md font-bold text-blue-800 mb-2 italic underline">10.1 Démarche et Choix des processus</h3>
                    <textarea name="demarche_risques" rows="4" class="text-[10px] bg-gray-50 mb-4">
La démarche adoptée s'appuie sur la méthode EBIOS RM / ISO 27005. Elle consiste à identifier les événements redoutés sur les processus métier critiques, à construire des scénarios de menace et à évaluer leur gravité résiduelle.
                    </textarea>
                    
                    <label class="block text-[10px] font-bold text-blue-900 mb-1">Justification des processus métier retenus :</label>
                    <div class="p-2 border bg-blue-50 text-[10px] mb-4 italic">
                        Processus cibles identifiés à la section 5.3 : <strong>{{ $audit->entreprise->secteur }} - {{ $audit->entreprise->actifs_critiques }}</strong>
                    </div>
                </div>

                <h3 class="text-md font-bold text-blue-800 mb-3 italic underline">10.2 Scénarios de risques prépondérants</h3>
                
                {{-- Modèle de Fiche de Risque --}}
                @foreach(range(0, 1) as $i)
                <div class="border-2 border-gray-800 p-4 mb-6 bg-white">
                    <div class="grid grid-cols-4 gap-2 text-[10px]">
                        <div class="col-span-3 bg-gray-800 text-white p-2 font-bold uppercase">
                            Scénario du risque n°{{ $i+1 }} : <input type="text" name="risques[{{$i}}][nom]" class="bg-transparent border-none text-white w-2/3" placeholder="Ex: Ransomware via Phishing">
                        </div>
                        <div class="bg-red-100 p-2 border border-red-300 text-center">
                            <span class="font-bold">Gravité :</span><br>
                            <select name="risques[{{$i}}][gravite]" class="bg-transparent font-bold text-red-700">
                                <option>Critique</option><option>Haute</option><option>Moyenne</option>
                            </select>
                        </div>

                        <div class="col-span-4 border-b py-2">
                            <strong>Description :</strong> 
                            <textarea name="risques[{{$i}}][desc]" rows="2" class="w-full mt-1 border-none bg-gray-50"></textarea>
                        </div>

                        <div class="col-span-2 border-r p-1">
                            <strong>Réf. Vulnérabilités :</strong>
                            <input type="text" name="risques[{{$i}}][ref]" class="w-full border-none p-0" placeholder="VULN-001, VULN-005...">
                        </div>
                        <div class="col-span-2 p-1">
                            <strong>Composantes impactées :</strong>
                            <input type="text" name="risques[{{$i}}][comp]" class="w-full border-none p-0" value="{{ $audit->entreprise->actifs_critiques }}">
                        </div>

                        <div class="col-span-4 bg-gray-100 p-2">
                            <strong>Impacts / Conséquences :</strong>
                            <input type="text" name="risques[{{$i}}][impact]" class="w-full bg-transparent border-none">
                        </div>

                        <div class="col-span-2 border-r p-2">
                            <strong>Complexité d'exploitation :</strong>
                            <select name="risques[{{$i}}][complex_exp]" class="w-full bg-transparent">
                                <option>Faible (Facile)</option><option>Moyenne</option><option>Élevée (Difficile)</option>
                            </select>
                        </div>
                        <div class="col-span-2 p-2">
                            <strong>Complexité mise en œuvre reco :</strong>
                            <select name="risques[{{$i}}][complex_reco]" class="w-full bg-transparent">
                                <option>Faible</option><option>Moyenne</option><option>Élevée</option>
                            </select>
                        </div>

                        <div class="col-span-4 p-2 border-t-2 border-gray-800">
                            <strong class="text-blue-900 uppercase">Recommandation stratégique :</strong>
                            <textarea name="risques[{{$i}}][reco]" rows="2" class="w-full mt-1 border-none bg-blue-50 italic"></textarea>
                        </div>
                    </div>
                </div>
                @endforeach
            </section>
        </div>

        {{-- PAGE 10 : PLAN D'ACTION --}}
        <div class="a4-page">
            <section id="chapitre-11">
                <h2 class="text-xl font-bold text-blue-900 mb-6 uppercase">11. Plan d’action proposé</h2>

                <div class="p-4 bg-blue-50 border border-blue-200 text-[11px] mb-6 leading-relaxed italic">
                    <p>Le présent plan d’action est établi en <strong>commun accord</strong> avec {{ $audit->entreprise->nom }}. Il prend en considération les objectifs stratégiques, les projets en cours et les capacités humaines et financières de l'organisme. Les estimations sont basées sur un dimensionnement adéquat du marché actuel.</p>
                </div>

                <h3 class="text-sm font-bold text-blue-800 mb-4 italic underline">Synthèse du plan d'action (Annexe 9)</h3>
                
                <table class="w-full border-collapse border border-gray-400 text-[9px]">
                    <thead>
                        <tr class="bg-blue-900 text-white uppercase">
                            <th class="border p-2">Projet / Action</th>
                            <th class="border p-2">Priorité</th>
                            <th class="border p-2">Responsable</th>
                            <th class="border p-2">Délai estimé</th>
                            <th class="border p-2">Ressources (DH/H.j)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $projets = ['Gouvernance & PSSI', 'Sécurisation Infrastructure', 'Sensibilisation RH', 'Gestion des accès']; 
                        @endphp
                        @foreach($projets as $pj)
                        <tr class="bg-gray-100 font-bold">
                            <td colspan="5" class="border p-2 text-blue-900">PROJET : {{ $pj }}</td>
                        </tr>
                        @foreach(range(0, 1) as $k)
                        <tr>
                            <td class="border p-1 pl-4"><input type="text" name="plan_act[{{$pj}}][{{$k}}][a]" class="w-full border-none bg-transparent" placeholder="Action spécifique..."></td>
                            <td class="border p-1 text-center">
                                <select class="text-[9px] border-none bg-transparent">
                                    <option>P1 (Urgent)</option><option>P2</option><option>P3</option>
                                </select>
                            </td>
                            <td class="border p-1"><input type="text" class="w-full border-none bg-transparent"></td>
                            <td class="border p-1"><input type="text" class="w-full border-none bg-transparent" placeholder="ex: 3 mois"></td>
                            <td class="border p-1"><input type="text" class="w-full border-none bg-transparent"></td>
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-10 text-center border-t pt-10">
                    <div class="grid grid-cols-2 gap-20">
                        <div>
                            <p class="text-xs font-bold underline mb-10">Pour l'Organisme Audité (Approbation)</p>
                            <div class="h-20 border border-dashed border-gray-400"></div>
                        </div>
                        <div>
                            <p class="text-xs font-bold underline mb-10">Pour l'Expert Auditeur</p>
                            <p class="text-xs">{{ auth()->user()->name }}</p>
                            <div class="h-20 border border-dashed border-gray-400"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
       

        {{-- BARRE D'ACTIONS FIXE --}}
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t p-4 no-print shadow-lg">
            <div class="container mx-auto flex justify-between items-center">
                <div class="text-sm text-gray-600 italic">
                    Modifications non enregistrées...
                </div>
                <div class="flex space-x-4">
                    <button type="button" onclick="window.print()" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition">
                        🖨️ Imprimer / PDF
                    </button>
                    
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let rowIndex = {{ count($results ?? []) }};    function addRow() {
        const container = document.getElementById('dynamic-results');
        const html = `
            <div class="grid grid-cols-12 gap-2 mb-2 border-b pb-2">
                <div class="col-span-3">
                    <input type="text" name="equipe_expert[${rowIndex}][domaine]" placeholder="Domaine" class="text-xs w-full border p-1">
                </div>
                <div class="col-span-3">
                    <input type="text" name="equipe_expert[${rowIndex}][critere]" placeholder="Critère" class="text-xs w-full border p-1">
                </div>
                <div class="col-span-6">
                    <textarea name="equipe_expert[${rowIndex}][description]" placeholder="Constat/Description" class="text-xs h-8"></textarea>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
        rowIndex++;
    }
</script>
@endsection