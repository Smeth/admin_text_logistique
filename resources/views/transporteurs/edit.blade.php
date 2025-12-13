@extends('layouts.app')

@section('title', 'Modifier Transporteur')
@section('page-title', 'Modifier le Transporteur')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <form action="{{ route('transporteurs.update', $transporteur) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom Entreprise <span class="text-red-500">*</span></label>
                    <input type="text" name="nom_entreprise" value="{{ old('nom_entreprise', $transporteur->nom_entreprise) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $transporteur->email) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $transporteur->telephone) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type Transport</label>
                    <input type="text" name="type_transport" value="{{ old('type_transport', $transporteur->type_transport) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut <span class="text-red-500">*</span></label>
                    <select name="statut" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="actif" {{ old('statut', $transporteur->statut) === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ old('statut', $transporteur->statut) === 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adresse</label>
                <textarea name="adresse" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">{{ old('adresse', $transporteur->adresse) }}</textarea>
            </div>
            <div class="flex justify-end space-x-4">
                <a href="{{ route('transporteurs.index') }}" class="px-6 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Annuler</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection

