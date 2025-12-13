@extends('layouts.app')

@section('title', 'Colis')
@section('page-title', 'Gestion des Colis')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Liste des Colis</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gérez vos expéditions</p>
        </div>
        <a href="{{ route('colis.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nouveau Colis
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-slate-700">
        <form method="GET" action="{{ route('colis.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
            <select name="statut" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                <option value="">Tous les statuts</option>
                <option value="emballe" {{ request('statut') === 'emballe' ? 'selected' : '' }}>Colis emballé</option>
                <option value="expedie_port" {{ request('statut') === 'expedie_port' ? 'selected' : '' }}>Expédié vers port/aéroport</option>
                <option value="arrive_aeroport_depart" {{ request('statut') === 'arrive_aeroport_depart' ? 'selected' : '' }}>Arrivé aéroport départ</option>
                <option value="en_vol" {{ request('statut') === 'en_vol' ? 'selected' : '' }}>En vol</option>
                <option value="arrive_aeroport_transit" {{ request('statut') === 'arrive_aeroport_transit' ? 'selected' : '' }}>Arrivé aéroport transit</option>
                <option value="arrive_aeroport_destination" {{ request('statut') === 'arrive_aeroport_destination' ? 'selected' : '' }}>Arrivé aéroport destination</option>
                <option value="en_dedouanement" {{ request('statut') === 'en_dedouanement' ? 'selected' : '' }}>En dédouanement</option>
                <option value="arrive_entrepot" {{ request('statut') === 'arrive_entrepot' ? 'selected' : '' }}>Arrivé entrepôt</option>
                <option value="livre" {{ request('statut') === 'livre' ? 'selected' : '' }}>Livré</option>
                <option value="retourne" {{ request('statut') === 'retourne' ? 'selected' : '' }}>Retourné</option>
            </select>
            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">Filtrer</button>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Numéro</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Paiement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date envoi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($colis as $coli)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $coli->numero_suivi }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $coli->client->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($coli->statut === 'livre') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                    @elseif(in_array($coli->statut, ['expedie_port', 'arrive_aeroport_depart', 'en_vol', 'arrive_aeroport_transit', 'arrive_aeroport_destination', 'en_dedouanement', 'arrive_entrepot'])) bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                    @elseif($coli->statut === 'emballe') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                    @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                    @endif">
                                    @php
                                        $statutLabels = [
                                            'emballe' => 'Colis emballé',
                                            'expedie_port' => 'Expédié',
                                            'arrive_aeroport_depart' => 'Aéroport départ',
                                            'en_vol' => 'En vol',
                                            'arrive_aeroport_transit' => 'Aéroport transit',
                                            'arrive_aeroport_destination' => 'Aéroport destination',
                                            'en_dedouanement' => 'En dédouanement',
                                            'arrive_entrepot' => 'Entrepôt',
                                            'livre' => 'Livré',
                                            'retourne' => 'Retourné'
                                        ];
                                    @endphp
                                    {{ $statutLabels[$coli->statut] ?? ucfirst(str_replace('_', ' ', $coli->statut)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($coli->statut_paiement === 'paye')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Payé</span>
                                @elseif($coli->statut_paiement === 'partiel')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                                        Partiel ({{ number_format($coli->total_paye, 0, ',', ' ') }}/{{ number_format($coli->frais_transport, 0, ',', ' ') }})
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Non payé</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $coli->date_envoi->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('colis.show', $coli) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Voir</a>
                                    @can('admin')
                                    <a href="{{ route('colis.edit', $coli) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">Modifier</a>
                                    <form action="{{ route('colis.destroy', $coli) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce colis ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400">Supprimer</button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun colis trouvé</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">{{ $colis->links() }}</div>
    </div>
</div>
@endsection

