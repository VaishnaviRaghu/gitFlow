<?php

namespace App\tests;

use PHPUnit\Framework\TestCase;
use App\Modules\Uploads\Controller\FileUploadController;

Class FileUploadTest extends TestCase 
{
    public function uploadTest()
    {
        $fileUpload = new FileUploadController;
        //dd($fileUpload);
    }
}

