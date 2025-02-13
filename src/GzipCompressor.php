<?php

include_once 'AbstractCompressor.php';

class GzipCompressor extends AbstractCompressor
{
    public function compress(string $input, string $output): bool
    {
        $data = $this->readFile($input);
        if ($data === null) {
            return false;
        }

        $compressed = gzencode($data, 9);
        return $compressed !== false ? $this->writeFile($output, $compressed) : false;
    }

    public function decompress(string $input, string $output): bool
    {
        $data = $this->readFile($input);
        if ($data === null) {
            return false;
        }

        $decompressed = gzdecode($data);
        return $decompressed !== false ? $this->writeFile($output, $decompressed) : false;
    }
}
