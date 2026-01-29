<?php
require_once dirname(__DIR__, 2) . '/core/BaseController.php';
require_once __DIR__ . '/SitesService.php';

class SitesController extends BaseController
{
    private SitesService $sitesService;

    public function __construct()
    {
        parent::__construct('sites.json', 'Site', 'site-');
        $this->sitesService = new SitesService();
    }

    public function uploadZip(Request $request, Response $response, array $params): void
    {
        $file = $request->getFiles()['file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $response->error('ZIP file not provided or upload error.', 400);
            return;
        }

        try {
            $updatedSite = $this->sitesService->handleZipUpload($params['id'], $file);
            $response->json($updatedSite);
        } catch (InvalidArgumentException $e) {
            $response->error($e->getMessage(), 404);
        } catch (RuntimeException $e) {
            $response->error($e->getMessage(), 500);
        }
    }

    public function addReference(Request $request, Response $response, array $params): void
    {
        $file = $request->getFiles()['file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $response->error('Reference image file not provided or upload error.', 400);
            return;
        }
        
        // As per the service implementation, we expect a pageId in the POST body.
        $pageId = $request->getPostParams()['pageId'] ?? null;
        if (!$pageId) {
            $response->error('`pageId` must be provided in the form data.', 400);
            return;
        }

        try {
            $updatedSite = $this->sitesService->addReference($params['id'], $pageId, $file);
            $response->json($updatedSite);
        } catch (InvalidArgumentException $e) {
            $response->error($e->getMessage(), 404);
        } catch (RuntimeException $e) {
            $response->error($e->getMessage(), 500);
        }
    }

    public function deleteReference(Request $request, Response $response, array $params): void
    {
        $fileName = $params['fileName'] ?? null;
        if (!$fileName) {
            $response->error('File name parameter is missing from the URL.', 400);
            return;
        }

        try {
            $updatedSite = $this->sitesService->deleteReference($params['id'], $fileName);
            $response->json($updatedSite);
        } catch (InvalidArgumentException $e) {
            $response->error($e->getMessage(), 404);
        } catch (RuntimeException $e) {
            $response->error($e->getMessage(), 500);
        }
    }
}
