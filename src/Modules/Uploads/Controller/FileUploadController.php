<?php

namespace App\Modules\Uploads\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Modules\Uploads\Services\FileUploadService;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Modules\Uploads\DTO\FileValidateRequest;
use Psr\Log\LoggerInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\{
    Request,
    Response,
    JsonResponse
};

class FileUploadController extends AbstractController
{

    /**
     * @var \App\Modules\Aggregator\Services\FileUploadService
     */
    private $fileUploadService;
    private $validator;
    private $logger;

    /**
     * @author Vaishnavi R <vaishnavi.r@impelsys.com>
     * @param FileUploadService $fileUploadService
     */
    public function __construct(FileUploadService $fileUploadService,
            ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->fileUploadService = $fileUploadService;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @Route("/view", name="get_file_details", methods={"GET"})
     * @param type $name 
     * 
     * 
     * @SWG\Get(
     *     summary="Retrieve all existing file details.",
     *     description="Retrieve all existing file details.",
     *     operationId="getFileDetails",
     *     tags={"Uploads"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="FileName",
     *         in="query",
     *         type="string",
     *         description="File parameter",
     *     ),
     *     @SWG\Parameter(
     *         name="FileSize",
     *         in="query",
     *         type="string",
     *         description="File parameter",
     *     ),
     *     @SWG\Parameter(
     *         name="MimeType",
     *         in="query",
     *         type="string",
     *         description="File parameter",
     *     ),
     *      @SWG\Parameter(
     *         name="FilePath",
     *         in="query",
     *         type="string",
     *         description="File parameter",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="File details fetched successfully.",
     *     )
     * )
     */
    public function veiwFileDetails(): JsonResponse
    {

        /*  PSEUDO-CODE
         *  Requesting for fileDetails
         *  Trying to get the file details from service called FileUploadService 
         *  Get the data and return in json formate and send the sucess code with message
         *  Else send the error message with code 400
         */

        try {
            $fileInfo = $this->fileUploadService->fileDetails();
            return $this->json([
                        'data' => $fileInfo,
                        'code' => 200,
                        'message' => 'File Details are',
            ]);
        } catch (\Exception $e) {
            $this->logger->error("Unable to get the file details" . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @Route("/upload", name="file_upload", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadFile(Request $request): Response
    {
        //dd($request);
        $data = $request->files->get('file');
        //dd($data);
        $fileName = pathinfo($data->getClientOriginalName(), PATHINFO_FILENAME);
        $fileSize = $data->getSize();
        $mimeType = $data->getClientMimeType();
        $file = [
            'fileName' => $fileName,
            'mimeType' => $mimeType,
            'fileSize' => $fileSize,
        ];
        $dto = new FileValidateRequest();
        $dto->setFileName($fileName);
        $dto->setFileSize($fileSize);
        $dto->setMimeType($mimeType);
        //dd($dto);
        $error = $this->validator->validate($dto);
        dd($error);
        //dd($error);
        if (count($error) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $errorsString = (string) $error;

            return new Response($errorsString);
        }
        $fileInfo = $this->fileUploadService->fileUpload($data);
        // dd($fileInfo);
        return $this->json([
                    'code' => 200,
                    'message' => 'File Uploaded Successfully.',
        ]);
    }

//        try {
//
//            $data = $request->files->get('file');
//
//            $fileInfo = $this->fileUploadService->fileUpload($data);
//
//            if ($fileInfo == 1) {
//                return $this->json([
//                            'code' => 200,
//                            'message' => 'File Uploaded Successfully.',
//                ]);
//            } else {
//                return $this->json([
//                            'code' => 400,
//                            'message' => 'File not Uploaded, Please check.'
//                ]);
//            }
//        } catch (Exception $ex) {
//            throw new \Exception($ex->getMessage());
//        }
//}

    /**
     * @Route("/view/{id}", name="view_file", methods={"GET"})
     * @param type $id fileId
     * @return JsonResponse
     */
    public function viewFile($id): JsonResponse
    {
        try {

            $file = $this->fileUploadService->getFile($id);

            return $this->json([
                        'data' => $file,
                        'code' => 200,
                        'message' => 'Your File Details are',
            ]);
        } catch (\Exception $ex) {
            $this->logger->error("exception while viewing the file" . $ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }

}
