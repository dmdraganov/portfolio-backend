<?php
require_once dirname(__DIR__, 2) . '/core/BaseController.php';
require_once __DIR__ . '/CourseworksService.php';

class CourseworksController extends BaseController
{
    private CourseworksService $courseworksService;

    public function __construct()
    {
        parent::__construct('courseworks.json', 'Coursework', 'coursework-');
        $this->courseworksService = new CourseworksService();
    }

    public function uploadFile(Request $request, Response $response, array $params): void
    {
        $file = $request->getFiles()['file'] ?? null;

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $response->error('File not provided or upload error.', 400);
            return;
        }

        try {
            $updatedCoursework = $this->courseworksService->handleFileUpload($params['id'], $file);
            $response->json($updatedCoursework);
        } catch (InvalidArgumentException $e) {
            $response->error($e->getMessage(), 404);
        } catch (RuntimeException $e) {
            $response->error($e->getMessage(), 500);
        }
    }
}
