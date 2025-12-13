@extends('layouts.app')

@section('title', 'Modifier Devise')
@section('page-title', 'Modifier la Devise')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <form action="{{ route('devises.update', $devise) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $devise->code) }}" maxlength="3" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Symbole <span class="text-red-500">*</span></label>
                    <input type="text" name="symbole" value="{{ old('symbole', $devise->symbole) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="nom" value="{{ old('nom', $devise->nom) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Taux de change <span class="text-red-500">*</span></label>
                <input type="number" step="0.0001" name="taux_change" value="{{ old('taux_change', $devise->taux_change) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
            </div>
            <div class="flex items-center space-x-6">
                <div class="flex items-center">
                    <input type="checkbox" name="est_principale" value="1" {{ old('est_principale', $devise->est_principale) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Devise principale</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="actif" value="1" {{ old('actif', $devise->actif) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Actif</label>
                </div>
            </div>
            <div class="flex justify-end space-x-4">
                <a href="{{ route('devises.index') }}" class="px-6 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Annuler</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection

