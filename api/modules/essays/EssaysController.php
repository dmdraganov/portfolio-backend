<?php
require_once dirname(__DIR__, 2) . '/core/BaseController.php';
require_once __DIR__ . '/EssaysService.php';

class EssaysController extends BaseController
{
    private EssaysService $essaysService;

    public function __construct()
    {
        parent::__construct('essays.json', 'Essay', 'essay-');
        $this->essaysService = new EssaysService();
    }

    public function uploadFile(Request $request, Response $response, array $params): void
    {
        $file = $request->getFiles()['file'] ?? null;

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $response->error('File not provided or upload error.', 400);
            return;
        }

        try {
            $updatedEssay = $this->essaysService->handleFileUpload($params['id'], $file);
            $response->json($updatedEssay);
        } catch (InvalidArgumentException $e) {
            $response->error($e->getMessage(), 404);
        } catch (RuntimeException $e) {
            $response->error($e->getMessage(), 500);
        }
    }
}
