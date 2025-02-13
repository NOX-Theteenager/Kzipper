<?php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Fonction pour zipper tout un dossier
function zipDirectory(string $sourceDir, string $zipPath): bool
{
    if (!is_dir($sourceDir)) {
        throw new Exception("Le répertoire source $sourceDir n'existe pas.");
    }

    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new Exception("Impossible de créer ou d'ouvrir le fichier ZIP : $zipPath");
    }

    $sourceDir = realpath($sourceDir); // Résoudre le chemin absolu
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourceDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($iterator as $file) {
        $filePath = $file->getPathname(); // Chemin absolu du fichier
        $relativePath = str_replace($sourceDir . DIRECTORY_SEPARATOR, '', $filePath); // Chemin relatif à la racine du zip

        // Ajouter le fichier au zip
        if (!$zip->addFile($filePath, $relativePath)) {
            throw new Exception("Impossible d'ajouter le fichier $filePath au zip.");
        }
    }

    return $zip->close();
}

// Inclure les classes nécessaires
require_once 'src/CompressorManager.php';

// Définir les chemins des répertoires d'upload et de sortie
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('OUTPUT_DIR', __DIR__ . '/output/');

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

    // Gérer l'upload des fichiers
    if (
        isset($_FILES['files']) && 
        is_array($_FILES['files']['tmp_name'])
    ) {
        $uploadedFiles = [];

        foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {
            $originalName = $_FILES['files']['name'][$index];
            $targetPath = UPLOAD_DIR . basename($originalName);

            // Déplacer les fichiers vers le répertoire d'upload
            if (!move_uploaded_file($tmpName, $targetPath)) {
                throw new Exception("Impossible de déplacer le fichier : $originalName");
            }

            $uploadedFiles[] = $targetPath;
        }

        // Vérifier qu'il n'y a qu'un seul fichier .keyce
        if (count($uploadedFiles) !== 1) {
            throw new Exception('Veuillez télécharger un seul fichier .keyce pour la décompression.');
        }

        $keyceFile = $uploadedFiles[0];
        if (pathinfo($keyceFile, PATHINFO_EXTENSION) !== 'keyce') {
            throw new Exception('Le fichier téléchargé n’est pas un fichier .keyce valide.');
        }

        // Utiliser la méthode de décompression
        if ($manager->decompressFile($keyceFile, OUTPUT_DIR)) {
            // Vérifier combien de fichiers sont dans OUTPUT_DIR
            $outputFiles = array_diff(scandir(OUTPUT_DIR), ['.', '..']);
            $outputFileCount = count($outputFiles);

            if ($outputFileCount === 1) {
                // Récupérer le chemin complet du premier élément dans OUTPUT_DIR
                $singleFilePath = OUTPUT_DIR . reset($outputFiles);
                
                // Vérifier si c'est un fichier et non un dossier
                if (is_file($singleFilePath)) {
                    $response['data'] = [
                        'path' => str_replace(__DIR__, '', $singleFilePath),
                        'name' => basename($singleFilePath),
                    ];
                    $response['success'] = true;
                    $response['message'] = 'Un seul fichier a été extrait avec succès.';
                } else {
                    // Si c'est un dossier, créer un zip
                    $zipPath = OUTPUT_DIR . 'decompressed_' . uniqid() . '.zip';
                    if (zipDirectory(OUTPUT_DIR, $zipPath)) {
                        $response['data'] = [
                            'path' => str_replace(__DIR__, '', $zipPath),
                            'name' => basename($zipPath),
                        ];
                        $response['success'] = true;
                        $response['message'] = 'Le contenu a été regroupé dans un fichier zip avec succès.';
                    } else {
                        throw new Exception('Échec de la création du fichier zip.');
                    }
                }
            } else {
                // Sinon, créer un fichier zip
                $zipPath = OUTPUT_DIR . 'decompressed_' . uniqid() . '.zip';
                if (zipDirectory(OUTPUT_DIR, $zipPath)) {
                    $response['data'] = [
                        'path' => str_replace(__DIR__, '', $zipPath),
                        'name' => basename($zipPath),
                    ];
                    $response['success'] = true;
                    $response['message'] = 'Fichiers décompressés et regroupés dans un zip avec succès.';
                } else {
                    throw new Exception('Échec de la création du fichier zip.');
                }
            }
            
        } else {
            throw new Exception('Échec de la décompression du fichier.');
        }
    } else {
        throw new Exception('Aucun fichier téléchargé.');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Envoyer la réponse en JSON
header('Content-Type: application/json');
echo json_encode($response);

?>
