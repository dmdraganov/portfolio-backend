<?php
require_once dirname(__DIR__, 2) . '/core/BaseController.php';
require_once __DIR__ . '/CompensatoryWorksService.php';

class CompensatoryWorksController extends BaseController
{
    private CompensatoryWorksService $compensatoryWorksService;

    public function __construct()
    {
        parent::__construct('compensatory-works.json', 'Compensatory Work', 'comp-work-');
        $this->compensatoryWorksService = new CompensatoryWorksService();
    }

    public function uploadFile(Request $request, Response $response, array $params): void
    {
        $file = $request->getFiles()['file'] ?? null;

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $response->error('File not provided or upload error.', 400);
            return;
        }

        try {
            $updatedWork = $this->compensatoryWorksService->handleFileUpload($params['id'], $file);
            $response->json($updatedWork);
        } catch (InvalidArgumentException $e) {
            $response->error($e->getMessage(), 404);
        } catch (RuntimeException $e) {
            $response->error($e->getMessage(), 500);
        }
    }
}
