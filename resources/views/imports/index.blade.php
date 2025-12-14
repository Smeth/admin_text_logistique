@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Importer des données</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Import Clients -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Import Clients</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Importez un fichier Excel (.xlsx, .xls, .csv) contenant les clients.
            </p>
            <form action="{{ route('imports.clients') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fichier</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                    Importer
                </button>
            </form>
            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-xs font-medium text-blue-800 dark:text-blue-300 mb-2">Format attendu :</p>
                <p class="text-xs text-blue-700 dark:text-blue-400">
                    Colonnes : Nom, Prénom, Email, Téléphone, Adresse, Type, Statut, Notes
                </p>
            </div>
        </div>

        <!-- Import Colis -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Import Colis</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Importez un fichier Excel (.xlsx, .xls, .csv) contenant les colis.
            </p>
            <form action="{{ route('imports.colis') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fichier</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                    Importer
                </button>
            </form>
            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-xs font-medium text-blue-800 dark:text-blue-300 mb-2">Format attendu :</p>
                <p class="text-xs text-blue-700 dark:text-blue-400">
                    Colonnes : Numéro de suivi, Client Email, Poids, Dimensions, Statut, Date envoi, Agence départ, Agence arrivée, Frais transport, Devise
                </p>
            </div>
        </div>

        <!-- Import Configurations -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Import Configurations</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Importez un fichier JSON contenant les configurations (Devises, Tarifs, Agences, Rôles).
            </p>
            <form action="{{ route('imports.configurations') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fichier JSON</label>
                    <input type="file" name="file" accept=".json" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-slate-700 dark:text-white">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                    Importer
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

