<?php

include_once 'GzipCompressor.php';
include_once 'AbstractCompressor.php';
include_once 'Bzip2Compressor.php';
include_once 'HuffmanCompressor.php';

class CompressorManager
{
    private array $compressors;

    public function __construct()
    {
        // Enregistrer les algorithmes disponibles
        $this->compressors = [
            'gzip' => new GzipCompressor(),
            'bzip2' => new Bzip2Compressor(),
            'huffman' => new HuffmanCompressor(),
        ];
    }

    /**
     * Récupère tous les fichiers d'un dossier récursivement.
     *
     * @param string $directory Chemin du dossier.
     * @return array Liste des chemins des fichiers.
     */
    private function getFilesFromDirectory(string $directory): array
    {
        $files = [];
        $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        foreach ($items as $item) {
            if ($item->isFile()) {
                $files[] = $item->getPathname();
            }
        }

        return $files;
    }

    /**
     * Trouve le chemin relatif d'un fichier ou d'un répertoire par rapport à un chemin de base.
     *
     * @param string $basePath Chemin absolu du répertoire de base.
     * @param string $targetPath Chemin absolu du fichier ou répertoire cible.
     * @return string Chemin relatif.
     */
    function getRelativePath(string $basePath, string $targetPath): string
    {
        // Normaliser les chemins (supprimer les barres obliques inutiles, etc.)
        $basePath = realpath($basePath);
        $targetPath = realpath($targetPath);

        if ($basePath === false || $targetPath === false) {
            throw new Exception("Chemins invalides : $basePath ou $targetPath");
        }

        // Convertir les chemins en tableaux de segments
        $baseSegments = explode(DIRECTORY_SEPARATOR, $basePath);
        $targetSegments = explode(DIRECTORY_SEPARATOR, $targetPath);

        // Trouver les segments communs
        $commonSegments = [];
        foreach ($baseSegments as $index => $segment) {
            if (isset($targetSegments[$index]) && $segment === $targetSegments[$index]) {
                $commonSegments[] = $segment;
            } else {
                break;
            }
        }

        // Calculer les parties restantes
        $baseRemaining = array_slice($baseSegments, count($commonSegments));
        $targetRemaining = array_slice($targetSegments, count($commonSegments));

        // Construire le chemin relatif
        $relativePath = str_repeat('..' . DIRECTORY_SEPARATOR, count($baseRemaining)) . implode(DIRECTORY_SEPARATOR, $targetRemaining);

        return rtrim($relativePath, DIRECTORY_SEPARATOR);
    }


    
    /**
     * Sélectionne l'algorithme optimal en fonction du type MIME.
     *
     * @param string $filePath Chemin du fichier.
     * @return CompressionAlgorithm|null Algorithme optimal ou null si aucun.
     */
    private function selectAlgorithm(string $filePath): ?CompressionAlgorithm
    {
        // $mimeType = mime_content_type($filePath);

        // // Logique de sélection simplifiée
        // if (str_starts_with($mimeType, 'text')) {
        //     return $this->compressors['bzip2']; // Texte => Huffman
        // }

        // if (str_starts_with($mimeType, 'application')) {
        //     return $this->compressors['gzip']; // Données génériques => Gzip
        // }

        // if (str_starts_with($mimeType, 'image') || str_starts_with($mimeType, 'video')) {
        //     return $this->compressors['bzip2']; // Images/Vidéos => Bzip2
        // }

        // Par défaut on va utiliser huffman a cause des exigences du professeur
        return $this->compressors['huffman'];
    }

