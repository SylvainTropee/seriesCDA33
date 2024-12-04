<?php

namespace App\HelperUtilService;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{

    public function __construct(
        string $directory,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {

        $logger->error($directory);
    }

    public function upload(UploadedFile $file, string $directory, string $name = ''): string
    {

        $fileName = ($name ? $name . '-' : $name) . uniqid() . '.' . $file->guessExtension();
        $file->move($directory, $fileName);
        return $fileName;
    }

    public function delete(string $filename, string $directory): bool
    {
        return unlink($directory . DIRECTORY_SEPARATOR . $filename);
    }

}