<?php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);


// Inclure les classes nécessaires
require_once 'CompressorManager.php';

// Définir le chemin d'upload temporaire
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('OUTPUT_DIR', __DIR__ . '/output/');

// Créer les dossiers nécessaires s'ils n'existent pas
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}
if (!is_dir(OUTPUT_DIR)) {
    mkdir(OUTPUT_DIR, 0777, true);
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

    // Vérifier si l'opération est spécifiée
    if (!isset($_POST['operation']) || !in_array($_POST['operation'], ['compress', 'decompress'])) {
        throw new Exception('Opération non valide ou non spécifiée.');
    }

    $operation = $_POST['operation'];
    $manager = new CompressorManager();

    // Gérer l'upload des fichiers
    if (isset($_FILES['files']) && is_array($_FILES['files']['tmp_name'])) {
        $uploadedFiles = [];
        foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {
            $originalName = $_FILES['files']['name'][$index];
            $targetPath = UPLOAD_DIR . basename($originalName);

            if (!move_uploaded_file($tmpName, $targetPath)) {
                throw new Exception("Impossible de déplacer le fichier : $originalName");
            }

            $uploadedFiles[] = $targetPath;
        }

        // Opération de compression
        if ($operation === 'compress') {
            $outputFile = OUTPUT_DIR . 'archive_' . uniqid() . '.keyce';
            if ($manager->compressFiles($uploadedFiles, $outputFile)) {
                $response['success'] = true;
                $response['message'] = 'Fichiers compressés avec succès.';
                $response['data'] = [
                    'path' => $outputFile,
                    'name' => basename($outputFile)
                ];
            } else {
                throw new Exception('Échec de la compression des fichiers.');
            }
        }

        // Opération de décompression
        if ($operation === 'decompress') {
            if (count($uploadedFiles) !== 1) {
                throw new Exception('Veuillez télécharger un seul fichier .keyce pour la décompression.');
            }

            $keyceFile = $uploadedFiles[0];
            if (pathinfo($keyceFile, PATHINFO_EXTENSION) !== 'keyce') {
                throw new Exception('Le fichier téléchargé n’est pas un fichier .keyce valide.');
            }

            if ($manager->decompressFile($keyceFile, OUTPUT_DIR)) {
                $decompressedFiles = [];
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(OUTPUT_DIR));
                foreach ($iterator as $item) {
                    if ($item->isFile()) {
                        $decompressedFiles[] = [
                            'path' => str_replace(__DIR__, '', $item->getPathname()),
                            'name' => $item->getFilename()
                        ];
                    }
                }

                $response['success'] = true;
                $response['message'] = 'Fichiers décompressés avec succès.';
                $response['data'] = $decompressedFiles;
            } else {
                throw new Exception('Échec de la décompression du fichier.');
            }
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
