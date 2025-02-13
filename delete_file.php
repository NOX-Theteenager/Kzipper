<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $filePath = $data['path'] ?? null;

    if ($filePath && file_exists($filePath)) {
        if (unlink($filePath)) {
            echo json_encode(['success' => true, 'message' => 'Fichier supprimé']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Échec de la suppression du fichier']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Fichier non trouvé']);
    }
}
?>
