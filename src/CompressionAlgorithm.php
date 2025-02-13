<?php

interface CompressionAlgorithm
{
    /**
     * Compresse un fichier ou des données.
     *
     * @param string $input Chemin du fichier source ou données en entrée.
     * @param string $output Chemin du fichier compressé ou données en sortie.
     * @return bool True si la compression réussit, sinon False.
     */
    public function compress(string $input, string $output): bool;

    /**
     * Décompresse un fichier ou des données.
     *
     * @param string $input Chemin du fichier compressé ou données en entrée.
     * @param string $output Chemin du fichier décompressé ou données en sortie.
     * @return bool True si la décompression réussit, sinon False.
     */
    public function decompress(string $input, string $output): bool;
}
