@extends('layouts.app')

@section('title', 'Détails Agence')
@section('page-title', 'Détails de l\'Agence')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $agence->nom_agence }}</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Code: {{ $agence->code_agence }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('agences.edit', $agence) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Modifier</a>
                <a href="{{ route('agences.index') }}" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Retour</a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Localisation</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $agence->localisation }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Colis au départ</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $agence->colisDepart->count() }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Colis à l'arrivée</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $agence->colisArrivee->count() }}</p></div>
        </div>
    </div>
</div>
@endsection

