<?php

namespace App\Modules\Uploads\Repository;

use App\Entity\FileUpload;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class FileUploadRepository
{

    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager,
            LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function getFileDetails()
    {
        /*  PSEUDO-CODE
         *  get all the file details from fileuplad table in database
         */
        try {
            $dbConn = $this->entityManager->createQueryBuilder();

            $fileDetails = $dbConn->select('fu.id,fu.fileName,fu.fileSize,fu.mimeType,fu.filePath')
                    ->from(FileUpload::class, 'fu')
                    ->getQuery()
                    ->execute();

            return $fileDetails;
        } catch (\DoctrineException $dExc) {
            $this->logger->error("DoctrineException while getting the details of file." . $dExc->getMessage());
            throw new \Exception($dExc->getMessage());
        } catch (\Exception $ex) {
            $this->logger->error("Exception while getting the file details." . $ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }

    public function createFile($data)
    {
        try {
            $now = new \DateTime(date('Y-m-d H:i:s'));
            $fileUpload = new FileUpload;
            $fileUpload->setFileName($data['fileName']);
            $fileUpload->setFileSize($data['fileSize']);
            $fileUpload->setMimeType($data['mimeType']);
            $fileUpload->setFilePath($data['filePath']);
            $fileUpload->setUpdatedBy($now);
            $fileUpload->setCreatedBy($now);
            $fileUpload->setStatus(1);
            $this->entityManager->persist($fileUpload);
            $this->entityManager->flush();

            if (!empty($fileUpload->getId())) {

                return 1;
            } else {

                return 0;
            }
        } catch (\DoctrineException $dex) {
            $this->logger->error("Doctrine Exception while adding the file details." . $dex->getMessage());
            throw new \Exception($dex->getMessage());
        } catch (\Exception $ex) {
            $this->logger->error("Exception while adding the file details" . $ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }

    public function getFilePath($id)
    {

        try {

            $dbConn = $this->entityManager->createQueryBuilder();
            $fileUrl = $dbConn->select('fu.id', 'fu.filePath', 'fu.fileName')
                    ->from(FileUpload::class, 'fu')
                    ->where('fu.id =' . $id)
                    ->getQuery()
                    ->execute();

            return $fileUrl;
        } catch (\DoctrineException $dex) {
            $this->logger->error("Doctrine exception while getting the file path" . $dex->getMessage());
            throw new \Exception($dex->getMessage());
        } catch (\Exception $ex) {
            $this->logger->error("Exception while getting the file path" . $ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }

}
