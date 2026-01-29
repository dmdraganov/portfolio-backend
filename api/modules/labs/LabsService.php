<?php
require_once dirname(__DIR__, 2) . '/core/JsonRepository.php';
require_once dirname(__DIR__, 2) . '/core/FileHelper.php';

class LabsService
{
    private JsonRepository $repository;
    private FileHelper $fileHelper;
    private string $filesDir;
    private string $resourceDir = 'labs';

    public function __construct()
    {
        $this->repository = new JsonRepository('labs.json');
        $this->fileHelper = new FileHelper();
        $this->filesDir = FILES_PATH . DIRECTORY_SEPARATOR . $this->resourceDir;
    }

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function handleFileUpload(string $labId, array $file): array
    {
        $lab = $this->repository->findById($labId);
        if (!$lab) {
            throw new InvalidArgumentException('Lab not found');
        }

        // If a file already exists for this lab, delete it before uploading the new one.
        if (!empty($lab['fileName'])) {
            $this->fileHelper->deleteFile($this->filesDir, $lab['fileName']);
        }
        
        $allowedMimes = ['application/pdf'];
        $fileName = $this->fileHelper->upload($file, $this->filesDir, $allowedMimes);

        $updateData = [
            'fileName' => $fileName,
            'url' => $this->resourceDir . '/' . $fileName,
        ];

        $updatedLab = $this->repository->partialUpdate($labId, $updateData);

        if (!$updatedLab) {
            // This case should ideally not be reached if findById succeeded.
            throw new RuntimeException('Failed to update lab data after file upload.');
        }
        
        return $updatedLab;
    }
}
