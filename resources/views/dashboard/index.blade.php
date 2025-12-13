@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Clients -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Clients</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalClients }}</p>
                </div>
                <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Nouveaux Clients -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nouveaux Clients</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $nouveauxClients }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aujourd'hui</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Clients Actifs -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Clients Actifs</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $clientsActifs }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Colis Livrés Aujourd'hui -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Colis Livrés</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $colisLivreAujourdhui }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aujourd'hui</p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques supplémentaires -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-slate-700">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Colis</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalColis }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-slate-700">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">En Transit</p>
            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-2">{{ $colisEnTransit }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-slate-700">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Revenus du Jour</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-2">{{ number_format($revenusJour, 0, ',', ' ') }} FCFA</p>
        </div>
    </div>

    <!-- Derniers clients et colis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Derniers Clients -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
            <div class="p-6 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Derniers Clients</h3>
            </div>
            <div class="p-6">
                @if($derniersClients->count() > 0)
                    <div class="space-y-4">
                        @foreach($derniersClients as $client)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $client->full_name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $client->email }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $client->statut === 'actif' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ ucfirst($client->statut) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('clients.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium">
                            Voir tous les clients →
                        </a>
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">Aucun client pour le moment</p>
                @endif
            </div>
        </div>

        <!-- Derniers Colis -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
            <div class="p-6 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Derniers Colis</h3>
            </div>
            <div class="p-6">
                @if($derniersColis->count() > 0)
                    <div class="space-y-4">
                        @foreach($derniersColis as $coli)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $coli->numero_suivi }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $coli->client->full_name }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($coli->statut === 'livre') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($coli->statut === 'en_transit') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                    @elseif($coli->statut === 'en_attente') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                    @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $coli->statut)) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('colis.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium">
                            Voir tous les colis →
                        </a>
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">Aucun colis pour le moment</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

