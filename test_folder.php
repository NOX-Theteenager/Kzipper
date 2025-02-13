<?php

require_once 'src/CompressorManager.php'; // Incluez vos classes ici

// 1. Créer un environnement de test
// $testDir = __DIR__ . '/test_folder';
// if (!is_dir($testDir)) {
//     mkdir($testDir, 0777, true);
// }
// file_put_contents($testDir . '/file1.txt', 'Hello World');
// file_put_contents($testDir . '/file2.log', 'This is a log file.');
// mkdir($testDir . '/subfolder', 0777, true);
// file_put_contents($testDir . '/subfolder/file3.txt', 'This is in a subfolder.');

// echo "Test files created in: $testDir\n";

$testDir = 'E:/B2/semestre_1/java';

// 2. Instancier le gestionnaire de compression
$compressorManager = new CompressorManager();

// 3. Compresser le dossier
$outputFile = __DIR__ . '/compressed_archive.keyce';
echo "Compressing the folder...\n";
if ($compressorManager->compressFiles([$testDir], $outputFile)) {
    echo "Compression successful. Archive created at: $outputFile\n";
} else {
    echo "Compression failed.\n";
    exit(1);
}

// 4. Décompresser l'archive
$decompressedDir = __DIR__ . '/decompressed_folder';
echo "Decompressing the archive...\n";
if ($compressorManager->decompressFile($outputFile, $decompressedDir)) {
    echo "Decompression successful. Files extracted to: $decompressedDir\n";
} else {
    echo "Decompression failed.\n";
    exit(1);
}

// 5. Vérifier les fichiers décompressés
function compareDirectories(string $dir1, string $dir2): bool
{
    $items1 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir1));
    $items2 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir2));

    foreach ($items1 as $item) {
        if ($item->isFile()) {
            $relativePath = str_replace($dir1 . DIRECTORY_SEPARATOR, '', $item->getPathname());
            $file2 = $dir2 . DIRECTORY_SEPARATOR . $relativePath;

            if (!file_exists($file2) || file_get_contents($item->getPathname()) !== file_get_contents($file2)) {
                echo "File mismatch: $relativePath\n";
                return false;
            }
        }
    }

    return true;
}

if (compareDirectories($testDir, $decompressedDir)) {
    echo "Test passed: The decompressed files match the original files.\n";
} else {
    echo "Test failed: The decompressed files do not match the original files.\n";
}