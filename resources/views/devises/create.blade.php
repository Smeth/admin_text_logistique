@extends('layouts.app')

@section('title', 'Nouvelle Devise')
@section('page-title', 'Créer une Devise')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <form action="{{ route('devises.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" maxlength="10" required placeholder="FCFA, EUR, USD" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    @error('code')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Symbole <span class="text-red-500">*</span></label>
                    <input type="text" name="symbole" value="{{ old('symbole') }}" required placeholder="FCFA, €, $" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    @error('symbole')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="nom" value="{{ old('nom') }}" required placeholder="Franc CFA, Euro, Dollar" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                @error('nom')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Taux de change <span class="text-red-500">*</span></label>
                <input type="number" step="0.0001" name="taux_change" value="{{ old('taux_change', 1) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Taux par rapport à la devise principale (1.0000 pour la devise principale)</p>
                @error('taux_change')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center space-x-6">
                <div class="flex items-center">
                    <input type="checkbox" name="est_principale" value="1" {{ old('est_principale') ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Devise principale</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="actif" value="1" {{ old('actif', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Actif</label>
                </div>
            </div>
            <div class="flex justify-end space-x-4">
                <a href="{{ route('devises.index') }}" class="px-6 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Annuler</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">Créer</button>
            </div>
        </form>
    </div>
</div>
@endsection

