@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Exporter des données</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Export Clients -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Export Clients</h2>
            <form action="{{ route('exports.clients') }}" method="GET" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut</label>
                    <select name="statut" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Tous</option>
                        <option value="actif">Actif</option>
                        <option value="inactif">Inactif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date de début</label>
                    <input type="date" name="date_from" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date de fin</label>
                    <input type="date" name="date_to" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                    Exporter en Excel
                </button>
            </form>
        </div>

        <!-- Export Colis -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Export Colis</h2>
            <form action="{{ route('exports.colis') }}" method="GET" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut</label>
                    <select name="statut" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Tous</option>
                        <option value="emballe">Colis emballé</option>
                        <option value="expedie_port">Expédié</option>
                        <option value="livre">Livré</option>
                        <option value="retourne">Retourné</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date d'envoi (début)</label>
                    <input type="date" name="date_from" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date d'envoi (fin)</label>
                    <input type="date" name="date_to" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                    Exporter en Excel
                </button>
            </form>
        </div>

        <!-- Export Transactions -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Export Transactions</h2>
            <form action="{{ route('exports.transactions') }}" method="GET" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                    <select name="type" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Tous</option>
                        <option value="entree">Entrée</option>
                        <option value="sortie">Sortie</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date (début)</label>
                    <input type="date" name="date_from" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date (fin)</label>
                    <input type="date" name="date_to" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                    Exporter en Excel
                </button>
            </form>
        </div>

        <!-- Export Configurations -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Export Configurations</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Exporte les configurations (Devises, Tarifs, Agences, Rôles) au format JSON.
            </p>
            <a href="{{ route('exports.configurations') }}" class="block w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-center">
                Exporter en JSON
            </a>
        </div>
    </div>
</div>
@endsection

