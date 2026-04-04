@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800 uppercase tracking-wide">
            📜 Historique des Missions d'Audit
        </h1>
        <a href="{{ route('audits.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition">
            + Nouvel Audit
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-100 text-gray-600 uppercase text-[10px] leading-normal">
                    <th class="py-3 px-6 text-left">Entreprise</th>
                    <th class="py-3 px-6 text-left">État du Rapport</th>
                    <th class="py-3 px-6 text-center">Date de l'Audit</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @forelse($audits as $audit)
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="py-3 px-6 text-left font-bold text-blue-900">
                        {{ $audit->entreprise->nom ?? 'N/A' }}
                    </td>
                    <td class="py-3 px-6 text-left">
                        @if($audit->rapport)
                            <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs font-semibold">
                                ✅ Rapport v{{ $audit->rapport->version }}
                            </span>
                        @else
                            <span class="bg-yellow-100 text-yellow-800 py-1 px-3 rounded-full text-xs font-semibold">
                                ⏳ Rapport non rédigé
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-6 text-center">
                        {{ $audit->created_at->format('d/m/Y') }}
                    </td>
                    <td class="py-3 px-6 text-center">
                        <div class="flex item-center justify-center space-x-4">
                            {{-- Bouton pour aller rédiger/voir le rapport --}}
                            <a href="{{ route('report.index', $audit->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 transition">
                                {{ $audit->rapport ? '📝 Modifier le rapport' : '➕ Rédiger le rapport' }}
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-10 text-center text-gray-400 italic">
                        Aucune mission d'audit enregistrée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $audits->links() }}
    </div>
</div>
@endsection