    /**
     * Compresse un ou plusieurs fichiers en un fichier `.keyce`.
     *
     * @param array $files Liste des chemins des fichiers à compresser.
     * @param string $outputPath Chemin du fichier de sortie.
     * @return bool True si la compression réussit, sinon False.
     */
    public function compressFiles(array $paths, string $outputPath, string $outputDir): bool
    {
        $metadata = [];
        $archiveData = [];

        foreach ($paths as $path) {
            if (!file_exists($path)) {
                continue;
            }

            // Déterminer le chemin racine pour les chemins relatifs
            $rootPath = rtrim(realpath($path), DIRECTORY_SEPARATOR);

            // Si c'est un dossier, obtenir tous les fichiers récursivement
            $files = is_dir($path) ? $this->getFilesFromDirectory($path) : [$path];
            $isDir = is_dir($path);

            foreach ($files as $file) {
                // Sélection de l'algorithme optimal
                $algorithm = $this->selectAlgorithm($file);
                if ($algorithm === null) {
                    return false;
                }

                // Compression du fichier
                $tempFile = tempnam(sys_get_temp_dir(), 'cmp_');
                if (!$algorithm->compress($file, $tempFile)) {
                    unlink($tempFile);
                    return false;
                }

                // Lecture des données compressées
                $compressedData = file_get_contents($tempFile);
                unlink($tempFile);

                if($isDir)
                    $relativePath = ltrim(str_replace($rootPath, '', realpath($file)), DIRECTORY_SEPARATOR, );
                else
                    $relativePath = $this->getRelativePath($outputDir, $path);

                // Stocker les métadonnées
                $metadata[] = [
                    'original_name' => basename($file),
                    'relative_path' => $relativePath,
                    'algorithm' => get_class($algorithm),
                    'size_original' => filesize($file),
                    'size_compressed' => strlen($compressedData),
                ];

                // Ajouter les données compressées à l'archive
                $archiveData[] = $compressedData;
            }
        }

        // Générer le fichier `.keyce`
        $keyceData = json_encode($metadata) . "\n===SEPARATOR===\n" . implode("\n===SEPARATOR===\n", $archiveData);
        return file_put_contents($outputPath, $keyceData) !== false;
    }



    /**
     * Décompresse un fichier `.keyce`.
     *
     * @param string $keyceFile Chemin du fichier .keyce.
     * @param string $outputDir Répertoire où décompresser les fichiers.
     * @return bool True si la décompression réussit, sinon False.
     */
    public function decompressFile(string $keyceFile, string $outputDir): bool
    {
        if (!file_exists($keyceFile)) {
            return false; // Fichier introuvable
        }

        // Lecture des données de l'archive
        $data = file_get_contents($keyceFile);
        [$metadataRaw, $compressedDataRaw] = explode("\n===SEPARATOR===\n", $data, 2);

        $metadata = json_decode($metadataRaw, true);
        if ($metadata === null) {
            return false; // Erreur de lecture des métadonnées
        }

        // Découper les segments compressés
        $compressedSegments = explode("\n===SEPARATOR===\n", $compressedDataRaw);

        foreach ($metadata as $index => $fileInfo) {
            $algorithmClass = $fileInfo['algorithm'];
            $relativePath = $fileInfo['relative_path']; // Chemin relatif dans l'archive
            $originalName = $fileInfo['original_name'];

            $outputFile = $outputDir . DIRECTORY_SEPARATOR .ltrim($relativePath, DIRECTORY_SEPARATOR);
            // Créer les dossiers nécessaires
            $outputDirPath = dirname($outputFile);
            if (!is_dir($outputDirPath)) {
                mkdir($outputDirPath, 0777, true);
            }

            // Vérifier la correspondance avec l'algorithme
            if (!isset($compressedSegments[$index])) {
                return false; // Données compressées manquantes
            }

            foreach ($this->compressors as $compressor) {
                if (get_class($compressor) === $algorithmClass) {
                    $tempFile = tempnam(sys_get_temp_dir(), 'dmp_');
                    file_put_contents($tempFile, $compressedSegments[$index]);

                    if (!$compressor->decompress($tempFile, $outputFile)) {
                        unlink($tempFile);
                        return false; // Erreur lors de la décompression
                    }

                    unlink($tempFile);
                    break;
                }
            }
        }

        return true;
    }

}


    



