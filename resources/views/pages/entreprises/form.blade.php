@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
        </div>
        <a href="{{ route('entreprises.index') }}" class="btn btn-secondary inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour à la liste
        </a>
    </div>
    
    <!-- Form -->
    <div class="card">
        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if($method ?? false)
                @method($method)
            @endif
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Colonne gauche -->
                <div class="space-y-6">
                    <!-- Informations générales -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nom de l'entreprise <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nom" id="nom" required
                                       value="{{ old('nom', $entreprise->nom ?? '') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('nom') border-red-500 @enderror">
                                @error('nom')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="secteur" class="block text-sm font-medium text-gray-700 mb-1">
                                    Secteur d'activité <span class="text-red-500">*</span>
                                </label>
                                <select name="secteur" id="secteur" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('secteur') border-red-500 @enderror">
                                    <option value="">Sélectionnez un secteur</option>
                                    <option value="Technologie" {{ old('secteur', $entreprise->secteur ?? '') == 'Technologie' ? 'selected' : '' }}>Technologie</option>
                                    <option value="Finance" {{ old('secteur', $entreprise->secteur ?? '') == 'Finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="Santé" {{ old('secteur', $entreprise->secteur ?? '') == 'Santé' ? 'selected' : '' }}>Santé</option>
                                    <option value="Industrie" {{ old('secteur', $entreprise->secteur ?? '') == 'Industrie' ? 'selected' : '' }}>Industrie</option>
                                    <option value="Commerce" {{ old('secteur', $entreprise->secteur ?? '') == 'Commerce' ? 'selected' : '' }}>Commerce</option>
                                    <option value="Éducation" {{ old('secteur', $entreprise->secteur ?? '') == 'Éducation' ? 'selected' : '' }}>Éducation</option>
                                    <option value="Transport" {{ old('secteur', $entreprise->secteur ?? '') == 'Transport' ? 'selected' : '' }}>Transport</option>
                                    <option value="Énergie" {{ old('secteur', $entreprise->secteur ?? '') == 'Énergie' ? 'selected' : '' }}>Énergie</option>
                                </select>
                                @error('secteur')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="taille" class="block text-sm font-medium text-gray-700 mb-1">
                                    Taille de l'entreprise <span class="text-red-500">*</span>
                                </label>
                                <select name="taille" id="taille" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('taille') border-red-500 @enderror">
                                    <option value="">Sélectionnez une taille</option>
                                    <option value="TPE" {{ old('taille', $entreprise->taille ?? '') == 'TPE' ? 'selected' : '' }}>TPE (Très Petite Entreprise)</option>
                                    <option value="PME" {{ old('taille', $entreprise->taille ?? '') == 'PME' ? 'selected' : '' }}>PME (Petite ou Moyenne Entreprise)</option>
                                    <option value="ETI" {{ old('taille', $entreprise->taille ?? '') == 'ETI' ? 'selected' : '' }}>ETI (Entreprise de Taille Intermédiaire)</option>
                                    <option value="GE" {{ old('taille', $entreprise->taille ?? '') == 'GE' ? 'selected' : '' }}>GE (Grande Entreprise)</option>
                                </select>
                                @error('taille')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="localisation" class="block text-sm font-medium text-gray-700 mb-1">
                                    Localisation
                                </label>
                                <input type="text" name="localisation" id="localisation"
                                       value="{{ old('localisation', $entreprise->localisation ?? '') }}"
                                       placeholder="Ville, Pays"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('localisation') border-red-500 @enderror">
                                @error('localisation')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chiffres clés -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Chiffres clés</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="chiffre_affaires" class="block text-sm font-medium text-gray-700 mb-1">
                                    Chiffre d'affaires
                                </label>
                                <input type="text" name="chiffre_affaires" id="chiffre_affaires"
                                       value="{{ old('chiffre_affaires', $entreprise->chiffre_affaires ?? '') }}"
                                       placeholder="Ex: 10M €"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('chiffre_affaires') border-red-500 @enderror">
                                @error('chiffre_affaires')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="effectif" class="block text-sm font-medium text-gray-700 mb-1">
                                    Effectif
                                </label>
                                <input type="number" name="effectif" id="effectif"
                                       value="{{ old('effectif', $entreprise->effectif ?? '') }}"
                                       placeholder="Nombre d'employés"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('effectif') border-red-500 @enderror">
                                @error('effectif')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Colonne droite -->
                <div class="space-y-6">
                    <!-- Logo -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Logo</h3>
                        
                        <div class="space-y-4">
                            @if(isset($entreprise) && $entreprise->image_logo)
                                <div class="flex items-center gap-4">
                                    <img src="{{ asset('storage/' . $entreprise->image_logo) }}" 
                                         alt="Logo actuel" 
                                         class="w-20 h-20 rounded-full object-cover">
                                    <div class="text-sm text-gray-500">
                                        <p>Logo actuel</p>
                                        <label class="inline-flex items-center mt-1">
                                            <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                            <span class="ml-2 text-sm text-red-600">Supprimer ce logo</span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                            
                            <div>
                                <label for="image_logo" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ isset($entreprise) && $entreprise->image_logo ? 'Nouveau logo' : 'Logo' }}
                                </label>
                                <input type="file" name="image_logo" id="image_logo" accept="image/*"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('image_logo') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500">Formats acceptés : JPG, PNG, GIF. Max 2MB</p>
                                @error('image_logo')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statut -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Statut</h3>
                        
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" 
                                       {{ old('is_active', isset($entreprise) ? $entreprise->is_active : true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">Entreprise active</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500">Les entreprises inactives n'apparaîtront pas dans les sélections pour les nouveaux audits</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Description détaillée - Pleine largeur -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Description détaillée</h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="contexte_metier" class="block text-sm font-medium text-gray-700 mb-1">
                            Contexte métier
                        </label>
                        <textarea name="contexte_metier" id="contexte_metier" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('contexte_metier') border-red-500 @enderror"
                                  placeholder="Décrivez le contexte métier de l'entreprise, ses activités principales, ses enjeux...">{{ old('contexte_metier', $entreprise->contexte_metier ?? '') }}</textarea>
                        @error('contexte_metier')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="systemes_cles" class="block text-sm font-medium text-gray-700 mb-1">
                            Systèmes clés
                        </label>
                        <textarea name="systemes_cles" id="systemes_cles" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('systemes_cles') border-red-500 @enderror"
                                  placeholder="Listez les systèmes d'information clés (ERP, CRM, SIRH, etc.)">{{ old('systemes_cles', $entreprise->systemes_cles ?? '') }}</textarea>
                        @error('systemes_cles')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="departements" class="block text-sm font-medium text-gray-700 mb-1">
                            Départements
                        </label>
                        <textarea name="departements" id="departements" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('departements') border-red-500 @enderror"
                                  placeholder="Listez les départements de l'entreprise (RH, IT, Finance, Marketing, etc.)">{{ old('departements', $entreprise->departements ?? '') }}</textarea>
                        @error('departements')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="actifs_critiques" class="block text-sm font-medium text-gray-700 mb-1">
                            Actifs critiques
                        </label>
                        <textarea name="actifs_critiques" id="actifs_critiques" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('actifs_critiques') border-red-500 @enderror"
                                  placeholder="Décrivez les actifs critiques de l'entreprise (données sensibles, infrastructure, propriété intellectuelle, etc.)">{{ old('actifs_critiques', $entreprise->actifs_critiques ?? '') }}</textarea>
                        @error('actifs_critiques')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('entreprises.index') }}" class="btn btn-secondary">
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($entreprise) ? 'Mettre à jour' : 'Créer l\'entreprise' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection