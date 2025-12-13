@extends('layouts.app')

@section('title', 'Détails Caisse')
@section('page-title', 'Détails de la Caisse')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $caisse->nom_caisse }}</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    {{ $caisse->agence ? $caisse->agence->nom_agence : 'Aucune agence' }}
                </p>
            </div>
            <div class="flex space-x-2">
                @if($caisse->isOuverte())
                    <form action="{{ route('caisses.fermer', $caisse) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                            Fermer la caisse
                        </button>
                    </form>
                @else
                    <form action="{{ route('caisses.ouvrir', $caisse) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            Ouvrir la caisse
                        </button>
                    </form>
                @endif
                <a href="{{ route('caisses.edit', $caisse) }}" 
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Modifier
                </a>
                <a href="{{ route('caisses.index') }}" 
                   class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Retour
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Solde Initial</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($caisse->solde_initial, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Solde Actuel</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ number_format($caisse->solde_actuel, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Entrées Aujourd'hui</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ number_format($entreesAujourdhui, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sorties Aujourd'hui</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ number_format($sortiesAujourdhui, 0, ',', ' ') }} FCFA</p>
            </div>
        </div>

        <!-- Informations -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Responsable</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $caisse->responsable ? $caisse->responsable->name : '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Statut</p>
                <span class="inline-block mt-1 px-3 py-1 text-sm font-medium rounded-full {{ $caisse->statut === 'ouverte' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
                    {{ $caisse->statut === 'ouverte' ? 'Ouverte' : 'Fermée' }}
                </span>
            </div>
            @if($caisse->date_ouverture)
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date d'ouverture</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $caisse->date_ouverture->format('d/m/Y H:i') }}</p>
            </div>
            @endif
            @if($caisse->date_fermeture)
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date de fermeture</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $caisse->date_fermeture->format('d/m/Y H:i') }}</p>
            </div>
            @endif
        </div>

        @if($caisse->notes)
        <div class="mt-6">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</p>
            <p class="text-gray-900 dark:text-white">{{ $caisse->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Dernières transactions -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
        <div class="p-6 border-b border-gray-200 dark:border-slate-700">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Dernières Transactions</h3>
                <a href="{{ route('transactions.create', ['caisse_id' => $caisse->id]) }}" 
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Nouvelle Transaction
                </a>
            </div>
        </div>
        <div class="p-6">
            @if($dernieresTransactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Libellé</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Montant</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Utilisateur</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                            @foreach($dernieresTransactions as $transaction)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $transaction->date_transaction->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ $transaction->libelle }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->type === 'entree' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                            {{ $transaction->type === 'entree' ? 'Entrée' : 'Sortie' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-semibold {{ $transaction->type === 'entree' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $transaction->type === 'entree' ? '+' : '-' }}{{ number_format($transaction->montant, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $transaction->user->name }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <a href="{{ route('transactions.index', ['caisse_id' => $caisse->id]) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium">
                        Voir toutes les transactions →
                    </a>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">Aucune transaction pour le moment</p>
            @endif
        </div>
    </div>
</div>
@endsection

