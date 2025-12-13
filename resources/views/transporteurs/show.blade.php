@extends('layouts.app')

@section('title', 'Détails Transporteur')
@section('page-title', 'Détails du Transporteur')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $transporteur->nom_entreprise }}</h3>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('transporteurs.edit', $transporteur) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Modifier</a>
                <a href="{{ route('transporteurs.index') }}" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Retour</a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $transporteur->email ?? '-' }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Téléphone</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $transporteur->telephone ?? '-' }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Type Transport</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $transporteur->type_transport ?? '-' }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Statut</p>
                <span class="inline-block mt-1 px-3 py-1 text-sm font-medium rounded-full {{ $transporteur->statut === 'actif' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
                    {{ ucfirst($transporteur->statut) }}
                </span>
            </div>
            @if($transporteur->adresse)
            <div class="md:col-span-2"><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Adresse</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $transporteur->adresse }}</p></div>
            @endif
        </div>
        <div class="mt-6">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Colis transportés</p>
            <p class="text-lg text-gray-900 dark:text-white">{{ $transporteur->colis->count() }}</p>
        </div>
    </div>
</div>
@endsection

