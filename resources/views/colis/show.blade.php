@extends('layouts.app')

@section('title', 'Détails Colis')
@section('page-title', 'Détails du Colis')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Colis #{{ $coli->numero_suivi }}</h3>
                <span class="inline-block mt-2 px-3 py-1 text-sm font-medium rounded-full 
                    @if($coli->statut === 'livre') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                    @elseif(in_array($coli->statut, ['expedie_port', 'arrive_aeroport_depart', 'en_vol', 'arrive_aeroport_transit', 'arrive_aeroport_destination', 'en_dedouanement', 'arrive_entrepot'])) bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                    @elseif($coli->statut === 'emballe') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                    @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                    @endif">
                    @php
                        $statutLabels = [
                            'emballe' => 'Colis emballé',
                            'expedie_port' => 'Expédié vers port/aéroport',
                            'arrive_aeroport_depart' => 'Arrivé à l\'aéroport de départ',
                            'en_vol' => 'En vol vers destination',
                            'arrive_aeroport_transit' => 'Arrivé à l\'aéroport de transit',
                            'arrive_aeroport_destination' => 'Arrivé à l\'aéroport de destination',
                            'en_dedouanement' => 'En cours de dédouanement',
                            'arrive_entrepot' => 'Arrivé à l\'entrepôt de destination',
                            'livre' => 'Livré',
                            'retourne' => 'Retourné'
                        ];
                    @endphp
                    {{ $statutLabels[$coli->statut] ?? ucfirst(str_replace('_', ' ', $coli->statut)) }}
                </span>
            </div>
            <div class="flex space-x-2">
                @can('admin')
                <a href="{{ route('colis.edit', $coli) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Modifier</a>
                @endcan
                <a href="{{ route('colis.facture', $coli) }}" target="_blank" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Facture</a>
                @if($coli->paiements->count() > 0)
                <a href="{{ route('colis.recu', $coli) }}" target="_blank" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">Reçu</a>
                @endif
                <a href="{{ route('colis.index') }}" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Retour</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Client</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->client->full_name }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Poids</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->poids }} kg</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Agence Départ</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->agenceDepart->nom_agence }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Agence Arrivée</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->agenceArrivee->nom_agence }}</p></div>
            @if($coli->pays_origine || $coli->ville_origine)
            <div class="md:col-span-2">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Lieu d'origine</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">
                    @if($coli->ville_origine && $coli->pays_origine)
                        {{ $coli->ville_origine }}, {{ $coli->pays_origine }}
                    @elseif($coli->ville_origine)
                        {{ $coli->ville_origine }}
                    @elseif($coli->pays_origine)
                        {{ $coli->pays_origine }}
                    @endif
                </p>
            </div>
            @endif
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Transporteur</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->transporteur ? $coli->transporteur->nom_entreprise : 'Non assigné' }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Devise</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->devise ? $coli->devise->nom . ' (' . $coli->devise->symbole . ')' : 'Non définie' }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tarif</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->tarif ? $coli->tarif->nom_tarif : 'Aucun' }}</p></div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Frais Transport</p>
                <p class="text-lg text-gray-900 dark:text-white mt-1">
                    {{ number_format($coli->frais_transport, 0, ',', ' ') }} {{ $coli->devise ? $coli->devise->symbole : 'FCFA' }}
                    @if($coli->frais_calcule && $coli->frais_calcule != $coli->frais_transport)
                        <span class="text-xs text-gray-500 dark:text-gray-400">(Calculé: {{ number_format($coli->frais_calcule, 0, ',', ' ') }})</span>
                    @endif
                </p>
            </div>
            <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date Envoi</p><p class="text-lg text-gray-900 dark:text-white mt-1">{{ $coli->date_envoi->format('d/m/Y') }}</p></div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Statut Paiement</p>
                <div class="mt-1">
                    @if($coli->statut_paiement === 'paye')
                        <span class="inline-block px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Payé</span>
                    @elseif($coli->statut_paiement === 'partiel')
                        <span class="inline-block px-3 py-1 text-sm font-medium rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">Partiellement payé</span>
                    @else
                        <span class="inline-block px-3 py-1 text-sm font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Non payé</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informations de paiement -->
        <div class="mt-6 border-t border-gray-200 dark:border-slate-700 pt-6">
            <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Informations de Paiement</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Montant Total</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($coli->frais_transport, 0, ',', ' ') }} {{ $coli->devise ? $coli->devise->symbole : 'FCFA' }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Payé</p>
                    <p class="text-xl font-bold text-green-600 dark:text-green-400 mt-1">{{ number_format($coli->total_paye, 0, ',', ' ') }} {{ $coli->devise ? $coli->devise->symbole : 'FCFA' }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Reste à Payer</p>
                    <p class="text-xl font-bold {{ $coli->montant_restant > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }} mt-1">
                        {{ number_format($coli->montant_restant, 0, ',', ' ') }} {{ $coli->devise ? $coli->devise->symbole : 'FCFA' }}
                    </p>
                </div>
            </div>
        </div>

        @if($coli->description_contenu)
        <div class="mt-6"><p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</p><p class="text-gray-900 dark:text-white">{{ $coli->description_contenu }}</p></div>
        @endif

        @if($coli->images->count() > 0)
        <div class="mt-6 border-t border-gray-200 dark:border-slate-700 pt-6">
            <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Images du colis</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($coli->images as $image)
                    <a href="{{ $image->url }}" target="_blank"
                       class="block rounded-lg overflow-hidden border border-gray-200 dark:border-slate-700 hover:ring-2 hover:ring-indigo-500">
                        <img src="{{ $image->url }}" alt="{{ $image->original_name }}"
                             class="w-full h-32 object-cover">
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Historique des paiements -->
    @if($coli->paiements->count() > 0)
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
        <div class="p-6 border-b border-gray-200 dark:border-slate-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Historique des Paiements</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Montant</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Mode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Caisse</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Utilisateur</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                        @foreach($coli->paiements as $paiement)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $paiement->date_paiement->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($paiement->montant, 0, ',', ' ') }} {{ $paiement->devise ? $paiement->devise->symbole : 'FCFA' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $paiement->caisse ? $paiement->caisse->nom_caisse : '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $paiement->user->name }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Historique de suivi -->
    @if($coli->historique->count() > 0)
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700">
        <div class="p-6 border-b border-gray-200 dark:border-slate-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Historique de Suivi</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($coli->historique->sortByDesc('created_at') as $historique)
                    <div class="flex items-start space-x-4 pb-4 border-b border-gray-200 dark:border-slate-700 last:border-0">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    @php
                                        $statutLabels = [
                                            'emballe' => 'Colis emballé',
                                            'expedie_port' => 'Expédié vers port/aéroport',
                                            'arrive_aeroport_depart' => 'Arrivé à l\'aéroport de départ',
                                            'en_vol' => 'En vol vers destination',
                                            'arrive_aeroport_transit' => 'Arrivé à l\'aéroport de transit',
                                            'arrive_aeroport_destination' => 'Arrivé à l\'aéroport de destination',
                                            'en_dedouanement' => 'En cours de dédouanement',
                                            'arrive_entrepot' => 'Arrivé à l\'entrepôt de destination',
                                            'livre' => 'Livré',
                                            'retourne' => 'Retourné'
                                        ];
                                    @endphp
                                    @if($historique->statut_avant)
                                        Statut changé de <span class="text-orange-600 dark:text-orange-400">{{ $statutLabels[$historique->statut_avant] ?? ucfirst(str_replace('_', ' ', $historique->statut_avant)) }}</span> 
                                        vers <span class="text-green-600 dark:text-green-400">{{ $statutLabels[$historique->statut_apres] ?? ucfirst(str_replace('_', ' ', $historique->statut_apres)) }}</span>
                                    @else
                                        Colis créé avec le statut <span class="text-green-600 dark:text-green-400">{{ $statutLabels[$historique->statut_apres] ?? ucfirst(str_replace('_', ' ', $historique->statut_apres)) }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $historique->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            @if($historique->localisation)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    <span class="font-medium">Localisation:</span> {{ $historique->localisation }}
                                </p>
                            @endif
                            @if($historique->commentaire)
                                <div class="mt-2 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description:</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $historique->commentaire }}</p>
                                </div>
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Par: {{ $historique->user->name }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal pour ajouter une étape -->
@can('admin')
<div id="addStepModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-slate-800">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ajouter une étape de suivi</h3>
                <button onclick="hideAddStepModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form action="{{ route('colis.add-step', $coli) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Statut <span class="text-red-500">*</span>
                    </label>
                    <select name="statut_etape" required class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="emballe">Colis emballé</option>
                        <option value="expedie_port">Expédié vers le port/aéroport</option>
                        <option value="arrive_aeroport_depart">Arrivé à l'aéroport de départ</option>
                        <option value="en_vol">En vol vers destination</option>
                        <option value="arrive_aeroport_transit">Arrivé à l'aéroport de transit</option>
                        <option value="arrive_aeroport_destination">Arrivé à l'aéroport de destination</option>
                        <option value="en_dedouanement">En cours de dédouanement</option>
                        <option value="arrive_entrepot">Arrivé à l'entrepôt de destination</option>
                        <option value="livre">Livré</option>
                        <option value="retourne">Retourné</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description_etape" required rows="4" 
                              placeholder="Décrivez cette étape en détail..."
                              class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Localisation
                    </label>
                    <input type="text" name="localisation_etape" 
                           placeholder="Ex: Aéroport Yaoundé, Entrepôt Douala..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="hideAddStepModal()" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showAddStepModal() {
    document.getElementById('addStepModal').classList.remove('hidden');
}

function hideAddStepModal() {
    document.getElementById('addStepModal').classList.add('hidden');
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('addStepModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideAddStepModal();
    }
});
</script>
@endcan
@endsection

