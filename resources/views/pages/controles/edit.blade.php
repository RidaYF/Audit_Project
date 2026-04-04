@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('controls.iso') }}" class="text-sm text-orange-600 hover:text-orange-700 mb-2 inline-block">
            ← Retour aux contrôles
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Modifier le contrôle</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $controle->code }} - {{ $controle->titre }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('controles.update', $controle->id) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                    <input type="text" name="code" required value="{{ old('code', $controle->code) }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                    <input type="text" name="categorie" value="{{ old('categorie', $controle->categorie) }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                    <input type="text" name="titre" required value="{{ old('titre', $controle->titre) }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Niveau critique</label>
                    <select name="niveau_critique" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                        <option value="faible" {{ $controle->niveau_critique == 'faible' ? 'selected' : '' }}>Faible</option>
                        <option value="moyen" {{ $controle->niveau_critique == 'moyen' ? 'selected' : '' }}>Moyen</option>
                        <option value="eleve" {{ $controle->niveau_critique == 'eleve' ? 'selected' : '' }}>Élevé</option>
                        <option value="critique" {{ $controle->niveau_critique == 'critique' ? 'selected' : '' }}>Critique</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de contrôle</label>
                    <select name="type_controle" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                        <option value="preventif" {{ $controle->type_controle == 'preventif' ? 'selected' : '' }}>Préventif</option>
                        <option value="detectif" {{ $controle->type_controle == 'detectif' ? 'selected' : '' }}>Détectif</option>
                        <option value="correctif" {{ $controle->type_controle == 'correctif' ? 'selected' : '' }}>Correctif</option>
                        <option value="analyse" {{ $controle->type_controle == 'analyse' ? 'selected' : '' }}>Analyse</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pondération par défaut</label>
                    <input type="number" name="ponderation_defaut" min="0" max="10" required value="{{ old('ponderation_defaut', $controle->ponderation_defaut) }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Temps estimé (minutes)</label>
                    <input type="number" name="temps_estime" min="0" value="{{ old('temps_estime', $controle->temps_estime) }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" required
                              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">{{ old('description', $controle->description) }}</textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Objectif</label>
                    <textarea name="objectif" rows="3" required
                              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">{{ old('objectif', $controle->objectif) }}</textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conseils d'audit</label>
                    <textarea name="conseils_audit" rows="3"
                              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">{{ old('conseils_audit', $controle->conseils_audit) }}</textarea>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end gap-3">
            <a href="{{ route('controles.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
