<?php
//checkpoint
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Inclure les classes nécessaires
require_once 'src/CompressorManager.php';

// Définir les chemins des répertoires d'upload et de sortie
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('OUTPUT_DIR', __DIR__ . '/output/');

// Limites des fichiers (en octets)
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 10 Mo par fichier
define('MAX_TOTAL_SIZE', 100 * 1024 * 1024); // Taille totale maximale (50 Mo)
define('MAX_FILE_COUNT', 200); // Nombre maximal de fichiers

// Fonction pour nettoyer un dossier
function cleanDirectory(string $directory): void
{
    $files = array_diff(scandir($directory), ['.', '..']);
    foreach ($files as $file) {
        $filePath = $directory . DIRECTORY_SEPARATOR . $file;
        if (is_dir($filePath)) {
            cleanDirectory($filePath);
            rmdir($filePath);
        } else {
            unlink($filePath);
        }
    }
}

// Créer les dossiers nécessaires s'ils n'existent pas
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}else{
    cleanDirectory(UPLOAD_DIR);
}
if (!is_dir(OUTPUT_DIR)) {
    mkdir(OUTPUT_DIR, 0777, true);
} else {
    cleanDirectory(OUTPUT_DIR); // Nettoyer le dossier output avant d'écrire dedans
}

// Initialiser la réponse
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    // Vérifier si une requête POST est reçue
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée.');
    }

    $manager = new CompressorManager();

    // Vérifier les fichiers et chemins relatifs
    if (
        isset($_FILES['files']) &&
        is_array($_FILES['files']['tmp_name']) &&
        isset($_POST['paths']) &&
        is_array($_POST['paths'])
    ) {
        // Vérifier le nombre de fichiers
        if (count($_FILES['files']['tmp_name']) > MAX_FILE_COUNT) {
            throw new Exception('Le nombre de fichiers dépasse la limite autorisée de ' . MAX_FILE_COUNT);
        }

        $uploadedFiles = [];
        $totalSize = 0;
        $relativePaths = $_POST['paths'];

        foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {
            $fileSize = $_FILES['files']['size'][$index];

            // Vérifier la taille de chaque fichier
            if ($fileSize > MAX_FILE_SIZE) {
                throw new Exception("Le fichier {$relativePaths[$index]} dépasse la taille maximale autorisée de " . MAX_FILE_SIZE . " octets.");
            }

            // Calculer la taille totale
            $totalSize += $fileSize;
            if ($totalSize > MAX_TOTAL_SIZE) {
                throw new Exception("La taille totale des fichiers dépasse la limite autorisée de " . MAX_TOTAL_SIZE . " octets.");
            }

            $relativePath = $relativePaths[$index];
            $targetPath = UPLOAD_DIR . $relativePath;

            // Créer les dossiers nécessaires pour conserver la structure
            $targetDir = dirname($targetPath);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            if (!move_uploaded_file($tmpName, $targetPath)) {
                throw new Exception("Impossible de déplacer le fichier : $relativePath");
            }

            $uploadedFiles[] = $targetPath; // Ajouter le chemin complet au tableau
        }

        // Déterminer le nom de l'archive
        if (count($uploadedFiles) === 1) {
            $baseName = pathinfo($relativePaths[0], PATHINFO_FILENAME); // Nom sans extension
            if (empty($baseName)) {
                $baseName = 'archive_single_file';
            }
            $outputFile = OUTPUT_DIR . $baseName . '.keyce';
        } else {
            $outputFile = OUTPUT_DIR . 'archive_' . uniqid() . '.keyce';
        }
        
        // Opération de compression
        if ($manager->compressFiles($uploadedFiles, $outputFile, UPLOAD_DIR)) { // Passer $uploadedFiles ici
            $response['success'] = true;
            $response['message'] = 'Fichiers compressés avec succès.';
            $response['data'] = [
                'path' => '/output/' . basename($outputFile),
                'name' => basename($outputFile),
            ];
        } else {
            throw new Exception('Échec de la compression des fichiers.');
        }
    } else {
        throw new Exception('Aucun fichier ou chemin relatif fourni.');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Envoyer la réponse en JSON
header('Content-Type: application/json');
echo json_encode($response);
