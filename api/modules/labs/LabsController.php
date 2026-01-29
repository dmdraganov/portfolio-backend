<?php
require_once dirname(__DIR__, 2) . '/core/BaseController.php';
require_once __DIR__ . '/LabsService.php';

class LabsController extends BaseController
{
    private LabsService $labsService;

    public function __construct()
    {
        parent::__construct('labs.json', 'Lab', 'lab-');
        $this->labsService = new LabsService();
    }

    public function uploadFile(Request $request, Response $response, array $params): void
    {
        $file = $request->getFiles()['file'] ?? null;

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $response->error('File not provided or upload error.', 400);
            return;
        }

        try {
            $updatedLab = $this->labsService->handleFileUpload($params['id'], $file);
            $response->json($updatedLab);
        } catch (InvalidArgumentException $e) {
            $response->error($e->getMessage(), 404);
        } catch (RuntimeException $e) {
            $response->error($e->getMessage(), 500);
        } catch (Exception $e) {
            $response->error('An unexpected error occurred.', 500);
        }
    }
}
