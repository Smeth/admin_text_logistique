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

    /**
     * Vide toutes les tables de la base de données (TRUNCATE)
     * Cela préserve la structure actuelle des tables (colonnes, index, contraintes)
     */
    private function truncateAllTables()
    {
        try {
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            $tables = \DB::select('SHOW TABLES');
            $database = config('database.connections.mysql.database');
            $db = 'Tables_in_' . $database;
            
            foreach ($tables as $table) {
                $tableName = $table->$db;
                // Utiliser TRUNCATE au lieu de DROP pour préserver la structure
                \DB::statement("TRUNCATE TABLE `{$tableName}`");
            }
            
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return true;
        } catch (\Exception $e) {
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            throw $e;
        }
    }

    /**
     * Prépare le SQL pour la restauration en ne gardant que les INSERT
     * Ignore les CREATE TABLE pour préserver la structure actuelle des tables
     */
    private function prepareSqlForRestore($sql)
    {
        // Diviser le SQL en lignes pour mieux le traiter
        $lines = explode("\n", $sql);
        $filteredLines = [];
        $inCreateTable = false;
        $inInsert = false;
        $insertBuffer = '';
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Ignorer les commentaires
            if (empty($trimmedLine) || preg_match('/^--/', $trimmedLine) || preg_match('/^\/\*/', $trimmedLine)) {
                continue;
            }
            
            // Détecter le début d'un CREATE TABLE
            if (preg_match('/CREATE\s+TABLE\s+/i', $trimmedLine)) {
                $inCreateTable = true;
                continue;
            }
            
            // Si on est dans un CREATE TABLE, ignorer jusqu'à la fin (;)
            if ($inCreateTable) {
                if (strpos($trimmedLine, ';') !== false) {
                    $inCreateTable = false;
                }
                continue;
            }
            
            // Ignorer les DROP TABLE
            if (preg_match('/DROP\s+TABLE/i', $trimmedLine)) {
                continue;
            }
            
            // Ignorer les SET FOREIGN_KEY_CHECKS (on les gère nous-mêmes)
            if (preg_match('/SET\s+FOREIGN_KEY_CHECKS/i', $trimmedLine)) {
                continue;
            }
            
            // Détecter les INSERT (peuvent être sur plusieurs lignes)
            if (preg_match('/INSERT\s+INTO/i', $trimmedLine)) {
                $inInsert = true;
                $insertBuffer = $trimmedLine;
                // Si l'INSERT se termine sur la même ligne
                if (strpos($trimmedLine, ';') !== false) {
                    $filteredLines[] = $insertBuffer;
                    $insertBuffer = '';
                    $inInsert = false;
                }
                continue;
            }
            
            // Continuer à collecter l'INSERT si on est dedans
            if ($inInsert) {
                $insertBuffer .= ' ' . $trimmedLine;
                // Si on trouve un point-virgule, c'est la fin de l'INSERT
                if (strpos($trimmedLine, ';') !== false) {
                    $filteredLines[] = $insertBuffer;
                    $insertBuffer = '';
                    $inInsert = false;
                }
                continue;
            }
        }
        
        // Ajouter le dernier INSERT s'il n'a pas été terminé
        if (!empty($insertBuffer)) {
            $filteredLines[] = $insertBuffer;
        }
        
        return implode("\n", $filteredLines);
    }

    public function restoreFromFile($filename)
    {
        try {
            $backupPath = storage_path('app/backups/' . $filename);
            
            // Vérifier que le fichier existe
            if (!file_exists($backupPath)) {
                return redirect()->route('backups.index')
                    ->with('error', 'Fichier de sauvegarde introuvable : ' . $filename);
            }

            // Vérifier que le fichier n'est pas vide
            if (filesize($backupPath) == 0) {
                return redirect()->route('backups.index')
                    ->with('error', 'Le fichier de sauvegarde est vide.');
            }

            // Vider toutes les tables existantes avant de restaurer (préserve la structure)
            $this->truncateAllTables();

            // Lire le fichier SQL
            $sql = file_get_contents($backupPath);
            
            if ($sql === false || empty($sql)) {
                return redirect()->route('backups.index')
                    ->with('error', 'Impossible de lire le fichier de sauvegarde.');
            }

            // Préparer le SQL : ignorer les CREATE TABLE pour préserver la structure actuelle
            // On ne garde que les INSERT pour restaurer les données
            $sql = $this->prepareSqlForRestore($sql);
            
            if (empty(trim($sql))) {
                return redirect()->route('backups.index')
                    ->with('error', 'Le fichier de sauvegarde ne contient pas de données à restaurer (seulement la structure).');
            }

            // Vérifier qu'il y a bien des INSERT dans le SQL filtré
            if (!preg_match('/INSERT\s+INTO/i', $sql)) {
                return redirect()->route('backups.index')
                    ->with('error', 'Aucune donnée INSERT trouvée dans le fichier de sauvegarde après filtrage.');
            }

            // Désactiver temporairement les vérifications de clés étrangères
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Diviser le fichier SQL en requêtes individuelles
            $queries = array_filter(
                array_map('trim', explode(';', $sql)),
                function($query) {
                    $query = trim($query);
                    return !empty($query) && 
                           !preg_match('/^--/', $query) && 
                           !preg_match('/^\/\*/', $query) &&
                           preg_match('/INSERT\s+INTO/i', $query); // Ne garder que les INSERT
                }
            );

            if (empty($queries)) {
                \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                return redirect()->route('backups.index')
                    ->with('error', 'Aucune requête INSERT valide trouvée dans le fichier de sauvegarde.');
            }

            $insertedCount = 0;
            $errorCount = 0;
            $errors = [];

            try {
                foreach ($queries as $index => $query) {
                    if (!empty(trim($query))) {
                        try {
                            \DB::unprepared($query);
                            $insertedCount++;
                        } catch (\Exception $e) {
                            $errorCount++;
                            $errors[] = "Erreur à la requête " . ($index + 1) . ": " . $e->getMessage();
                            // Continuer avec les autres requêtes même en cas d'erreur
                        }
                    }
                }
                
                if ($insertedCount === 0 && $errorCount > 0) {
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    return redirect()->route('backups.index')
                        ->with('error', 'Aucune donnée n\'a pu être restaurée. Erreurs: ' . implode(' | ', array_slice($errors, 0, 5)));
                }
                
                if ($errorCount > 0) {
                    // Afficher un avertissement mais continuer
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    return redirect()->route('backups.index')
                        ->with('warning', "Restauration partielle : {$insertedCount} requêtes réussies, {$errorCount} erreurs. " . implode(' | ', array_slice($errors, 0, 3)));
                }
                
            } catch (\Exception $e) {
                \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                return redirect()->route('backups.index')
                    ->with('error', 'Erreur lors de la restauration : ' . $e->getMessage() . ' (Inséré: ' . $insertedCount . ' requêtes)');
            }

            // Réactiver les vérifications de clés étrangères
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->route('backups.index')
                ->with('success', 'Base de données restaurée avec succès depuis : ' . $filename);
        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file'
        ]);

        try {
            $file = $request->file('backup_file');
            
            // Vérifier l'extension du fichier
            $extension = $file->getClientOriginalExtension();
            if (strtolower($extension) !== 'sql') {
                return redirect()->route('backups.index')
                    ->with('error', 'Le fichier doit être un fichier SQL (.sql)');
            }

            $path = $file->storeAs('temp', 'restore_' . time() . '.sql');
            $fullPath = storage_path('app/' . $path);

            // Vérifier que le fichier existe et n'est pas vide
            if (!file_exists($fullPath) || filesize($fullPath) == 0) {
                Storage::delete($path);
                return redirect()->route('backups.index')
                    ->with('error', 'Le fichier de sauvegarde est vide ou introuvable.');
            }

            // Vider toutes les tables existantes avant de restaurer (préserve la structure)
            $this->truncateAllTables();

            // Lire le fichier SQL
            $sql = file_get_contents($fullPath);
            
            if ($sql === false || empty($sql)) {
                Storage::delete($path);
                return redirect()->route('backups.index')
                    ->with('error', 'Impossible de lire le fichier de sauvegarde.');
            }

            // Préparer le SQL : ignorer les CREATE TABLE pour préserver la structure actuelle
            // On ne garde que les INSERT pour restaurer les données
            $sql = $this->prepareSqlForRestore($sql);
            
            if (empty(trim($sql))) {
                Storage::delete($path);
                return redirect()->route('backups.index')
                    ->with('error', 'Le fichier de sauvegarde ne contient pas de données à restaurer (seulement la structure).');
            }

            // Vérifier qu'il y a bien des INSERT dans le SQL filtré
            if (!preg_match('/INSERT\s+INTO/i', $sql)) {
                Storage::delete($path);
                return redirect()->route('backups.index')
                    ->with('error', 'Aucune donnée INSERT trouvée dans le fichier de sauvegarde après filtrage.');
            }

            // Désactiver temporairement les vérifications de clés étrangères
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Diviser le fichier SQL en requêtes individuelles
            $queries = array_filter(
                array_map('trim', explode(';', $sql)),
                function($query) {
                    $query = trim($query);
                    return !empty($query) && 
                           !preg_match('/^--/', $query) && 
                           !preg_match('/^\/\*/', $query) &&
                           preg_match('/INSERT\s+INTO/i', $query); // Ne garder que les INSERT
                }
            );

            if (empty($queries)) {
                \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                Storage::delete($path);
                return redirect()->route('backups.index')
                    ->with('error', 'Aucune requête INSERT valide trouvée dans le fichier de sauvegarde.');
            }

            $insertedCount = 0;
            $errorCount = 0;
            $errors = [];

            try {
                foreach ($queries as $index => $query) {
                    if (!empty(trim($query))) {
                        try {
                            \DB::unprepared($query);
                            $insertedCount++;
                        } catch (\Exception $e) {
                            $errorCount++;
                            $errors[] = "Erreur à la requête " . ($index + 1) . ": " . $e->getMessage();
                            // Continuer avec les autres requêtes même en cas d'erreur
                        }
                    }
                }
                
                if ($insertedCount === 0 && $errorCount > 0) {
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    Storage::delete($path);
                    return redirect()->route('backups.index')
                        ->with('error', 'Aucune donnée n\'a pu être restaurée. Erreurs: ' . implode(' | ', array_slice($errors, 0, 5)));
                }
                
                if ($errorCount > 0) {
                    // Afficher un avertissement mais continuer
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    Storage::delete($path);
                    return redirect()->route('backups.index')
                        ->with('warning', "Restauration partielle : {$insertedCount} requêtes réussies, {$errorCount} erreurs. " . implode(' | ', array_slice($errors, 0, 3)));
                }
                
            } catch (\Exception $e) {
                \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                Storage::delete($path);
                return redirect()->route('backups.index')
                    ->with('error', 'Erreur lors de la restauration : ' . $e->getMessage() . ' (Inséré: ' . $insertedCount . ' requêtes)');
            }

            // Réactiver les vérifications de clés étrangères
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Supprimer le fichier temporaire
            Storage::delete($path);

            return redirect()->route('backups.index')
                ->with('success', 'Base de données restaurée avec succès');
        } catch (\Exception $e) {
            // Supprimer le fichier temporaire en cas d'erreur
            if (isset($path)) {
                Storage::delete($path);
            }
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

