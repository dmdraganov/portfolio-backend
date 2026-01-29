<?php
require_once dirname(__DIR__, 2) . '/core/JsonRepository.php';
require_once dirname(__DIR__, 2) . '/core/FileHelper.php';

class EssaysService
{
    private JsonRepository $repository;
    private FileHelper $fileHelper;
    private string $filesDir;
    private string $resourceDir = 'essays';

    public function __construct()
    {
        $this->repository = new JsonRepository('essays.json');
        $this->fileHelper = new FileHelper();
        $this->filesDir = FILES_PATH . DIRECTORY_SEPARATOR . $this->resourceDir;
    }

    public function handleFileUpload(string $essayId, array $file): array
    {
        $essay = $this->repository->findById($essayId);
        if (!$essay) {
            throw new InvalidArgumentException('Essay not found');
        }

        if (!empty($essay['fileName'])) {
            $this->fileHelper->deleteFile($this->filesDir, $essay['fileName']);
        }
        
        $allowedMimes = ['application/pdf'];
        $fileName = $this->fileHelper->upload($file, $this->filesDir, $allowedMimes);

        $updateData = [
            'fileName' => $fileName,
            'url' => $this->resourceDir . '/' . $fileName,
        ];

        $updatedEssay = $this->repository->partialUpdate($essayId, $updateData);

        if (!$updatedEssay) {
            throw new RuntimeException('Failed to update essay data after file upload.');
        }
        
        return $updatedEssay;
    }
}
