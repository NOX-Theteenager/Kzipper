<?php

include_once 'CompressionAlgorithm.php';

class HuffmanCompressor implements CompressionAlgorithm
{
    /**
     * Compresse les données d'un fichier et génère un fichier compressé.
     *
     * @param string $inputFile Chemin du fichier à compresser.
     * @param string $outputFile Chemin du fichier de sortie compressé.
     * @return bool True si la compression réussit, sinon False.
     */
    public function compress(string $inputFile, string $outputFile): bool
    {
        if (!file_exists($inputFile)) {
            throw new Exception("Le fichier d'entrée n'existe pas.");
        }

        // Lire les données d'entrée
        $data = file_get_contents($inputFile);
        if ($data === false) {
            throw new Exception("Impossible de lire le fichier d'entrée.");
        }

        // Étape 1 : Calculer les fréquences des caractères
        $frequencies = $this->calculateFrequencies($data);

        // Étape 2 : Construire l'arbre de Huffman
        $huffmanTree = $this->buildHuffmanTree($frequencies);

        // Étape 3 : Générer les codes de Huffman
        $codes = $this->generateHuffmanCodes($huffmanTree);

        // Étape 4 : Encoder les données avec les codes de Huffman
        $binaryData = $this->encodeData($data, $codes);

        // Étape 5 : Convertir les données binaires en octets
        $compressedData = $this->binaryToBytes($binaryData);

        // Étape 6 : Créer le fichier compressé
        $archive = [
            'tree' => $huffmanTree,         // Arbre de Huffman pour la décompression
            'size' => strlen($data),        // Taille originale des données
            'data' => $compressedData,      // Données compressées
        ];

        // Sauvegarder l'archive dans un fichier compressé
        $result = file_put_contents($outputFile, serialize($archive));

        return $result !== false;
    }


    /**
    * Décompresse un fichier compressé avec Huffman.
    *
    * @param string $inputFile Chemin du fichier compressé.
    * @param string $outputFile Chemin du fichier de sortie décompressé.
    * @return bool True si la décompression réussit, sinon False.
    */
    public function decompress(string $inputFile, string $outputFile): bool
    {
        if (!file_exists($inputFile)) {
            throw new Exception("Le fichier compressé n'existe pas.");
        }

        // Lire et désérialiser le fichier compressé
        $archive = unserialize(file_get_contents($inputFile));
        if (!isset($archive['tree'], $archive['size'], $archive['data'])) {
            throw new Exception("Le fichier compressé est corrompu ou invalide.");
        }

        // Récupérer les parties de l'archive
        $huffmanTree = $archive['tree'];
        $originalSize = $archive['size'];
        $compressedData = $archive['data'];

        // Convertir les données compactées en chaîne binaire
        $binaryData = $this->bytesToBinary($compressedData);

        // Décoder les données binaires avec l'arbre de Huffman
        $decodedData = $this->decodeData($binaryData, $huffmanTree, $originalSize);

        // Écrire les données décompressées dans le fichier de sortie
        return file_put_contents($outputFile, $decodedData) !== false;
    }

    /**
     * Calcul des fréquences des caractères ou octets.
     *
     * @param string $data Données à analyser.
     * @param bool $binaryMode Indique si le traitement est binaire.
     * @return array Fréquences des caractères/octets.
     */
    private function calculateFrequencies(string $data): array
    {
        $frequencies = [];
        $length = strlen($data); // Utiliser strlen pour traiter les octets
    
        for ($i = 0; $i < $length; $i++) {
            $byte = $data[$i]; // Lire chaque octet
            if (!isset($frequencies[$byte])) {
                $frequencies[$byte] = 0;
            }
            $frequencies[$byte]++;
        }
    
        return $frequencies;
    }
    


    /**
     * Construction de l'arbre de Huffman.
     *
     * @param array $frequencies Fréquences des caractères/octets.
     * @return array Arbre de Huffman.
     */
    private function buildHuffmanTree(array $frequencies): array
    {
        // Créer une file de priorité (min-heap)
        $heap = new SplPriorityQueue();

        // Ajouter chaque caractère/octet avec sa fréquence
        foreach ($frequencies as $char => $freq) {
            $heap->insert(['char' => $char, 'freq' => $freq, 'left' => null, 'right' => null], -$freq);
        }

        // Construire l'arbre
        while ($heap->count() > 1) {
            // Extraire les deux nœuds avec les fréquences les plus faibles
            $node1 = $heap->extract();
            $node2 = $heap->extract();

            // Combiner ces nœuds en un nouveau nœud parent
            $mergedNode = [
                'char' => null, // Nœud interne (pas de caractère)
                'freq' => $node1['freq'] + $node2['freq'],
                'left' => $node1,
                'right' => $node2
            ];

            // Insérer le nouveau nœud dans le heap
            $heap->insert($mergedNode, -$mergedNode['freq']);
        }

        // Retourner la racine de l'arbre
        return $heap->extract();
    }


