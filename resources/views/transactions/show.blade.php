@extends('layouts.app')

@section('title', 'Détails Transaction')
@section('page-title', 'Détails de la Transaction')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $transaction->libelle }}</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $transaction->date_transaction->format('d/m/Y') }}</p>
            </div>
            <div class="flex space-x-2">
                @if(auth()->user()->isAdmin())
                    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                            Supprimer
                        </button>
                    </form>
                @endif
                <a href="{{ route('transactions.index') }}" 
                   class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</p>
                <span class="inline-block mt-1 px-3 py-1 text-sm font-medium rounded-full {{ $transaction->type === 'entree' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                    {{ $transaction->type === 'entree' ? 'Entrée' : 'Sortie' }}
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Montant</p>
                <p class="text-2xl font-bold {{ $transaction->type === 'entree' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-1">
                    {{ $transaction->type === 'entree' ? '+' : '-' }}{{ number_format($transaction->montant, 0, ',', ' ') }} {{ $transaction->devise ? $transaction->devise->symbole : 'FCFA' }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Caisse</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $transaction->caisse->nom_caisse }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date de transaction</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $transaction->date_transaction->format('d/m/Y') }}</p>
            </div>
            @if($transaction->coli)
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Colis</p>
                <a href="{{ route('colis.show', $transaction->coli) }}" class="text-lg text-indigo-600 dark:text-indigo-400 hover:underline mt-1">
                    {{ $transaction->coli->numero_suivi }}
                </a>
            </div>
            @endif
            @if($transaction->client)
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Client</p>
                <a href="{{ route('clients.show', $transaction->client) }}" class="text-lg text-indigo-600 dark:text-indigo-400 hover:underline mt-1">
                    {{ $transaction->client->full_name }}
                </a>
            </div>
            @endif
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Utilisateur</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $transaction->user->name }}</p>
            </div>
            @if($transaction->reference)
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Référence</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $transaction->reference }}</p>
            </div>
            @endif
        </div>

        @if($transaction->description)
        <div class="mt-6">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</p>
            <p class="text-gray-900 dark:text-white">{{ $transaction->description }}</p>
        </div>
        @endif

        @if($transaction->paiement)
        <div class="mt-6 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Informations de paiement</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Mode de paiement</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ ucfirst(str_replace('_', ' ', $transaction->paiement->mode_paiement)) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Date de paiement</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $transaction->paiement->date_paiement->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

