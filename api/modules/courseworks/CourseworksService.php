<?php
require_once dirname(__DIR__, 2) . '/core/JsonRepository.php';
require_once dirname(__DIR__, 2) . '/core/FileHelper.php';

class CourseworksService
{
    private JsonRepository $repository;
    private FileHelper $fileHelper;
    private string $filesDir;
    private string $resourceDir = 'courseworks';

    public function __construct()
    {
        $this->repository = new JsonRepository('courseworks.json');
        $this->fileHelper = new FileHelper();
        $this->filesDir = FILES_PATH . DIRECTORY_SEPARATOR . $this->resourceDir;
    }

    public function handleFileUpload(string $courseworkId, array $file): array
    {
        $coursework = $this->repository->findById($courseworkId);
        if (!$coursework) {
            throw new InvalidArgumentException('Coursework not found');
        }

        if (!empty($coursework['fileName'])) {
            $this->fileHelper->deleteFile($this->filesDir, $coursework['fileName']);
        }
        
        $allowedMimes = ['application/pdf'];
        $fileName = $this->fileHelper->upload($file, $this->filesDir, $allowedMimes);

        $updateData = [
            'fileName' => $fileName,
            'url' => $this->resourceDir . '/' . $fileName,
        ];

        $updatedCoursework = $this->repository->partialUpdate($courseworkId, $updateData);

        if (!$updatedCoursework) {
            throw new RuntimeException('Failed to update coursework data after file upload.');
        }
        
        return $updatedCoursework;
    }
}
