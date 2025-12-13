@extends('layouts.app')

@section('title', 'Détails Tarif')
@section('page-title', 'Détails du Tarif')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $tarif->nom_tarif }}</h3>
                @if($tarif->description)
                    <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $tarif->description }}</p>
                @endif
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('tarifs.edit', $tarif) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Modifier</a>
                <a href="{{ route('tarifs.index') }}" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Retour</a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Prix par kilo</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ number_format($tarif->prix_par_kilo, 0, ',', ' ') }} FCFA</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Prix minimum</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ number_format($tarif->prix_minimum, 0, ',', ' ') }} FCFA</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Prix maximum</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $tarif->prix_maximum ? number_format($tarif->prix_maximum, 0, ',', ' ') . ' FCFA' : 'Illimité' }}</p></div>
        </div>
        @if($tarif->agenceDepart || $tarif->agenceArrivee || $tarif->transporteur)
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            @if($tarif->agenceDepart)
                <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Agence départ</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $tarif->agenceDepart->nom_agence }}</p></div>
            @endif
            @if($tarif->agenceArrivee)
                <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Agence arrivée</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $tarif->agenceArrivee->nom_agence }}</p></div>
            @endif
            @if($tarif->transporteur)
                <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Transporteur</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $tarif->transporteur->nom_entreprise }}</p></div>
            @endif
        </div>
        @endif
        <div class="mt-6">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Statut</p>
            <span class="px-3 py-1 text-sm font-medium rounded-full {{ $tarif->actif ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
                {{ $tarif->actif ? 'Actif' : 'Inactif' }}
            </span>
        </div>
        <div class="mt-6">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Colis utilisant ce tarif</p>
            <p class="text-lg text-gray-900 dark:text-white">{{ $tarif->colis->count() }}</p>
        </div>
    </div>
</div>
@endsection

