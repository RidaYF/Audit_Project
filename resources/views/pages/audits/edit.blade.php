@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('audits.show', $audit->id) }}" class="text-sm text-purple-600 hover:text-purple-700 mb-2 inline-block">
                ← Retour à l'audit
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Modifier l'audit</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $audit->titre }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('audits.update', $audit->id) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Informations générales -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                    <input type="text" name="titre" required value="{{ old('titre', $audit->titre) }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                    @error('titre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Entreprise</label>
                    <select name="entreprise_id" required class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                        <option value="">Sélectionner une entreprise</option>
                        @foreach($entreprises as $entreprise)
                            <option value="{{ $entreprise->id }}" {{ old('entreprise_id', $audit->entreprise_id) == $entreprise->id ? 'selected' : '' }}>
                                {{ $entreprise->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('entreprise_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">{{ old('description', $audit->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                        <input type="datetime-local" name="date_debut" required 
                               value="{{ old('date_debut', $audit->date_debut ? $audit->date_debut->format('Y-m-d\TH:i') : '') }}"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                        @error('date_debut')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                        <input type="datetime-local" name="date_fin" 
                               value="{{ old('date_fin', $audit->date_fin ? $audit->date_fin->format('Y-m-d\TH:i') : '') }}"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                        @error('date_fin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                        <option value="en_cours" {{ old('status', $audit->status) == 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="suspendu" {{ old('status', $audit->status) == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                        <option value="termine" {{ old('status', $audit->status) == 'termine' ? 'selected' : '' }}>Terminé</option>
                        <option value="annule" {{ old('status', $audit->status) == 'annule' ? 'selected' : '' }}>Annulé</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Référentiels ISO -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Référentiels ISO</h2>
            <p class="text-sm text-gray-500 mb-4">Sélectionnez les normes qui seront vérifiées lors de l'audit</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($references as $reference)
                    @php
                        $isSelected = $audit->references->contains($reference->id);
                    @endphp
                    <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors
                        {{ $isSelected ? 'border-purple-500 bg-purple-50' : '' }}">
                        <input type="checkbox" name="reference_ids[]" value="{{ $reference->id }}"
                               {{ $isSelected ? 'checked' : '' }}
                               class="mt-1 mr-3 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
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
            <p class="text-xs text-gray-500 mt-3">⚠️ Attention: Modifier les référentiels peut affecter les évaluations existantes.</p>
            @error('reference_ids')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Actions -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('audits.show', $audit->id) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
