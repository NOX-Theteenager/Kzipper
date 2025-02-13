<?php
$response = ['success' => false, 'message' => ''];

// Fonction pour supprimer les fichiers d'un dossier
function clearFolder($folder) {
    if (!is_dir($folder)) {
        return false;
    }

    foreach (scandir($folder) as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $folder . DIRECTORY_SEPARATOR . $file;
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }
    }
    return true;
}

$uploadsCleared = clearFolder(__DIR__ . '/uploads');
$outputCleared = clearFolder(__DIR__ . '/output');

if ($uploadsCleared && $outputCleared) {
    $response['success'] = true;
    $response['message'] = 'Cache vidé avec succès.';
} else {
    $response['message'] = 'Impossible de vider certains dossiers.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>