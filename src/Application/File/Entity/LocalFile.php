<?php

namespace PhpUniter\PackageLaravel\Application\File\Entity;

class LocalFile
{
    private string $filePath;
    private string $fileBody;

    public function __construct(string $filePath, string $fileBody)
    {
        $this->filePath = $filePath;
        $this->fileBody = $fileBody;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getFileBody(): string
    {
        return $this->fileBody;
    }
}
