@extends('layouts.app')

@section('title', 'Modifier Colis')
@section('page-title', 'Modifier le Colis')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <form action="{{ route('colis.update', $coli) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Client <span class="text-red-500">*</span></label>
                    <select name="client_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $coli->client_id) == $client->id ? 'selected' : '' }}>{{ $client->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Numéro de suivi <span class="text-red-500">*</span></label>
                    <input type="text" name="numero_suivi" value="{{ old('numero_suivi', $coli->numero_suivi) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Poids (kg) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="poids" value="{{ old('poids', $coli->poids) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dimensions</label>
                    <input type="text" name="dimensions" value="{{ old('dimensions', $coli->dimensions) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Agence Départ <span class="text-red-500">*</span></label>
                    <select name="agence_depart_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        @foreach($agences as $agence)
                            <option value="{{ $agence->id }}" {{ old('agence_depart_id', $coli->agence_depart_id) == $agence->id ? 'selected' : '' }}>{{ $agence->nom_agence }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Agence Arrivée <span class="text-red-500">*</span></label>
                    <select name="agence_arrivee_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        @foreach($agences as $agence)
                            <option value="{{ $agence->id }}" {{ old('agence_arrivee_id', $coli->agence_arrivee_id) == $agence->id ? 'selected' : '' }}>{{ $agence->nom_agence }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pays d'origine</label>
                    <input type="text" name="pays_origine" value="{{ old('pays_origine', $coli->pays_origine) }}" 
                           placeholder="Ex: Cameroun, Sénégal..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    @error('pays_origine')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ville d'origine</label>
                    <input type="text" name="ville_origine" value="{{ old('ville_origine', $coli->ville_origine) }}" 
                           placeholder="Ex: Douala, Dakar..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    @error('ville_origine')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Transporteur</label>
                    <select name="transporteur_id" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Aucun</option>
                        @foreach($transporteurs as $transporteur)
                            <option value="{{ $transporteur->id }}" {{ old('transporteur_id', $coli->transporteur_id) == $transporteur->id ? 'selected' : '' }}>{{ $transporteur->nom_entreprise }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut <span class="text-red-500">*</span></label>
                    <select name="statut" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="en_attente" {{ old('statut', $coli->statut) === 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="en_transit" {{ old('statut', $coli->statut) === 'en_transit' ? 'selected' : '' }}>En transit</option>
                        <option value="livre" {{ old('statut', $coli->statut) === 'livre' ? 'selected' : '' }}>Livré</option>
                        <option value="retourne" {{ old('statut', $coli->statut) === 'retourne' ? 'selected' : '' }}>Retourné</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date envoi <span class="text-red-500">*</span></label>
                    <input type="date" name="date_envoi" value="{{ old('date_envoi', $coli->date_envoi->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date livraison prévue</label>
                    <input type="date" name="date_livraison_prevue" value="{{ old('date_livraison_prevue', $coli->date_livraison_prevue?->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Devise</label>
                    <select name="devise_id" id="devise_id" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Sélectionner une devise</option>
                        @foreach($devises as $devise)
                            <option value="{{ $devise->id }}" {{ old('devise_id', $coli->devise_id) == $devise->id ? 'selected' : '' }} data-symbole="{{ $devise->symbole }}">
                                {{ $devise->nom }} ({{ $devise->symbole }})@if($devise->est_principale) - Principale @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tarif (pour calcul automatique)</label>
                    <select name="tarif_id" id="tarif_id" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Aucun (saisie manuelle)</option>
                        @foreach($tarifs as $tarif)
                            <option value="{{ $tarif->id }}" {{ old('tarif_id', $coli->tarif_id) == $tarif->id ? 'selected' : '' }} data-prix-kilo="{{ $tarif->prix_par_kilo }}" data-prix-min="{{ $tarif->prix_minimum }}" data-prix-max="{{ $tarif->prix_maximum }}">
                                {{ $tarif->nom_tarif }} - {{ number_format($tarif->prix_par_kilo, 0, ',', ' ') }} FCFA/kg
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Frais transport <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="frais_transport" id="frais_transport" value="{{ old('frais_transport', $coli->frais_transport) }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    @if($coli->frais_calcule)
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Prix calculé: {{ number_format($coli->frais_calcule, 0, ',', ' ') }} FCFA</p>
                    @endif
                    <p id="prix_calcule_info" class="mt-1 text-xs text-green-600 dark:text-green-400 hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Valeur déclarée</label>
                    <input type="number" step="0.01" name="valeur_declaree" id="valeur_declaree" value="{{ old('valeur_declaree', $coli->valeur_declaree) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description contenu</label>
                <textarea name="description_contenu" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">{{ old('description_contenu', $coli->description_contenu) }}</textarea>
            </div>

            <!-- Section Paiement existant -->
            @if($coli->paiements->count() > 0)
            <div class="border-t border-gray-200 dark:border-slate-700 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Paiements enregistrés</h3>
                <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 space-y-2">
                    @foreach($coli->paiements as $paiement)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $paiement->date_paiement->format('d/m/Y') }} - {{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ $paiement->caisse ? $paiement->caisse->nom_caisse : '-' }}
                            </span>
                        </div>
                    @endforeach
                    <div class="pt-2 border-t border-gray-200 dark:border-slate-600 mt-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total payé:</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($coli->total_paye, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Reste à payer:</span>
                            <span class="text-sm font-bold {{ $coli->montant_restant > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ number_format($coli->montant_restant, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Section Ajouter un acompte -->
            @if($coli->montant_restant > 0)
            <div class="border-t border-gray-200 dark:border-slate-700 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ajouter un acompte</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Caisse <span class="text-red-500">*</span>
                        </label>
                        <select name="caisse_id" id="caisse_id" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                            <option value="">Sélectionner une caisse</option>
                            @foreach($caisses as $caisse)
                                <option value="{{ $caisse->id }}">{{ $caisse->nom_caisse }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Montant (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               step="0.01" 
                               name="montant_paye" 
                               id="montant_paye" 
                               value="" 
                               min="0.01"
                               max="{{ $coli->montant_restant }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <p id="montant_restant_info" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Maximum: {{ number_format($coli->montant_restant, 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Mode de paiement
                        </label>
                        <select name="mode_paiement" id="mode_paiement" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                            <option value="espece">Espèce</option>
                            <option value="carte">Carte</option>
                            <option value="virement">Virement</option>
                            <option value="cheque">Chèque</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                    </div>
                </div>
            </div>
            @endif
            <div class="flex justify-end space-x-4">
                <a href="{{ route('colis.index') }}" class="px-6 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Annuler</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">Enregistrer</button>
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

        let prix = poids * prixParKilo;
        if (prix < prixMinimum) prix = prixMinimum;
        if (prixMaximum && prix > prixMaximum) prix = prixMaximum;

        fraisTransportInput.value = prix.toFixed(2);
        const selectedOption = deviseSelect.options[deviseSelect.selectedIndex];
        const symbole = selectedOption ? selectedOption.getAttribute('data-symbole') || 'FCFA' : 'FCFA';
        prixCalculeInfo.textContent = `Prix calculé automatiquement: ${prix.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 2})} ${symbole}`;
        prixCalculeInfo.classList.remove('hidden');
    }

    poidsInput.addEventListener('input', calculerPrix);
    tarifSelect.addEventListener('change', calculerPrix);
    
    if (poidsInput.value && tarifSelect.value) {
        calculerPrix();
    }

    // Gestion de l'acompte supplémentaire
    const montantPayeInput = document.getElementById('montant_paye');
    const montantRestantInfo = document.getElementById('montant_restant_info');
    const fraisTransport = parseFloat(fraisTransportInput.value) || 0;
    const montantRestantMax = parseFloat('{{ $coli->montant_restant }}') || 0;

    if (montantPayeInput && montantRestantInfo) {
        montantPayeInput.addEventListener('input', function() {
            const montantPaye = parseFloat(this.value) || 0;
            const restant = montantRestantMax - montantPaye;
            
            if (montantPaye > 0) {
                if (restant > 0) {
                    montantRestantInfo.textContent = `Reste après cet acompte: ${restant.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 2})} FCFA`;
                    montantRestantInfo.className = 'mt-1 text-xs text-orange-600 dark:text-orange-400';
                } else if (restant == 0) {
                    montantRestantInfo.textContent = 'Paiement complet après cet acompte';
                    montantRestantInfo.className = 'mt-1 text-xs text-green-600 dark:text-green-400';
                } else {
                    montantRestantInfo.textContent = 'Attention: Montant supérieur au reste à payer';
                    montantRestantInfo.className = 'mt-1 text-xs text-red-600 dark:text-red-400';
                }
            } else {
                montantRestantInfo.textContent = `Maximum: ${montantRestantMax.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 2})} FCFA`;
                montantRestantInfo.className = 'mt-1 text-xs text-gray-500 dark:text-gray-400';
            }
        });
    }
});
</script>
@endsection

