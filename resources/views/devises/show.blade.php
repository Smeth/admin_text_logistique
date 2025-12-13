@extends('layouts.app')

@section('title', 'Détails Devise')
@section('page-title', 'Détails de la Devise')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $devise->nom }} ({{ $devise->code }})</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Symbole: {{ $devise->symbole }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('devises.edit', $devise) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Modifier</a>
                <a href="{{ route('devises.index') }}" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Retour</a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Taux de change</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ number_format($devise->taux_change, 4) }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Statut</p>
                <div class="mt-1 flex gap-2">
                    @if($devise->est_principale)
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">Principale</span>
                    @endif
                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ $devise->actif ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
                        {{ $devise->actif ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Colis utilisant cette devise</p>
            <p class="text-lg text-gray-900 dark:text-white">{{ $devise->colis->count() }}</p>
        </div>
    </div>
</div>
@endsection

