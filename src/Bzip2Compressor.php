<?php

class Bzip2Compressor extends AbstractCompressor
{
    public function compress(string $input, string $output): bool
    {
        $data = $this->readFile($input);
        if ($data === null) {
            return false;
        }

        $compressed = bzcompress($data, 9);
        return $compressed !== false ? $this->writeFile($output, $compressed) : false;
    }

    public function decompress(string $input, string $output): bool
    {
        $data = $this->readFile($input);
        if ($data === null) {
            return false;
        }

        $decompressed = bzdecompress($data);
        return $decompressed !== false ? $this->writeFile($output, $decompressed) : false;
    }
}
