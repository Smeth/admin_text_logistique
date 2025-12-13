@extends('layouts.app')

@section('title', 'Modifier Agence')
@section('page-title', 'Modifier l\'Agence')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <form action="{{ route('agences.update', $agence) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom Agence <span class="text-red-500">*</span></label>
                <input type="text" name="nom_agence" value="{{ old('nom_agence', $agence->nom_agence) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Localisation <span class="text-red-500">*</span></label>
                <input type="text" name="localisation" value="{{ old('localisation', $agence->localisation) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Code Agence <span class="text-red-500">*</span></label>
                <input type="text" name="code_agence" value="{{ old('code_agence', $agence->code_agence) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
            </div>
            <div class="flex justify-end space-x-4">
                <a href="{{ route('agences.index') }}" class="px-6 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Annuler</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection

