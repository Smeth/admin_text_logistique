@extends('layouts.app')

@section('title', 'Modifier la Caisse')
@section('page-title', 'Modifier la Caisse')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <form action="{{ route('caisses.update', $caisse) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nom_caisse" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nom de la caisse <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="nom_caisse" 
                           name="nom_caisse" 
                           value="{{ old('nom_caisse', $caisse->nom_caisse) }}" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                    @error('nom_caisse')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="agence_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Agence
                    </label>
                    <select id="agence_id" 
                            name="agence_id" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                        <option value="">Aucune agence</option>
                        @foreach($agences as $agence)
                            <option value="{{ $agence->id }}" {{ old('agence_id', $caisse->agence_id) == $agence->id ? 'selected' : '' }}>{{ $agence->nom_agence }}</option>
                        @endforeach
                    </select>
                    @error('agence_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="responsable_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Responsable
                    </label>
                    <select id="responsable_id" 
                            name="responsable_id" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                        <option value="">Aucun responsable</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('responsable_id', $caisse->responsable_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('responsable_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="solde_initial" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Solde initial (FCFA) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           step="0.01" 
                           id="solde_initial" 
                           name="solde_initial" 
                           value="{{ old('solde_initial', $caisse->solde_initial) }}" 
                           required
                           min="0"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                    @error('solde_initial')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Statut <span class="text-red-500">*</span>
                    </label>
                    <select id="statut" 
                            name="statut" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                        <option value="fermee" {{ old('statut', $caisse->statut) === 'fermee' ? 'selected' : '' }}>Fermée</option>
                        <option value="ouverte" {{ old('statut', $caisse->statut) === 'ouverte' ? 'selected' : '' }}>Ouverte</option>
                    </select>
                    @error('statut')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Notes
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">{{ old('notes', $caisse->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('caisses.index') }}" class="px-6 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Annuler</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection

