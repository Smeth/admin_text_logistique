@extends('layouts.app')

@section('title', 'Modifier Client')
@section('page-title', 'Modifier le Client')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <form action="{{ route('clients.update', $client) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom <span class="text-red-500">*</span></label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom', $client->nom) }}" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                    @error('nom')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $client->prenom) }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                    @error('prenom')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $client->email) }}" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                    @error('email')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Téléphone <span class="text-red-500">*</span></label>
                    <input type="text" id="telephone" name="telephone" value="{{ old('telephone', $client->telephone) }}" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                    @error('telephone')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="adresse" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adresse <span class="text-red-500">*</span></label>
                <textarea id="adresse" name="adresse" rows="3" required
                          class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">{{ old('adresse', $client->adresse) }}</textarea>
                @error('adresse')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type <span class="text-red-500">*</span></label>
                    <select id="type" name="type" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                        <option value="particulier" {{ old('type', $client->type) === 'particulier' ? 'selected' : '' }}>Particulier</option>
                        <option value="entreprise" {{ old('type', $client->type) === 'entreprise' ? 'selected' : '' }}>Entreprise</option>
                    </select>
                </div>
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut <span class="text-red-500">*</span></label>
                    <select id="statut" name="statut" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                        <option value="actif" {{ old('statut', $client->statut) === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ old('statut', $client->statut) === 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                <textarea id="notes" name="notes" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">{{ old('notes', $client->notes) }}</textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('clients.index') }}" 
                   class="px-6 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

