<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Modules\Uploads\Services;

interface FileUploadServiceInterface
{
    public function fileDetails();
    public function fileUpload($data);
    public function getFile($id);
}

