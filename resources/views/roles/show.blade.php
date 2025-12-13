@extends('layouts.app')

@section('title', 'Détails Rôle')
@section('page-title', 'Détails du Rôle')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $role->display_name }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $role->name }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('roles.edit', $role) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Modifier</a>
                <a href="{{ route('roles.index') }}" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Retour</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nom technique</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $role->name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nom d'affichage</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $role->display_name }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $role->description ?? 'Aucune description' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre d'utilisateurs</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $role->users->count() }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date de création</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $role->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    @if($role->users->count() > 0)
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
        <div class="p-6 border-b border-gray-200 dark:border-slate-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Utilisateurs avec ce rôle</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($role->users as $user)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                        <a href="{{ route('users.show', $user) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium">
                            Voir →
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

