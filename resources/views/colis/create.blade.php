@extends('layouts.app')

@section('title', 'Nouveau Colis')
@section('page-title', 'Créer un Colis')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <form action="{{ route('colis.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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
                    <input type="number" 
                           step="0.01" 
                           min="0" 
                           name="poids" 
                           id="poids"
                           value="{{ old('poids') }}" 
                           placeholder="Ex: 2.50"
                           required 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Valeur décimale uniquement (ex: 2.50 kg)</p>
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pays d'origine</label>
                    <input type="text" name="pays_origine" value="{{ old('pays_origine') }}" 
                           placeholder="Ex: Cameroun, Sénégal..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    @error('pays_origine')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ville d'origine</label>
                    <input type="text" name="ville_origine" value="{{ old('ville_origine') }}" 
                           placeholder="Ex: Douala, Dakar..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    @error('ville_origine')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
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
                    <select name="statut" id="statut_select" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="emballe" {{ old('statut', 'emballe') === 'emballe' ? 'selected' : '' }}>Colis emballé</option>
                        <option value="expedie_port" {{ old('statut') === 'expedie_port' ? 'selected' : '' }}>Expédié vers le port/aéroport</option>
                        <option value="arrive_aeroport_depart" {{ old('statut') === 'arrive_aeroport_depart' ? 'selected' : '' }}>Arrivé à l'aéroport de départ</option>
                        <option value="en_vol" {{ old('statut') === 'en_vol' ? 'selected' : '' }}>En vol vers destination</option>
                        <option value="arrive_aeroport_transit" {{ old('statut') === 'arrive_aeroport_transit' ? 'selected' : '' }}>Arrivé à l'aéroport de transit</option>
                        <option value="arrive_aeroport_destination" {{ old('statut') === 'arrive_aeroport_destination' ? 'selected' : '' }}>Arrivé à l'aéroport de destination</option>
                        <option value="en_dedouanement" {{ old('statut') === 'en_dedouanement' ? 'selected' : '' }}>En cours de dédouanement</option>
                        <option value="arrive_entrepot" {{ old('statut') === 'arrive_entrepot' ? 'selected' : '' }}>Arrivé à l'entrepôt de destination</option>
                        <option value="livre" {{ old('statut') === 'livre' ? 'selected' : '' }}>Livré</option>
                        <option value="retourne" {{ old('statut') === 'retourne' ? 'selected' : '' }}>Retourné</option>
                    </select>
                </div>
                
                <!-- Champs pour description et localisation -->
                <div id="description_etape_container" class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description de l'étape
                    </label>
                    <textarea name="description_etape" 
                              id="description_etape"
                              rows="3"
                              placeholder="Décrivez cette étape (ex: Colis emballé et prêt pour l'expédition. Poids vérifié: 2.5kg)"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">{{ old('description_etape') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ajoutez des détails sur cette étape (optionnel)</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Localisation actuelle
                    </label>
                    <input type="text" 
                           name="localisation_etape" 
                           id="localisation_etape"
                           value="{{ old('localisation_etape') }}"
                           placeholder="Ex: Entrepôt Douala, Aéroport Yaoundé..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Où se trouve le colis actuellement (optionnel)</p>
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
                            @php
                                $devisePrincipale = $devises->firstWhere('est_principale', true);
                                $deviseParDefaut = $devisePrincipale ?? $devises->first();
                            @endphp
                            <option value="{{ $devise->id }}" {{ old('devise_id', $deviseParDefaut?->id) == $devise->id ? 'selected' : '' }} data-symbole="{{ $devise->symbole }}">
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
                            <option value="{{ $tarif->id }}" data-prix-kilo="{{ $tarif->prix_par_kilo }}" data-prix-min="{{ $tarif->prix_minimum }}" data-prix-max="{{ $tarif->prix_maximum }}">
                                {{ $tarif->nom_tarif }} - {{ number_format($tarif->prix_par_kilo, 0, ',', ' ') }} FCFA/kg
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Sélectionnez un tarif pour calculer automatiquement le prix</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Frais transport <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="frais_transport" 
                           id="frais_transport" 
                           value="{{ old('frais_transport') ? number_format(old('frais_transport'), 0, ',', ' ') : '' }}" 
                           placeholder="Ex: 10 000"
                           required 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                    <input type="hidden" id="frais_transport_raw" name="frais_transport_raw">
                    <p id="prix_calcule_info" class="mt-1 text-xs text-green-600 dark:text-green-400 hidden"></p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: séparateurs de milliers automatiques</p>
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

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Images du colis (optionnel)
                </label>
                <div class="space-y-3">
                    <div class="flex items-center justify-center w-full">
                        <label for="images_upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 dark:border-slate-600 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-slate-700/50 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-10 h-10 mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold">Cliquez pour sélectionner</span> ou glissez-déposez
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Plusieurs images possibles (JPEG, PNG, WebP, max 4 Mo chacune)
                                </p>
                            </div>
                            <input id="images_upload" type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                        </label>
                    </div>
                    
                    <!-- Compteur d'images -->
                    <div id="images_count" class="hidden text-sm font-medium text-gray-700 dark:text-gray-300">
                        <span id="images_count_number">0</span> image(s) sélectionnée(s)
                    </div>
                    
                    <!-- Aperçu des images sélectionnées -->
                    <div id="images_preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 hidden">
                        <!-- Les miniatures seront ajoutées ici par JavaScript -->
                    </div>
                </div>
                @error('images')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                @error('images.*')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <!-- Section Paiement -->
            <div class="border-t border-gray-200 dark:border-slate-700 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Paiement</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="paiement_complet" id="paiement_complet" value="1" {{ old('paiement_complet') ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="paiement_complet" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Paiement complet</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="paiement_partiel" id="paiement_partiel" value="1" {{ old('paiement_partiel') ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="paiement_partiel" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Acompte</label>
                        </div>
                    </div>

                    <!-- Section détails paiement (visible si paiement_complet ou paiement_partiel est coché) -->
                    <div id="paiement_section" class="hidden grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Caisse <span class="text-red-500">*</span>
                            </label>
                            <select name="caisse_id" id="caisse_id" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                                <option value="">Sélectionner une caisse</option>
                                @foreach($caisses as $caisse)
                                    <option value="{{ $caisse->id }}" {{ old('caisse_id') == $caisse->id ? 'selected' : '' }}>{{ $caisse->nom_caisse }}</option>
                                @endforeach
                            </select>
                            @error('caisse_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        
                        <div id="montant_paiement_container">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Montant payé (FCFA) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   step="0.01" 
                                   name="montant_paye" 
                                   id="montant_paye" 
                                   value="{{ old('montant_paye') }}" 
                                   min="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                            <p id="montant_restant_info" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
                            @error('montant_paye')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Mode de paiement
                            </label>
                            <select name="mode_paiement" id="mode_paiement" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                                <option value="espece" {{ old('mode_paiement', 'espece') === 'espece' ? 'selected' : '' }}>Espèce</option>
                                <option value="carte" {{ old('mode_paiement') === 'carte' ? 'selected' : '' }}>Carte</option>
                                <option value="virement" {{ old('mode_paiement') === 'virement' ? 'selected' : '' }}>Virement</option>
                                <option value="cheque" {{ old('mode_paiement') === 'cheque' ? 'selected' : '' }}>Chèque</option>
                                <option value="mobile_money" {{ old('mode_paiement') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            </select>
                        </div>
                    </div>
                </div>
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
    const poidsInput = document.getElementById('poids');
    const tarifSelect = document.getElementById('tarif_id');
    const fraisTransportInput = document.getElementById('frais_transport');
    const fraisTransportRaw = document.getElementById('frais_transport_raw');
    const prixCalculeInfo = document.getElementById('prix_calcule_info');
    const deviseSelect = document.getElementById('devise_id');
    // Fonction pour formater un nombre avec séparateurs de milliers
    function formatNumber(num) {
        if (!num && num !== 0) return '';
        const numStr = num.toString().replace(/\s/g, '').replace(/,/g, '.');
        const numValue = parseFloat(numStr);
        if (isNaN(numValue)) return '';
        return Math.round(numValue).toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }

    // Fonction pour convertir un nombre formaté en valeur numérique
    function parseFormattedNumber(str) {
        if (!str) return 0;
        const cleaned = str.toString().replace(/\s/g, '').replace(/,/g, '.');
        const num = parseFloat(cleaned);
        return isNaN(num) ? 0 : num;
    }

    // Formater le champ frais de transport
    fraisTransportInput.addEventListener('input', function(e) {
        const cursorPosition = this.selectionStart;
        const value = this.value;
        const numericValue = parseFormattedNumber(value);
        const formatted = formatNumber(numericValue);
        
        this.value = formatted;
        
        // Restaurer la position du curseur
        const diff = formatted.length - value.length;
        this.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
        
        // Mettre à jour la valeur brute pour le calcul
        fraisTransportRaw.value = numericValue;
    });

    // Convertir la valeur formatée en valeur numérique avant soumission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const formattedValue = fraisTransportInput.value;
        const numericValue = parseFormattedNumber(formattedValue);
        
        // Créer un champ caché avec la valeur numérique ou remplacer la valeur
        if (fraisTransportRaw) {
            fraisTransportRaw.value = numericValue;
            fraisTransportRaw.name = 'frais_transport';
            fraisTransportInput.name = '';
        } else {
            // Créer un input caché avec la valeur numérique
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'frais_transport';
            hiddenInput.value = numericValue;
            form.appendChild(hiddenInput);
            fraisTransportInput.name = '';
        }
    });


    // Formater le poids avec 2 décimales
    poidsInput.addEventListener('blur', function() {
        const value = parseFloat(this.value);
        if (!isNaN(value) && value >= 0) {
            this.value = value.toFixed(2);
        }
    });

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

        // Mettre à jour le champ frais_transport avec formatage
        fraisTransportInput.value = formatNumber(prix);
        fraisTransportRaw.value = prix;

        // Afficher l'info
        const selectedOption = deviseSelect.options[deviseSelect.selectedIndex];
        const symbole = selectedOption ? selectedOption.getAttribute('data-symbole') || 'FCFA' : 'FCFA';
        prixCalculeInfo.textContent = `Prix calculé automatiquement: ${formatNumber(prix)} ${symbole}`;
        prixCalculeInfo.classList.remove('hidden');
    }

    poidsInput.addEventListener('input', calculerPrix);
    tarifSelect.addEventListener('change', calculerPrix);

    // Calculer au chargement si des valeurs existent
    if (poidsInput.value && tarifSelect.value) {
        calculerPrix();
    }

    // Gestion des paiements (complet/partiel)
    const paiementComplet = document.getElementById('paiement_complet');
    const paiementPartiel = document.getElementById('paiement_partiel');
    const paiementSection = document.getElementById('paiement_section');
    const montantPayeInput = document.getElementById('montant_paye');
    const montantRestantInfo = document.getElementById('montant_restant_info');
    const caisseSelect = document.getElementById('caisse_id');

    function togglePaiementSection() {
        if (paiementComplet.checked || paiementPartiel.checked) {
            paiementSection.classList.remove('hidden');
            caisseSelect.required = true;
            
            if (paiementComplet.checked) {
                // Si paiement complet, pré-remplir avec le montant total
                const fraisTransport = parseFormattedNumber(fraisTransportInput.value) || 0;
                montantPayeInput.value = fraisTransport.toFixed(2);
                montantPayeInput.readOnly = true;
                montantPayeInput.required = false;
                montantRestantInfo.textContent = 'Paiement complet';
                montantRestantInfo.className = 'mt-1 text-xs text-green-600 dark:text-green-400';
            } else {
                // Si acompte, permettre la saisie
                montantPayeInput.readOnly = false;
                montantPayeInput.required = true;
                if (!montantPayeInput.value) {
                    montantPayeInput.value = '';
                }
                updateMontantRestant();
            }
        } else {
            paiementSection.classList.add('hidden');
            caisseSelect.required = false;
            montantPayeInput.required = false;
        }
    }

    function updateMontantRestant() {
        const fraisTransport = parseFormattedNumber(fraisTransportInput.value) || 0;
        const montantPaye = parseFloat(montantPayeInput.value) || 0;
        const restant = fraisTransport - montantPaye;
        
        if (montantPaye > 0) {
            if (restant > 0) {
                montantRestantInfo.textContent = `Reste à payer: ${restant.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 2})} FCFA`;
                montantRestantInfo.className = 'mt-1 text-xs text-orange-600 dark:text-orange-400';
            } else if (restant == 0) {
                montantRestantInfo.textContent = 'Paiement complet';
                montantRestantInfo.className = 'mt-1 text-xs text-green-600 dark:text-green-400';
            } else {
                montantRestantInfo.textContent = 'Attention: Montant supérieur au total';
                montantRestantInfo.className = 'mt-1 text-xs text-red-600 dark:text-red-400';
            }
        } else {
            montantRestantInfo.textContent = '';
        }
    }

    paiementComplet.addEventListener('change', function() {
        if (this.checked) {
            paiementPartiel.checked = false;
        }
        togglePaiementSection();
    });

    paiementPartiel.addEventListener('change', function() {
        if (this.checked) {
            paiementComplet.checked = false;
        }
        togglePaiementSection();
    });

    fraisTransportInput.addEventListener('input', function() {
        if (paiementComplet.checked) {
            const fraisTransport = parseFormattedNumber(this.value) || 0;
            montantPayeInput.value = fraisTransport.toFixed(2);
        }
        if (paiementPartiel.checked) {
            updateMontantRestant();
        }
    });

    montantPayeInput.addEventListener('input', function() {
        if (paiementPartiel.checked) {
            updateMontantRestant();
        }
    });

    // Initialiser au chargement
    togglePaiementSection();

    // Gestion de l'aperçu des images
    const imagesUpload = document.getElementById('images_upload');
    const imagesPreview = document.getElementById('images_preview');
    const imagesCount = document.getElementById('images_count');
    const imagesCountNumber = document.getElementById('images_count_number');
    let selectedImages = [];

    function updateImagesPreview() {
        if (selectedImages.length === 0) {
            imagesPreview.classList.add('hidden');
            imagesCount.classList.add('hidden');
            imagesPreview.innerHTML = '';
        } else {
            imagesPreview.classList.remove('hidden');
            imagesCount.classList.remove('hidden');
            imagesCountNumber.textContent = selectedImages.length;
            
            imagesPreview.innerHTML = '';
            selectedImages.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Aperçu" class="w-full h-32 object-cover rounded-lg border border-gray-200 dark:border-slate-600">
                        <button type="button" onclick="removeImage(${index})" class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate" title="${file.name}">${file.name}</p>
                    `;
                    imagesPreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    window.removeImage = function(index) {
        selectedImages.splice(index, 1);
        updateFileInput();
        updateImagesPreview();
    };

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        selectedImages.forEach(file => {
            dataTransfer.items.add(file);
        });
        imagesUpload.files = dataTransfer.files;
    }

    imagesUpload.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        
        // Vérifier la taille de chaque fichier (max 4 Mo)
        const maxSize = 4 * 1024 * 1024; // 4 Mo en octets
        const validFiles = [];
        const invalidFiles = [];
        
        files.forEach(file => {
            if (file.size > maxSize) {
                invalidFiles.push(file.name);
            } else {
                validFiles.push(file);
            }
        });
        
        if (invalidFiles.length > 0) {
            alert(`Les fichiers suivants sont trop volumineux (max 4 Mo) :\n${invalidFiles.join('\n')}`);
        }
        
        // Ajouter les fichiers valides à la liste
        selectedImages = [...selectedImages, ...validFiles];
        updateFileInput();
        updateImagesPreview();
    });

    // Gestion du glisser-déposer
    const dropZone = imagesUpload.closest('label');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
        }, false);
    });

    dropZone.addEventListener('drop', function(e) {
        const files = Array.from(e.dataTransfer.files).filter(file => 
            file.type.startsWith('image/') && 
            ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(file.type)
        );
        
        if (files.length > 0) {
            const maxSize = 4 * 1024 * 1024;
            const validFiles = files.filter(file => file.size <= maxSize);
            selectedImages = [...selectedImages, ...validFiles];
            updateFileInput();
            updateImagesPreview();
        }
    }, false);
});
</script>
@endsection

