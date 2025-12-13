@extends('layouts.app')

@section('title', 'Détails Utilisateur')
@section('page-title', 'Détails de l\'Utilisateur')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $user->email }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('users.edit', $user) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Modifier</a>
                <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Retour</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rôle</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">
                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                        @if($user->role?->name === 'admin') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400
                        @elseif($user->role?->name === 'superviseur') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                        @elseif($user->role?->name === 'responsable_agence') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400
                        @endif">
                        {{ $user->role?->display_name ?? 'Aucun rôle' }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Agence</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $user->agence?->nom_agence ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date de création</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $user->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Dernière mise à jour</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

