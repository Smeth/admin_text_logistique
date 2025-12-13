@extends('layouts.app')

@section('title', 'Tarifs')
@section('page-title', 'Gestion des Tarifs')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Liste des Tarifs</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gérez vos tarifs de transport</p>
        </div>
        <a href="{{ route('tarifs.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nouveau Tarif
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-slate-700">
        <form method="GET" action="{{ route('tarifs.index') }}" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="flex-1 px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
            <select name="actif" class="px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                <option value="">Tous</option>
                <option value="1" {{ request('actif') === '1' ? 'selected' : '' }}>Actifs</option>
                <option value="0" {{ request('actif') === '0' ? 'selected' : '' }}>Inactifs</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Filtrer</button>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Prix/Kg</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Min</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Max</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Spécificité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Statut</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($tarifs as $tarif)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $tarif->nom_tarif }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ number_format($tarif->prix_par_kilo, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ number_format($tarif->prix_minimum, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $tarif->prix_maximum ? number_format($tarif->prix_maximum, 0, ',', ' ') . ' FCFA' : '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                @if($tarif->agenceDepart || $tarif->agenceArrivee || $tarif->transporteur)
                                    <div class="text-xs">
                                        @if($tarif->agenceDepart) Départ: {{ $tarif->agenceDepart->nom_agence }}<br> @endif
                                        @if($tarif->agenceArrivee) Arrivée: {{ $tarif->agenceArrivee->nom_agence }}<br> @endif
                                        @if($tarif->transporteur) Transporteur: {{ $tarif->transporteur->nom_entreprise }} @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Général</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $tarif->actif ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ $tarif->actif ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('tarifs.show', $tarif) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Voir</a>
                                    <a href="{{ route('tarifs.edit', $tarif) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">Modifier</a>
                                    <form action="{{ route('tarifs.destroy', $tarif) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun tarif</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">{{ $tarifs->links() }}</div>
    </div>
</div>
@endsection

