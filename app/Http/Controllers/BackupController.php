<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        $backups = $this->getBackupFiles();
        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_His') . '.sql';
            $backupDir = storage_path('app/backups');
            
            // Créer le dossier si nécessaire
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $path = $backupDir . '/' . $filename;

            // Exécuter mysqldump
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            // Commande mysqldump (Windows)
            $command = "mysqldump -h {$host} -u {$username} -p{$password} {$database} > \"{$path}\" 2>&1";
            
            // Pour Linux/Mac, utiliser cette version :
            // $command = "mysqldump -h {$host} -u {$username} -p{$password} {$database} > {$path} 2>&1";
            
            exec($command, $output, $returnVar);

            if ($returnVar === 0 && file_exists($path) && filesize($path) > 0) {
                return redirect()->route('backups.index')
                    ->with('success', 'Sauvegarde créée avec succès : ' . $filename);
            } else {
                // Essayer une autre méthode si mysqldump n'est pas disponible
                return $this->createBackupAlternative($filename);
            }
        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    private function createBackupAlternative($filename)
    {
        // Méthode alternative : exporter via Laravel DB
        try {
            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $path = $backupDir . '/' . $filename;
            $tables = \DB::select('SHOW TABLES');
            $database = config('database.connections.mysql.database');
            $db = 'Tables_in_' . $database;

            $output = "-- Backup généré le " . date('Y-m-d H:i:s') . "\n";
            $output .= "-- Base de données: {$database}\n\n";
            $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                $tableName = $table->$db;
                $output .= "-- Structure de la table `{$tableName}`\n";
                $output .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                
                $createTable = \DB::select("SHOW CREATE TABLE `{$tableName}`");
                $output .= $createTable[0]->{'Create Table'} . ";\n\n";
                
                $output .= "-- Données de la table `{$tableName}`\n";
                $rows = \DB::table($tableName)->get();
                
                if ($rows->count() > 0) {
                    $output .= "INSERT INTO `{$tableName}` VALUES ";
                    $values = [];
                    foreach ($rows as $row) {
                        $rowValues = [];
                        foreach ((array)$row as $value) {
                            if ($value === null) {
                                $rowValues[] = 'NULL';
                            } else {
                                $rowValues[] = "'" . addslashes($value) . "'";
                            }
                        }
                        $values[] = '(' . implode(',', $rowValues) . ')';
                    }
                    $output .= implode(',', $values) . ";\n\n";
                }
            }

            $output .= "SET FOREIGN_KEY_CHECKS=1;\n";

            file_put_contents($path, $output);

            return redirect()->route('backups.index')
                ->with('success', 'Sauvegarde créée avec succès : ' . $filename);
        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'Erreur lors de la création de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        
        if (file_exists($path)) {
            return response()->download($path);
        }
        
        return redirect()->route('backups.index')
            ->with('error', 'Fichier de sauvegarde introuvable');
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql'
        ]);

        try {
            $file = $request->file('backup_file');
            $path = $file->storeAs('temp', 'restore_' . time() . '.sql');
            $fullPath = storage_path('app/' . $path);

            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            $command = "mysql -h {$host} -u {$username} -p{$password} {$database} < \"{$fullPath}\" 2>&1";
            exec($command, $output, $returnVar);

            // Supprimer le fichier temporaire
            Storage::delete($path);

            if ($returnVar === 0) {
                return redirect()->route('backups.index')
                    ->with('success', 'Base de données restaurée avec succès');
            } else {
                return redirect()->route('backups.index')
                    ->with('error', 'Erreur lors de la restauration. Vérifiez que MySQL est accessible.');
            }
        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function destroy($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        
        if (file_exists($path)) {
            unlink($path);
            return redirect()->route('backups.index')
                ->with('success', 'Sauvegarde supprimée');
        }
        
        return redirect()->route('backups.index')
            ->with('error', 'Fichier introuvable');
    }

    private function getBackupFiles()
    {
        $backups = [];
        $backupDir = storage_path('app/backups');
        
        if (file_exists($backupDir)) {
            $files = glob($backupDir . '/*.sql');
            foreach ($files as $file) {
                $backups[] = [
                    'filename' => basename($file),
                    'size' => filesize($file),
                    'size_formatted' => $this->formatBytes(filesize($file)),
                    'created_at' => Carbon::createFromTimestamp(filemtime($file))
                ];
            }
            // Trier par date décroissante
            usort($backups, function($a, $b) {
                return $b['created_at']->timestamp - $a['created_at']->timestamp;
            });
        }
        
        return $backups;
    }

    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}

