<?php

require_once 'src/CompressionAlgorithm.php';
require_once 'src/AbstractCompressor.php';
require_once 'src/HuffmanCompressor.php';
require_once 'src/GzipCompressor.php';
require_once 'src/Bzip2Compressor.php';
require_once 'src/CompressorManager.php';

$manager = new CompressorManager();

// Compression
echo "Compression en cours...\n";
$filesToCompress = [
    "E:\B2\semestre_1\php\Kzipper\dossier_test_compression/",
];

$outputKeyceFile = 'test/archive.keyce';

if ($manager->compressFiles($filesToCompress, $outputKeyceFile)) {
    echo "Fichiers compressés avec succès dans $outputKeyceFile.\n";
} else {
    echo "Échec de la compression.\n";
}

// Décompression
echo "Décompression en cours...\n";
$outputDirectory = 'test/output';

if (!is_dir($outputDirectory)) {
    mkdir($outputDirectory, 0777, true);
}

if ($file = $manager->decompressFile($outputKeyceFile, $outputDirectory)) {
    echo $file;
    echo "Fichiers décompressés avec succès dans $outputDirectory.\n";
} else {
    echo "Échec de la décompression.\n";
}
