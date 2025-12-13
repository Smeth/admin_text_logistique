@extends('layouts.app')

@section('title', 'Nouveau Colis')
@section('page-title', 'Créer un Colis')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <form action="{{ route('colis.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Client <span class="text-red-500">*</span></label>
                    <select name="client_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Sélectionner un client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->full_name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Numéro de suivi <span class="text-red-500">*</span></label>
                    <input type="text" name="numero_suivi" value="{{ old('numero_suivi') }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    @error('numero_suivi')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Poids (kg) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="poids" value="{{ old('poids') }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    @error('poids')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dimensions</label>
                    <input type="text" name="dimensions" value="{{ old('dimensions') }}" placeholder="L x l x H" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Agence Départ <span class="text-red-500">*</span></label>
                    <select name="agence_depart_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Sélectionner</option>
                        @foreach($agences as $agence)
                            <option value="{{ $agence->id }}" {{ old('agence_depart_id') == $agence->id ? 'selected' : '' }}>{{ $agence->nom_agence }}</option>
                        @endforeach
                    </select>
                    @error('agence_depart_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Agence Arrivée <span class="text-red-500">*</span></label>
                    <select name="agence_arrivee_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Sélectionner</option>
                        @foreach($agences as $agence)
                            <option value="{{ $agence->id }}" {{ old('agence_arrivee_id') == $agence->id ? 'selected' : '' }}>{{ $agence->nom_agence }}</option>
                        @endforeach
                    </select>
                    @error('agence_arrivee_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Transporteur</label>
                    <select name="transporteur_id" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Aucun</option>
                        @foreach($transporteurs as $transporteur)
                            <option value="{{ $transporteur->id }}" {{ old('transporteur_id') == $transporteur->id ? 'selected' : '' }}>{{ $transporteur->nom_entreprise }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut <span class="text-red-500">*</span></label>
                    <select name="statut" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="en_attente" {{ old('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="en_transit" {{ old('statut') === 'en_transit' ? 'selected' : '' }}>En transit</option>
                        <option value="livre" {{ old('statut') === 'livre' ? 'selected' : '' }}>Livré</option>
                        <option value="retourne" {{ old('statut') === 'retourne' ? 'selected' : '' }}>Retourné</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date envoi <span class="text-red-500">*</span></label>
                    <input type="date" name="date_envoi" value="{{ old('date_envoi', date('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date livraison prévue</label>
                    <input type="date" name="date_livraison_prevue" value="{{ old('date_livraison_prevue') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Frais transport (FCFA) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="frais_transport" value="{{ old('frais_transport') }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Valeur déclarée (FCFA)</label>
                    <input type="number" step="0.01" name="valeur_declaree" value="{{ old('valeur_declaree') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description contenu</label>
                <textarea name="description_contenu" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">{{ old('description_contenu') }}</textarea>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="paye" value="1" {{ old('paye') ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Payé</label>
            </div>
            <div class="flex justify-end space-x-4">
                <a href="{{ route('colis.index') }}" class="px-6 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Annuler</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">Créer</button>
            </div>
        </form>
    </div>
</div>
@endsection

