<?php

include_once 'CompressionAlgorithm.php';

abstract class AbstractCompressor implements CompressionAlgorithm
{
    /**
     * Vérifie si un fichier existe.
     *
     * @param string $filePath Chemin du fichier.
     * @return bool True si le fichier existe, sinon False.
     */
    protected function fileExists(string $filePath): bool
    {
        return file_exists($filePath);
    }

    /**
     * Lecture des données du fichier.
     *
     * @param string $filePath Chemin du fichier.
     * @return string|null Contenu du fichier ou null en cas d'erreur.
     */
    protected function readFile(string $filePath): ?string
    {
        return $this->fileExists($filePath) ? file_get_contents($filePath) : null;
    }

    /**
     * Écrit des données dans un fichier.
     *
     * @param string $filePath Chemin du fichier.
     * @param string $data Données à écrire.
     * @return bool True si l'écriture réussit, sinon False.
     */
    protected function writeFile(string $filePath, string $data): bool
    {
        return file_put_contents($filePath, $data) !== false;
    }

    // Méthodes compress et decompress à implémenter par les sous-classes.
    abstract public function compress(string $input, string $output): bool;

    abstract public function decompress(string $input, string $output): bool;

    
}
