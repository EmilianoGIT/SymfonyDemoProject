<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FileUploader {

    
    /** 
     * @var ContainerInterface
    */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function uploadFile(UploadedFile $file){

        //I concatenate an unique id with the extension of the file
        $filename = md5(uniqid()) . '.' . $file->guessClientExtension();
        $file->move($this->container->getParameter('uploads_dir'), $filename);

        return $filename;
    }

    public function deleteFile(string $filename){

        $filesystem = new Filesystem();

        $filesystem->remove($this->container->getParameter('uploads_dir') . $filename);

        return $filename;
    }

}