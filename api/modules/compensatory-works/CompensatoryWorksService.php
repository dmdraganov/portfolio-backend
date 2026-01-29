<?php
require_once dirname(__DIR__, 2) . '/core/JsonRepository.php';
require_once dirname(__DIR__, 2) . '/core/FileHelper.php';

class CompensatoryWorksService
{
    private JsonRepository $repository;
    private FileHelper $fileHelper;
    private string $filesDir;
    private string $resourceDir = 'compensatory-works';

    public function __construct()
    {
        $this->repository = new JsonRepository('compensatory-works.json');
        $this->fileHelper = new FileHelper();
        $this->filesDir = FILES_PATH . DIRECTORY_SEPARATOR . $this->resourceDir;
    }

    public function handleFileUpload(string $workId, array $file): array
    {
        $work = $this->repository->findById($workId);
        if (!$work) {
            throw new InvalidArgumentException('Compensatory work not found');
        }

        if (!empty($work['fileName'])) {
            $this->fileHelper->deleteFile($this->filesDir, $work['fileName']);
        }
        
        $allowedMimes = ['application/pdf'];
        $fileName = $this->fileHelper->upload($file, $this->filesDir, $allowedMimes);

        $updateData = [
            'fileName' => $fileName,
            'url' => $this->resourceDir . '/' . $fileName,
        ];

        $updatedWork = $this->repository->partialUpdate($workId, $updateData);

        if (!$updatedWork) {
            throw new RuntimeException('Failed to update compensatory work data after file upload.');
        }
        
        return $updatedWork;
    }
}
