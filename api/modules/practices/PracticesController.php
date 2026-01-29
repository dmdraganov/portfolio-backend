<?php
require_once dirname(__DIR__, 2) . '/core/BaseController.php';
require_once __DIR__ . '/PracticesService.php';

class PracticesController extends BaseController
{
    private PracticesService $practicesService;

    public function __construct()
    {
        parent::__construct('practices.json', 'Practice', 'practice-');
        $this->practicesService = new PracticesService();
    }

    public function uploadFile(Request $request, Response $response, array $params): void
    {
        $file = $request->getFiles()['file'] ?? null;

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $response->error('File not provided or upload error.', 400);
            return;
        }

        try {
            $updatedPractice = $this->practicesService->handleFileUpload($params['id'], $file);
            $response->json($updatedPractice);
        } catch (InvalidArgumentException $e) {
            $response->error($e->getMessage(), 404);
        } catch (RuntimeException $e) {
            $response->error($e->getMessage(), 500);
        }
    }
}
