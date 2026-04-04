@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('controls.iso') }}" class="text-sm text-orange-600 hover:text-orange-700 mb-2 inline-block">
            ← Retour aux contrôles
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Gestion des preuves</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $controle->code }} - {{ $controle->titre }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Formulaire d'ajout de preuve avec fichier -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Ajouter une preuve</h2>
        
        <form method="POST" action="{{ route('controles.preuves.add', $controle->id) }}" class="space-y-4" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre de la preuve</label>
                    <input type="text" name="titre" required 
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                           placeholder="Ex: Politique de sécurité">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de preuve</label>
                    <select name="type_preuve" required class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                        <option value="">Sélectionner</option>
                        <option value="document">Document</option>
                        <option value="configuration">Configuration</option>
                        <option value="log">Log / Journal</option>
                        <option value="entretien">Entretien</option>
                        <option value="capture_ecran">Capture d'écran</option>
                        <option value="analyse">Analyse</option>
                        <option value="image">Image</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Points maximum</label>
                    <input type="number" name="points_max" value="1" min="0" max="10" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fichier joint (optionnel)</label>
                    <input type="file" name="fichier" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" 
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500">
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPG, PNG, GIF, SVG, PDF, DOC, DOCX, XLS, XLSX. Max: 5MB</p>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contenu / Description</label>
                    <textarea name="contenu" rows="3" required
                              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-purple-500 focus:ring-purple-500"
                              placeholder="Décrivez la preuve à collecter..."></textarea>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    + Ajouter la preuve
                </button>
            </div>
        </form>
    </div>

    <!-- Liste des preuves existantes -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Preuves existantes</h2>
        </div>
        
        @if($controle->preuves->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($controle->preuves as $preuve)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <h3 class="font-semibold text-gray-900">{{ $preuve->titre }}</h3>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    {{ $preuve->type_preuve }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    Max: {{ $preuve->points_max }} pts
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $preuve->contenu }}</p>
                            
                            @if($preuve->file_path)
                            <div class="mt-3">
                                @php
                                    $fileExtension = pathinfo($preuve->file_path, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                                @endphp
                                
                                @if($isImage)
                                    <a href="{{ Storage::url($preuve->file_path) }}" target="_blank" 
                                       class="inline-flex items-center gap-2 text-sm text-purple-600 hover:text-purple-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Voir l'image
                                    </a>
                                @else
                                    <a href="{{ Storage::url($preuve->file_path) }}" download 
                                       class="inline-flex items-center gap-2 text-sm text-purple-600 hover:text-purple-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17a2 2 0 002 2h14a2 2 0 002-2M5 7h14M5 7l4-4h6l4 4M5 7v10"></path>
                                        </svg>
                                        Télécharger le document ({{ strtoupper($fileExtension) }})
                                    </a>
                                @endif
                            </div>
                            @endif
                            
                            <p class="text-xs text-gray-400 mt-2">Ordre: {{ $preuve->ordre_affichage }}</p>
                        </div>
                        <div class="ml-4 flex gap-2">
                            @if($preuve->file_path)
                            <form method="POST" action="{{ route('controles.preuves.delete-file', $preuve->id) }}" 
                                  onsubmit="return confirm('Supprimer le fichier de cette preuve ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-orange-600 hover:text-orange-800 text-sm">
                                    Supprimer fichier
                                </button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('controles.preuves.delete', $preuve->id) }}" 
                                  onsubmit="return confirm('Supprimer cette preuve ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center text-gray-500">
                Aucune preuve associée à ce contrôle.
            </div>
        @endif
    </div>
</div>
@endsection