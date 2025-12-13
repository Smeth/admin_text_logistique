@extends('layouts.app')

@section('title', 'Nouvelle Transaction')
@section('page-title', 'Créer une Transaction')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <form action="{{ route('transactions.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="caisse_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Caisse <span class="text-red-500">*</span>
                    </label>
                    <select id="caisse_id" 
                            name="caisse_id" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                        <option value="">Sélectionner une caisse</option>
                        @foreach($caisses as $caisse)
                            <option value="{{ $caisse->id }}" {{ old('caisse_id', request('caisse_id')) == $caisse->id ? 'selected' : '' }}>{{ $caisse->nom_caisse }} ({{ $caisse->statut === 'ouverte' ? 'Ouverte' : 'Fermée' }})</option>
                        @endforeach
                    </select>
                    @error('caisse_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <select id="type" 
                            name="type" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                        <option value="">Sélectionner</option>
                        <option value="entree" {{ old('type') === 'entree' ? 'selected' : '' }}>Entrée</option>
                        <option value="sortie" {{ old('type') === 'sortie' ? 'selected' : '' }}>Sortie</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="libelle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Libellé <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="libelle" 
                           name="libelle" 
                           value="{{ old('libelle') }}" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                    @error('libelle')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="montant" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Montant (FCFA) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           step="0.01" 
                           id="montant" 
                           name="montant" 
                           value="{{ old('montant') }}" 
                           required
                           min="0.01"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                    @error('montant')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="devise_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Devise
                    </label>
                    <select id="devise_id" 
                            name="devise_id" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                        <option value="">Sélectionner une devise</option>
                        @foreach($devises as $devise)
                            @php
                                $devisePrincipale = $devises->firstWhere('est_principale', true);
                                $deviseParDefaut = $devisePrincipale ?? $devises->first();
                            @endphp
                            <option value="{{ $devise->id }}" {{ old('devise_id', $deviseParDefaut?->id) == $devise->id ? 'selected' : '' }}>{{ $devise->nom }} ({{ $devise->symbole }})@if($devise->est_principale) - Principale @endif</option>
                        @endforeach
                    </select>
                    @error('devise_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_transaction" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Date de transaction <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="date_transaction" 
                           name="date_transaction" 
                           value="{{ old('date_transaction', date('Y-m-d')) }}" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                    @error('date_transaction')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="coli_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Colis (si paiement)
                    </label>
                    <select id="coli_id" 
                            name="coli_id" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                        <option value="">Aucun colis</option>
                        @foreach($colis as $coli)
                            <option value="{{ $coli->id }}" {{ old('coli_id') == $coli->id ? 'selected' : '' }}>{{ $coli->numero_suivi }} - {{ $coli->client->full_name }} ({{ number_format($coli->frais_transport, 0, ',', ' ') }} FCFA)</option>
                        @endforeach
                    </select>
                    @error('coli_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div id="mode_paiement_container" class="hidden">
                    <label for="mode_paiement" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Mode de paiement
                    </label>
                    <select id="mode_paiement" 
                            name="mode_paiement" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                        <option value="espece">Espèce</option>
                        <option value="carte">Carte</option>
                        <option value="virement">Virement</option>
                        <option value="cheque">Chèque</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>

                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Client
                    </label>
                    <select id="client_id" 
                            name="client_id" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                        <option value="">Aucun client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->full_name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reference" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Référence
                    </label>
                    <input type="text" 
                           id="reference" 
                           name="reference" 
                           value="{{ old('reference') }}" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">
                    @error('reference')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-700 dark:text-white">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('transactions.index') }}" class="px-6 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Annuler</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">Créer</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const coliSelect = document.getElementById('coli_id');
    const modePaiementContainer = document.getElementById('mode_paiement_container');
    const typeSelect = document.getElementById('type');

    function toggleModePaiement() {
        if (coliSelect.value && typeSelect.value === 'entree') {
            modePaiementContainer.classList.remove('hidden');
        } else {
            modePaiementContainer.classList.add('hidden');
        }
    }

    coliSelect.addEventListener('change', toggleModePaiement);
    typeSelect.addEventListener('change', toggleModePaiement);
});
</script>
@endsection

