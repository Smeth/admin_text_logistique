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
     * 
     * @param bool $preserveUsers Si true, préserve la table users pour éviter la déconnexion
     */
    private function truncateAllTables($preserveUsers = true)
    {
        try {
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            $tables = \DB::select('SHOW TABLES');
            $database = config('database.connections.mysql.database');
            $db = 'Tables_in_' . $database;
            
            // Tables à exclure du TRUNCATE pour préserver certaines données
            $excludedTables = [];
            
            if ($preserveUsers) {
                // Préserver la table users pour éviter la déconnexion
                // L'utilisateur actuel restera connecté
                $excludedTables[] = 'users';
                $excludedTables[] = 'sessions'; // Préserver aussi les sessions
            }
            
            foreach ($tables as $table) {
                $tableName = $table->$db;
                
                // Ignorer les tables système Laravel et les tables exclues
                if (in_array($tableName, $excludedTables)) {
                    continue;
                }
                
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
     * 
     * @param string $sql Le SQL à préparer
     * @param bool $excludeUsers Si true, exclut les INSERT INTO users pour préserver les utilisateurs actuels
     */
    private function prepareSqlForRestore($sql, $excludeUsers = true)
    {
        // Supprimer les commentaires de ligne (-- comment)
        $sql = preg_replace('/--.*$/m', '', $sql);
        
        // Supprimer les commentaires conditionnels MySQL qui ne sont pas des INSERT
        // Mais garder ceux qui sont dans les INSERT
        $sql = preg_replace('/\/\*!40014[^*]*\*+(?:[^/*][^*]*\*+)*\//', '', $sql);
        $sql = preg_replace('/\/\*!40101[^*]*\*+(?:[^/*][^*]*\*+)*\//', '', $sql);
        $sql = preg_replace('/\/\*!40111[^*]*\*+(?:[^/*][^*]*\*+)*\//', '', $sql);
        $sql = preg_replace('/\/\*!40000[^*]*\*+(?:[^/*][^*]*\*+)*\//', '', $sql);
        
        // Supprimer les commandes DROP TABLE
        $sql = preg_replace('/DROP\s+TABLE\s+IF\s+EXISTS\s+`[^`]+`\s*;/i', '', $sql);
        $sql = preg_replace('/DROP\s+TABLE\s+`[^`]+`\s*;/i', '', $sql);
        
        // Supprimer les CREATE TABLE (peuvent être sur plusieurs lignes)
        $sql = preg_replace('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`[^`]+`\s*\([^;]+\)\s*[^;]*;/is', '', $sql);
        
        // Supprimer les SET FOREIGN_KEY_CHECKS
        $sql = preg_replace('/SET\s+FOREIGN_KEY_CHECKS\s*=\s*[01]\s*;/i', '', $sql);
        $sql = preg_replace('/SET\s+@OLD_FOREIGN_KEY_CHECKS[^;]*;/i', '', $sql);
        $sql = preg_replace('/SET\s+FOREIGN_KEY_CHECKS\s*=\s*@OLD_FOREIGN_KEY_CHECKS\s*;/i', '', $sql);
        
        // Supprimer les SET SQL_MODE
        $sql = preg_replace('/SET\s+@OLD_SQL_MODE[^;]*;/i', '', $sql);
        $sql = preg_replace('/SET\s+SQL_MODE[^;]*;/i', '', $sql);
        $sql = preg_replace('/SET\s+SQL_MODE\s*=\s*@OLD_SQL_MODE\s*;/i', '', $sql);
        
        // Supprimer les SET SQL_NOTES
        $sql = preg_replace('/SET\s+@OLD_SQL_NOTES[^;]*;/i', '', $sql);
        $sql = preg_replace('/SET\s+SQL_NOTES[^;]*;/i', '', $sql);
        $sql = preg_replace('/SET\s+SQL_NOTES\s*=\s*@OLD_SQL_NOTES\s*;/i', '', $sql);
        
        // Supprimer les LOCK TABLES et UNLOCK TABLES
        $sql = preg_replace('/LOCK\s+TABLES\s+`[^`]+`\s+WRITE\s*;/i', '', $sql);
        $sql = preg_replace('/UNLOCK\s+TABLES\s*;/i', '', $sql);
        
        // Supprimer les ALTER TABLE DISABLE/ENABLE KEYS
        $sql = preg_replace('/ALTER\s+TABLE\s+`[^`]+`\s+DISABLE\s+KEYS\s*;/i', '', $sql);
        $sql = preg_replace('/ALTER\s+TABLE\s+`[^`]+`\s+ENABLE\s+KEYS\s*;/i', '', $sql);
        
        // Supprimer les SET character_set_client
        $sql = preg_replace('/SET\s+@saved_cs_client[^;]*;/i', '', $sql);
        $sql = preg_replace('/SET\s+character_set_client[^;]*;/i', '', $sql);
        $sql = preg_replace('/SET\s+character_set_client\s*=\s*@saved_cs_client\s*;/i', '', $sql);
        
        // Nettoyer les lignes vides multiples
        $sql = preg_replace('/\n\s*\n\s*\n+/', "\n\n", $sql);
        
        // Maintenant, extraire uniquement les INSERT (peuvent être sur plusieurs lignes)
        // Diviser par point-virgule pour obtenir les requêtes complètes
        $queries = explode(';', $sql);
        $insertQueries = [];
        
        foreach ($queries as $query) {
            $query = trim($query);
            
            // Ignorer les requêtes vides
            if (empty($query)) {
                continue;
            }
            
            // Garder uniquement les INSERT
            if (preg_match('/INSERT\s+INTO/i', $query)) {
                // Exclure les INSERT INTO users si demandé
                if ($excludeUsers && preg_match('/INSERT\s+INTO\s+`?users`?/i', $query)) {
                    continue;
                }
                
                // Nettoyer la requête
                $query = preg_replace('/\s+/', ' ', $query);
                $query = trim($query);
                
                if (!empty($query)) {
                    $insertQueries[] = $query;
                }
            }
        }
        
        // Retourner les INSERT séparés par des points-virgules
        return implode(";\n", $insertQueries) . (count($insertQueries) > 0 ? ';' : '');
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
            // Préserver les utilisateurs pour éviter la déconnexion
            $this->truncateAllTables(true);

            // Lire le fichier SQL
            $sql = file_get_contents($backupPath);
            
            if ($sql === false || empty($sql)) {
                return redirect()->route('backups.index')
                    ->with('error', 'Impossible de lire le fichier de sauvegarde.');
            }

            // Préparer le SQL : ignorer les CREATE TABLE pour préserver la structure actuelle
            // On ne garde que les INSERT pour restaurer les données
            // Exclure les INSERT INTO users pour préserver les utilisateurs actuels
            $originalSqlLength = strlen($sql);
            $sql = $this->prepareSqlForRestore($sql, true);
            $filteredSqlLength = strlen($sql);
            
            if (empty(trim($sql))) {
                return redirect()->route('backups.index')
                    ->with('error', 'Le fichier de sauvegarde ne contient pas de données à restaurer (seulement la structure). SQL original : ' . $originalSqlLength . ' caractères, après filtrage : ' . $filteredSqlLength . ' caractères.');
            }

            // Vérifier qu'il y a bien des INSERT dans le SQL filtré
            $insertCount = preg_match_all('/INSERT\s+INTO/i', $sql);
            if ($insertCount === 0) {
                return redirect()->route('backups.index')
                    ->with('error', 'Aucune donnée INSERT trouvée dans le fichier de sauvegarde après filtrage. SQL après filtrage : ' . substr($sql, 0, 500) . '...');
            }

            // Désactiver temporairement les vérifications de clés étrangères
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Diviser le fichier SQL en requêtes individuelles
            // Les INSERT sont déjà séparés par des ; dans prepareSqlForRestore()
            $queries = array_filter(
                array_map('trim', explode(';', $sql)),
                function($query) {
                    $query = trim($query);
                    return !empty($query) && 
                           preg_match('/INSERT\s+INTO/i', $query); // Ne garder que les INSERT
                }
            );

            if (empty($queries)) {
                \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                return redirect()->route('backups.index')
                    ->with('error', 'Aucune requête INSERT valide trouvée après division. Nombre d\'INSERT détectés : ' . $insertCount . '. SQL filtré (500 premiers caractères) : ' . substr($sql, 0, 500));
            }

            $insertedCount = 0;
            $errorCount = 0;
            $errors = [];
            $totalQueries = count($queries);

            try {
                foreach ($queries as $index => $query) {
                    $query = trim($query);
                    if (empty($query)) {
                        continue;
                    }
                    
                    // Ajouter le point-virgule si nécessaire
                    if (substr($query, -1) !== ';') {
                        $query .= ';';
                    }
                    
                    try {
                        \DB::unprepared($query);
                        $insertedCount++;
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errorMsg = $e->getMessage();
                        // Limiter la longueur du message d'erreur
                        if (strlen($errorMsg) > 200) {
                            $errorMsg = substr($errorMsg, 0, 200) . '...';
                        }
                        $errors[] = "Requête " . ($index + 1) . "/{$totalQueries}: " . $errorMsg;
                        // Afficher les 100 premiers caractères de la requête en erreur pour déboguer
                        if (count($errors) <= 3) {
                            $errors[count($errors) - 1] .= " (SQL: " . substr($query, 0, 100) . "...)";
                        }
                        // Continuer avec les autres requêtes même en cas d'erreur
                    }
                }
                
                if ($insertedCount === 0 && $errorCount > 0) {
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    return redirect()->route('backups.index')
                        ->with('error', 'Aucune donnée n\'a pu être restaurée. ' . $errorCount . ' erreur(s) sur ' . $totalQueries . ' requête(s). Premières erreurs: ' . implode(' | ', array_slice($errors, 0, 3)));
                }
                
                if ($errorCount > 0) {
                    // Afficher un avertissement mais continuer
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    return redirect()->route('backups.index')
                        ->with('warning', "Restauration partielle : {$insertedCount} requête(s) réussie(s) sur {$totalQueries}, {$errorCount} erreur(s). " . implode(' | ', array_slice($errors, 0, 3)));
                }
                
            } catch (\Exception $e) {
                \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                return redirect()->route('backups.index')
                    ->with('error', 'Erreur lors de la restauration : ' . $e->getMessage() . ' (Inséré: ' . $insertedCount . ' requête(s) sur ' . $totalQueries . ')');
            }

            // Réactiver les vérifications de clés étrangères
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->route('backups.index')
                ->with('success', "Base de données restaurée avec succès depuis : {$filename} ({$insertedCount} requête(s) exécutée(s)). Note : Les utilisateurs actuels ont été préservés pour éviter la déconnexion.");
        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:102400' // Max 100MB
        ]);

        try {
            $file = $request->file('backup_file');
            
            // Vérifier que le fichier a bien été uploadé
            if (!$file || !$file->isValid()) {
                return redirect()->route('backups.index')
                    ->with('error', 'Erreur lors de l\'upload du fichier. Veuillez réessayer.');
            }
            
            // Vérifier la taille du fichier uploadé
            if ($file->getSize() == 0) {
                return redirect()->route('backups.index')
                    ->with('error', 'Le fichier uploadé est vide. Veuillez sélectionner un fichier valide.');
            }
            
            // Vérifier l'extension du fichier
            $extension = $file->getClientOriginalExtension();
            if (strtolower($extension) !== 'sql') {
                return redirect()->route('backups.index')
                    ->with('error', 'Le fichier doit être un fichier SQL (.sql). Extension reçue : ' . $extension);
            }

            // Créer le dossier temp s'il n'existe pas avec les bonnes permissions
            $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
            if (!file_exists($tempDir)) {
                if (!mkdir($tempDir, 0755, true)) {
                    return redirect()->route('backups.index')
                        ->with('error', 'Impossible de créer le dossier storage/app/temp. Vérifiez les permissions.');
                }
            }
            
            // Vérifier que le dossier est accessible en écriture
            if (!is_writable($tempDir)) {
                return redirect()->route('backups.index')
                    ->with('error', 'Le dossier storage/app/temp n\'est pas accessible en écriture. Vérifiez les permissions.');
            }

            // Stocker le fichier avec gestion d'erreur améliorée
            try {
                $path = $file->storeAs('temp', 'restore_' . time() . '_' . uniqid() . '.sql');
            } catch (\Exception $e) {
                return redirect()->route('backups.index')
                    ->with('error', 'Erreur lors de l\'enregistrement du fichier : ' . $e->getMessage());
            }

            if (!$path) {
                return redirect()->route('backups.index')
                    ->with('error', 'Erreur : le fichier n\'a pas pu être enregistré. Vérifiez les permissions du dossier storage/app/temp.');
            }

            // Utiliser Storage::path() pour obtenir le chemin complet normalisé
            try {
                $fullPath = Storage::path($path);
            } catch (\Exception $e) {
                // Fallback si Storage::path() ne fonctionne pas
                $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path));
            }

            // Normaliser le chemin pour Windows
            $fullPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fullPath);
            $fullPath = realpath(dirname($fullPath)) . DIRECTORY_SEPARATOR . basename($fullPath);

            // Attendre un peu pour s'assurer que le fichier est complètement écrit
            $attempts = 0;
            $maxAttempts = 10;
            while (!file_exists($fullPath) && $attempts < $maxAttempts) {
                usleep(100000); // Attendre 100ms
                $attempts++;
            }

            // Vérifier que le fichier existe après stockage
            if (!file_exists($fullPath)) {
                // Essayer de trouver le fichier avec un autre chemin
                $alternatePath = storage_path('app' . DIRECTORY_SEPARATOR . $path);
                $alternatePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $alternatePath);
                
                if (file_exists($alternatePath)) {
                    $fullPath = $alternatePath;
                } else {
                    return redirect()->route('backups.index')
                        ->with('error', 'Erreur : le fichier n\'a pas pu être enregistré sur le serveur. Chemin attendu : ' . $fullPath . '. Vérifiez les permissions du dossier storage/app/temp.');
                }
            }

            // Vérifier que le fichier n'est pas vide après stockage
            $fileSize = filesize($fullPath);
            if ($fileSize === false) {
                Storage::delete($path);
                return redirect()->route('backups.index')
                    ->with('error', 'Erreur : impossible de lire la taille du fichier. Vérifiez les permissions.');
            }

            if ($fileSize == 0) {
                Storage::delete($path);
                return redirect()->route('backups.index')
                    ->with('error', 'Le fichier de sauvegarde est vide après l\'upload (0 octets). Veuillez vérifier que le fichier source n\'est pas vide.');
            }

            // Vérifier que le fichier contient du texte SQL valide
            $firstBytes = file_get_contents($fullPath, false, null, 0, 100);
            if ($firstBytes === false || empty(trim($firstBytes))) {
                Storage::delete($path);
                return redirect()->route('backups.index')
                    ->with('error', 'Le fichier semble être vide ou corrompu. Taille : ' . $fileSize . ' octets.');
            }

            // Vider toutes les tables existantes avant de restaurer (préserve la structure)
            // Préserver les utilisateurs pour éviter la déconnexion
            $this->truncateAllTables(true);

            // Lire le fichier SQL
            $sql = file_get_contents($fullPath);
            
            if ($sql === false || empty($sql)) {
                Storage::delete($path);
                return redirect()->route('backups.index')
                    ->with('error', 'Impossible de lire le fichier de sauvegarde.');
            }

            // Préparer le SQL : ignorer les CREATE TABLE pour préserver la structure actuelle
            // On ne garde que les INSERT pour restaurer les données
            // Exclure les INSERT INTO users pour préserver les utilisateurs actuels
            $originalSqlLength = strlen($sql);
            $sql = $this->prepareSqlForRestore($sql, true);
            $filteredSqlLength = strlen($sql);
            
            if (empty(trim($sql))) {
                Storage::delete($path);
                return redirect()->route('backups.index')
                    ->with('error', 'Le fichier de sauvegarde ne contient pas de données à restaurer (seulement la structure). SQL original : ' . $originalSqlLength . ' caractères, après filtrage : ' . $filteredSqlLength . ' caractères.');
            }

            // Vérifier qu'il y a bien des INSERT dans le SQL filtré
            $insertCount = preg_match_all('/INSERT\s+INTO/i', $sql);
            if ($insertCount === 0) {
                Storage::delete($path);
                return redirect()->route('backups.index')
                    ->with('error', 'Aucune donnée INSERT trouvée dans le fichier de sauvegarde après filtrage. SQL après filtrage : ' . substr($sql, 0, 500) . '...');
            }

            // Désactiver temporairement les vérifications de clés étrangères
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Diviser le fichier SQL en requêtes individuelles
            // Les INSERT sont déjà séparés par des ; dans prepareSqlForRestore()
            $queries = array_filter(
                array_map('trim', explode(';', $sql)),
                function($query) {
                    $query = trim($query);
                    return !empty($query) && 
                           preg_match('/INSERT\s+INTO/i', $query); // Ne garder que les INSERT
                }
            );

            if (empty($queries)) {
                \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                Storage::delete($path);
                return redirect()->route('backups.index')
                    ->with('error', 'Aucune requête INSERT valide trouvée après division. Nombre d\'INSERT détectés : ' . $insertCount . '. SQL filtré (500 premiers caractères) : ' . substr($sql, 0, 500));
            }

            $insertedCount = 0;
            $errorCount = 0;
            $errors = [];
            $totalQueries = count($queries);

            try {
                foreach ($queries as $index => $query) {
                    $query = trim($query);
                    if (empty($query)) {
                        continue;
                    }
                    
                    // Ajouter le point-virgule si nécessaire
                    if (substr($query, -1) !== ';') {
                        $query .= ';';
                    }
                    
                    try {
                        \DB::unprepared($query);
                        $insertedCount++;
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errorMsg = $e->getMessage();
                        // Limiter la longueur du message d'erreur
                        if (strlen($errorMsg) > 200) {
                            $errorMsg = substr($errorMsg, 0, 200) . '...';
                        }
                        $errors[] = "Requête " . ($index + 1) . "/{$totalQueries}: " . $errorMsg;
                        // Afficher les 100 premiers caractères de la requête en erreur pour déboguer
                        if (count($errors) <= 3) {
                            $errors[count($errors) - 1] .= " (SQL: " . substr($query, 0, 100) . "...)";
                        }
                        // Continuer avec les autres requêtes même en cas d'erreur
                    }
                }
                
                if ($insertedCount === 0 && $errorCount > 0) {
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    Storage::delete($path);
                    return redirect()->route('backups.index')
                        ->with('error', 'Aucune donnée n\'a pu être restaurée. ' . $errorCount . ' erreur(s) sur ' . $totalQueries . ' requête(s). Premières erreurs: ' . implode(' | ', array_slice($errors, 0, 3)));
                }
                
                if ($errorCount > 0) {
                    // Afficher un avertissement mais continuer
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    Storage::delete($path);
                    return redirect()->route('backups.index')
                        ->with('warning', "Restauration partielle : {$insertedCount} requête(s) réussie(s) sur {$totalQueries}, {$errorCount} erreur(s). " . implode(' | ', array_slice($errors, 0, 3)));
                }
                
            } catch (\Exception $e) {
                \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                Storage::delete($path);
                return redirect()->route('backups.index')
                    ->with('error', 'Erreur lors de la restauration : ' . $e->getMessage() . ' (Inséré: ' . $insertedCount . ' requête(s) sur ' . $totalQueries . ')');
            }

            // Réactiver les vérifications de clés étrangères
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Supprimer le fichier temporaire
            Storage::delete($path);

            return redirect()->route('backups.index')
                ->with('success', "Base de données restaurée avec succès : {$insertedCount} requête(s) exécutée(s). Note : Les utilisateurs actuels ont été préservés pour éviter la déconnexion.");
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