    /**
     * Génération des codes de Huffman à partir de l'arbre.
     *
     * @param array $tree Arbre de Huffman.
     * @return array Tableau associatif des codes (caractère => code binaire).
     */
    private function generateHuffmanCodes(array $tree): array
    {
        $codes = [];
        $this->traverseTree($tree, '', $codes);
        return $codes;
    }

    /**
     * Parcours récursif de l'arbre pour générer les codes.
     *
     * @param array $node Nœud courant de l'arbre.
     * @param string $currentCode Code binaire courant.
     * @param array &$codes Tableau associatif des codes générés.
     * @return void
     */
    private function traverseTree(array $node, string $currentCode, array &$codes): void
    {
        // Si le nœud est une feuille, associer le code au caractère
        if ($node['char'] !== null) {
            $codes[$node['char']] = $currentCode;
            return;
        }

        // Parcourir le sous-arbre gauche (ajouter '0' au code)
        if ($node['left'] !== null) {
            $this->traverseTree($node['left'], $currentCode . '0', $codes);
        }

        // Parcourir le sous-arbre droit (ajouter '1' au code)
        if ($node['right'] !== null) {
            $this->traverseTree($node['right'], $currentCode . '1', $codes);
        }
    }


    /**
     * Encode les données en utilisant les codes de Huffman.
     *
     * @param string $data Données à encoder.
     * @param array $codes Tableau des codes de Huffman (caractère => code binaire).
     * @return string Données encodées sous forme de chaîne binaire.
     */
    private function encodeData(string $data, array $codes): string
    {
        $encoded = '';
        $length = strlen($data);

        // Parcourir chaque caractère et construire la chaîne encodée
        for ($i = 0; $i < $length; $i++) {
            $char = $data[$i];

            if (!isset($codes[$char])) {
                throw new Exception("Le caractère '{$char}' ne peut pas être encodé. Code Huffman manquant.");
            }

            $encoded .= $codes[$char];
        }

        return $encoded;
    }


    /**
     * Décode une chaîne binaire en utilisant l'arbre de Huffman.
     *
     * @param string $binaryData Chaîne binaire à décoder.
     * @param array $tree Arbre de Huffman pour le décodage.
     * @param int $originalSize Taille originale des données (en octets).
     * @return string Données décodées.
     */
    private function decodeData(string $binaryData, array $tree, int $originalSize): string
    {
        $decoded = '';
        $node = $tree;
        $decodedLength = 0;

        for ($i = 0, $len = strlen($binaryData); $i < $len; $i++) {
            // Naviguer dans l'arbre selon le bit actuel
            $node = $binaryData[$i] === '0' ? $node['left'] : $node['right'];

            // Si on atteint une feuille, récupérer l'octet
            if (isset($node['char'])) {
                $decoded .= $node['char']; // Ajouter l'octet brut
                $node = $tree; // Retourner à la racine
                $decodedLength++;

                // Arrêter si toutes les données originales ont été reconstruites
                if ($decodedLength >= $originalSize) {
                    break;
                }
            }
        }

        return $decoded; // Retourner les données décompressées
    }



    /**
     * Convertit une chaîne binaire en un tableau d'octets.
     *
     * @param string $binaryData Données encodées sous forme de chaîne binaire.
     * @return string Données compactées sous forme binaire (octets).
     */
    private function binaryToBytes(string $binaryData): string
    {
        // Ajouter des zéros pour compléter le dernier octet si nécessaire
        $paddedData = str_pad($binaryData, (ceil(strlen($binaryData) / 8) * 8), '0', STR_PAD_RIGHT);
        $bytes = '';

        // Convertir chaque bloc de 8 bits en un caractère binaire
        for ($i = 0; $i < strlen($paddedData); $i += 8) {
            $byte = substr($paddedData, $i, 8);
            $bytes .= chr(bindec($byte));
        }

        return $bytes; // Retourner les octets bruts
    }


    /**
     * Convertit une chaîne d'octets en une chaîne binaire.
     *
     * @param string $byteData Données compactées sous forme binaire (octets).
     * @return string Chaîne binaire reconstruite.
     */
    private function bytesToBinary(string $byteData): string
    {
        $binaryData = '';
    
        // Convertir chaque octet en une chaîne binaire de 8 bits
        for ($i = 0; $i < strlen($byteData); $i++) {
            $binaryData .= str_pad(decbin(ord($byteData[$i])), 8, '0', STR_PAD_LEFT);
        }
    
        return $binaryData; // Retourner la chaîne binaire complète
    }
    


}

    

