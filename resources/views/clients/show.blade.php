@extends('layouts.app')

@section('title', 'Détails Client')
@section('page-title', 'Détails du Client')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $client->full_name }}</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $client->email }}</p>
            </div>
            <div class="flex space-x-2">
                @can('admin')
                <a href="{{ route('clients.edit', $client) }}" 
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Modifier
                </a>
                @endcan
                <a href="{{ route('clients.index') }}" 
                   class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Téléphone</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $client->telephone }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</p>
                <span class="inline-block mt-1 px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                    {{ ucfirst($client->type) }}
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Statut</p>
                <span class="inline-block mt-1 px-3 py-1 text-sm font-medium rounded-full {{ $client->statut === 'actif' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
                    {{ ucfirst($client->statut) }}
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date de création</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $client->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="mt-6">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Adresse</p>
            <p class="text-gray-900 dark:text-white">{{ $client->adresse }}</p>
        </div>

        @if($client->notes)
        <div class="mt-6">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</p>
            <p class="text-gray-900 dark:text-white">{{ $client->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Colis du client -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Colis ({{ $client->colis->count() }})</h4>
        @if($client->colis->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Numéro</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date envoi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                        @foreach($client->colis as $coli)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $coli->numero_suivi }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        @if($coli->statut === 'livre') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($coli->statut === 'en_transit') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                        @else bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $coli->statut)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $coli->date_envoi->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('colis.show', $coli) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Voir</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400 text-center py-4">Aucun colis pour ce client</p>
        @endif
    </div>
</div>
@endsection

