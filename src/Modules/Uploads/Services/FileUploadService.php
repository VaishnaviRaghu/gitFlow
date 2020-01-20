<?php

namespace App\Modules\Uploads\Services;

use App\Modules\Uploads\Repository\FileUploadRepository;
use App\Modules\Uploads\Services\FileUploadServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Psr\Log\LoggerInterface;

class FileUploadService implements FileUploadServiceInterface
{

    private $fileUploadRepo;
    private $container;
    private $logger;

    public function __construct(FileUploadRepository $fileUploadRepo,
            ContainerInterface $container, LoggerInterface $logger)
    {

        $this->fileUploadRepo = $fileUploadRepo;
        $this->container = $container;
        $this->logger = $logger;
    }

    public function fileDetails()
    {
        /*  PSEUDO-CODE
         *  getting the file details from getFileDetails method in FileUploadRepository
         */
        try {
            $uploadedDoc = $this->fileUploadRepo->getFileDetails();
            return $uploadedDoc;
        } catch (\Exception $ex) {
            $this->logger->error("Exception error while getting the file details" . $ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }

    public function fileUpload($data): ?string
    {
        $fileName = md5(uniqid()) . '.' . $data->guessExtension();
        $mimeType = $data->getClientMimeType();
        $fileSize = $data->getSize();
        $filePath = $this->container->getParameter('PROJECT_DIR') . '/file-uploads/' . $fileName;

        $config = new \Flow\Config();
        $config->setTempDir($this->container->getParameter('PROJECT_DIR') . '/chunks-temp-folder');
        $request = new \Flow\Request();
       // dd($request);
        $uploadFolder = $this->container->getParameter('PROJECT_DIR') . '/file-uploads/'; // Folder where the file will be stored
        $uploadFileName = uniqid() . "_" . $fileName; // The name the file will have on the server
        $uploadPath = $uploadFolder . $uploadFileName;
        //dd($uploadPath, $config, $request);
        //dd(\Flow\Basic::save($uploadPath, $config, $request));
        if (\Flow\Basic::save($uploadPath, $config, $request)) {
            echo "file saved successfully and can be accessed at". $uploadPath;
        } else {
             echo "This is not a final chunk or request is invalid, continue to upload.";
        }
//        try {
//            $data->move(
//                    $this->container->getParameter('PROJECT_DIR') . '/file-uploads',
//                    $fileName
//            );
//        } catch (\FileException $fex) {
//            // ... handle exception if something happens during file upload
//            $this->logger->error("Exception while moving the file to local directory" . $fex->getMessage());
//            throw new \FileException($fex->getMessage());
//        }
        $fileDetails = [
            'fileName' => $fileName,
            'mimeType' => $mimeType,
            'fileSize' => $fileSize,
            'filePath' => $filePath
        ];
        $file = $this->fileUploadRepo->createFile($fileDetails);
        return $file;
    }

    public function getFile($id)
    {
        try {
            $filePath = $this->fileUploadRepo->getFilePath($id);
            // dd($filePath);
            $fileName = json_decode(json_encode($filePath[0]['filePath']));
            // dd($fileName);
//            $file_with_path = $this->container->getParameter('PROJECT_DIR') . '/uploads/'  . $fileName;
//            $webPath = realpath($file_with_path . '/../uploads');
//            $path = realpath($file_with_path . "/../uploads/" . $fileName);
//              dd($path);
//            //dd($file_with_path);
            $response = new BinaryFileResponse($fileName);
            dd($response);
//            $realPath = $response->file->getRealPath();
//            //$realPath = $response->file->get('realPath');
//            // $response->headers->set ( 'Content-Type', 'text/plain' );
//            // dd($response);
//        //$response->setContentDisposition ( ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName );
//            //dd($response);
//            dd($realPath);
            return $fileName;
        } catch (\Exception $ex) {
            $this->logger->error("Exception while getting the file path" . $ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }

}
