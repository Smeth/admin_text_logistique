<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ClientsImport;
use App\Imports\ColisImport;
use App\Models\Devise;
use App\Models\Tarif;
use App\Models\Agence;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function index()
    {
        return view('imports.index');
    }

    public function importClients(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new ClientsImport, $request->file('file'));
            
            return redirect()->route('imports.index')
                ->with('success', 'Clients importés avec succès');
        } catch (\Exception $e) {
            return redirect()->route('imports.index')
                ->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    public function importColis(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new ColisImport, $request->file('file'));
            
            return redirect()->route('imports.index')
                ->with('success', 'Colis importés avec succès');
        } catch (\Exception $e) {
            return redirect()->route('imports.index')
                ->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    public function importConfigurations(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json'
        ]);

        try {
            $content = file_get_contents($request->file('file')->getRealPath());
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Fichier JSON invalide : ' . json_last_error_msg());
            }

            DB::beginTransaction();

            // Importer les devises
            if (isset($data['devises']) && is_array($data['devises'])) {
                foreach ($data['devises'] as $deviseData) {
                    Devise::updateOrCreate(
                        ['code' => $deviseData['code']],
                        $deviseData
                    );
                }
            }

            // Importer les tarifs
            if (isset($data['tarifs']) && is_array($data['tarifs'])) {
                foreach ($data['tarifs'] as $tarifData) {
                    Tarif::updateOrCreate(
                        ['nom' => $tarifData['nom']],
                        $tarifData
                    );
                }
            }

            // Importer les agences
            if (isset($data['agences']) && is_array($data['agences'])) {
                foreach ($data['agences'] as $agenceData) {
                    Agence::updateOrCreate(
                        ['code_agence' => $agenceData['code_agence']],
                        $agenceData
                    );
                }
            }

            DB::commit();

            return redirect()->route('imports.index')
                ->with('success', 'Configurations importées avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('imports.index')
                ->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }
}
