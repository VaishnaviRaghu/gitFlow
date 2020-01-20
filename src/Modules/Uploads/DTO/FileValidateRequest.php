<?php

namespace App\Modules\Uploads\DTO;

use Symfony\Component\Validator\Constraints as Assert;

Class FileValidateRequest
{
    /**
     * 
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="The File Name {{ value }} is not a valid {{ type }}."
     * )
     */

    private $fileName;
    /**
     * @Assert\File(
     *       maxSize = "1024k",
     *       maxSizeMessage="The file size is too large, Please upload less than {{ limit }} {{ suffix }}."
     * )
     */
    private $fileSize;
    /**
     *  @Assert\File(
     *      mimeTypes = {"application/pdf", "application/x-pdf"},
     *      mimeTypesMessage = "Please upload a valid PDF."
     * )
     */
    private $mimeType;
    
    function getFileName()
    {
        return $this->fileName;
    }

    function getFileSize()
    {
        return $this->fileSize;
    }

    function getMimeType()
    {
        return $this->mimeType;
    }

    function setFileName($fileName): void
    {
        $this->fileName = $fileName;
    }

    function setFileSize($fileSize): void
    {
        $this->fileSize = $fileSize;
    }

    function setMimeType($mimeType): void
    {
        $this->mimeType = $mimeType;
    }



}
