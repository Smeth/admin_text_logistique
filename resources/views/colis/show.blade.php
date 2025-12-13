@extends('layouts.app')

@section('title', 'Détails Colis')
@section('page-title', 'Détails du Colis')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Colis #{{ $coli->numero_suivi }}</h3>
                <span class="inline-block mt-2 px-3 py-1 text-sm font-medium rounded-full 
                    @if($coli->statut === 'livre') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                    @elseif($coli->statut === 'en_transit') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                    @else bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $coli->statut)) }}
                </span>
            </div>
            <div class="flex space-x-2">
                @can('admin')
                <a href="{{ route('colis.edit', $coli) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Modifier</a>
                @endcan
                <a href="{{ route('colis.index') }}" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Retour</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Client</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->client->full_name }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Poids</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->poids }} kg</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Agence Départ</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->agenceDepart->nom_agence }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Agence Arrivée</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->agenceArrivee->nom_agence }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Transporteur</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->transporteur ? $coli->transporteur->nom_entreprise : 'Non assigné' }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Frais Transport</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ number_format($coli->frais_transport, 0, ',', ' ') }} FCFA</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date Envoi</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->date_envoi->format('d/m/Y') }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Payé</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->paye ? 'Oui' : 'Non' }}</p></div>
        </div>
        @if($coli->description_contenu)
        <div class="mt-6"><p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</p><p class="text-gray-900 dark:text-white">{{ $coli->description_contenu }}</p></div>
        @endif
    </div>
</div>
@endsection

