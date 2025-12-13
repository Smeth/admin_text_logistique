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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Devise</label>
                    <select name="devise_id" id="devise_id" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Sélectionner une devise</option>
                        @foreach($devises as $devise)
                            <option value="{{ $devise->id }}" {{ old('devise_id', $devises->firstWhere('est_principale', true)?->id) == $devise->id ? 'selected' : '' }} data-symbole="{{ $devise->symbole }}">
                                {{ $devise->nom }} ({{ $devise->symbole }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tarif (pour calcul automatique)</label>
                    <select name="tarif_id" id="tarif_id" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Aucun (saisie manuelle)</option>
                        @foreach($tarifs as $tarif)
                            <option value="{{ $tarif->id }}" data-prix-kilo="{{ $tarif->prix_par_kilo }}" data-prix-min="{{ $tarif->prix_minimum }}" data-prix-max="{{ $tarif->prix_maximum }}">
                                {{ $tarif->nom_tarif }} - {{ number_format($tarif->prix_par_kilo, 0, ',', ' ') }} FCFA/kg
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Sélectionnez un tarif pour calculer automatiquement le prix</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Frais transport <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="frais_transport" id="frais_transport" value="{{ old('frais_transport') }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    <p id="prix_calcule_info" class="mt-1 text-xs text-green-600 dark:text-green-400 hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Valeur déclarée</label>
                    <input type="number" step="0.01" name="valeur_declaree" id="valeur_declaree" value="{{ old('valeur_declaree') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
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

<style>
    /* Masquer les spinners des inputs numériques */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const poidsInput = document.querySelector('input[name="poids"]');
    const tarifSelect = document.getElementById('tarif_id');
    const fraisTransportInput = document.getElementById('frais_transport');
    const prixCalculeInfo = document.getElementById('prix_calcule_info');
    const deviseSelect = document.getElementById('devise_id');

    // Calculer le prix automatiquement
    function calculerPrix() {
        const poids = parseFloat(poidsInput.value) || 0;
        const tarifOption = tarifSelect.options[tarifSelect.selectedIndex];

        if (!tarifOption || !tarifOption.value || poids <= 0) {
            prixCalculeInfo.classList.add('hidden');
            return;
        }

        const prixParKilo = parseFloat(tarifOption.getAttribute('data-prix-kilo')) || 0;
        const prixMinimum = parseFloat(tarifOption.getAttribute('data-prix-min')) || 0;
        const prixMaximum = tarifOption.getAttribute('data-prix-max') ? parseFloat(tarifOption.getAttribute('data-prix-max')) : null;

        // Calculer le prix
        let prix = poids * prixParKilo;

        // Appliquer le minimum
        if (prix < prixMinimum) {
            prix = prixMinimum;
        }

        // Appliquer le maximum si défini
        if (prixMaximum && prix > prixMaximum) {
            prix = prixMaximum;
        }

        // Mettre à jour le champ frais_transport
        fraisTransportInput.value = prix.toFixed(2);

        // Afficher l'info
        const selectedOption = deviseSelect.options[deviseSelect.selectedIndex];
        const symbole = selectedOption ? selectedOption.getAttribute('data-symbole') || 'FCFA' : 'FCFA';
        prixCalculeInfo.textContent = `Prix calculé automatiquement: ${prix.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 2})} ${symbole}`;
        prixCalculeInfo.classList.remove('hidden');
    }

    poidsInput.addEventListener('input', calculerPrix);
    tarifSelect.addEventListener('change', calculerPrix);

    // Calculer au chargement si des valeurs existent
    if (poidsInput.value && tarifSelect.value) {
        calculerPrix();
    }
});
</script>
@endsection

