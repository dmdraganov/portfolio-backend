<?php
require_once dirname(__DIR__, 2) . '/core/JsonRepository.php';
require_once dirname(__DIR__, 2) . '/core/FileHelper.php';

class PracticesService
{
    private JsonRepository $repository;
    private FileHelper $fileHelper;
    private string $filesDir;
    private string $resourceDir = 'practices';

    public function __construct()
    {
        $this->repository = new JsonRepository('practices.json');
        $this->fileHelper = new FileHelper();
        $this->filesDir = FILES_PATH . DIRECTORY_SEPARATOR . $this->resourceDir;
    }

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function handleFileUpload(string $practiceId, array $file): array
    {
        $practice = $this->repository->findById($practiceId);
        if (!$practice) {
            throw new InvalidArgumentException('Practice not found');
        }

        if (!empty($practice['fileName'])) {
            $this->fileHelper->deleteFile($this->filesDir, $practice['fileName']);
        }
        
        $allowedMimes = ['application/pdf'];
        $fileName = $this->fileHelper->upload($file, $this->filesDir, $allowedMimes);

        $updateData = [
            'fileName' => $fileName,
            'url' => $this->resourceDir . '/' . $fileName,
        ];

        $updatedPractice = $this->repository->partialUpdate($practiceId, $updateData);

        if (!$updatedPractice) {
            throw new RuntimeException('Failed to update practice data after file upload.');
        }
        
        return $updatedPractice;
    }
}
