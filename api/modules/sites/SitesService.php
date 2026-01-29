<?php
require_once dirname(__DIR__, 2) . '/core/JsonRepository.php';
require_once dirname(__DIR__, 2) . '/core/FileHelper.php';

class SitesService
{
    private JsonRepository $repository;
    private FileHelper $fileHelper;

    public function __construct()
    {
        $this->repository = new JsonRepository('sites.json');
        $this->fileHelper = new FileHelper();
    }

    /**
     * @throws InvalidArgumentException|RuntimeException
     */
    public function handleZipUpload(string $siteId, array $file): array
    {
        $site = $this->repository->findById($siteId);
        if (!$site) {
            throw new InvalidArgumentException('Site not found');
        }
        $slug = $site['slug'] ?? $siteId;
        $siteDir = FILES_PATH . DIRECTORY_SEPARATOR . 'sites' . DIRECTORY_SEPARATOR . $slug;

        // Clean directory before unpacking new version
        if (is_dir($siteDir)) {
            // A simple recursive delete. For production, a more robust method is needed.
            system('rm -rf ' . escapeshellarg($siteDir));
        }

        // Use a temporary file for the upload
        $tempDir = sys_get_temp_dir();
        $uploadedFileName = $this->fileHelper->upload($file, $tempDir, ['application/zip', 'application/x-zip-compressed']);
        $zipPath = $tempDir . DIRECTORY_SEPARATOR . $uploadedFileName;

        if (!$this->fileHelper->unpackZip($zipPath, $siteDir)) {
            throw new RuntimeException('Failed to unpack ZIP file.');
        }

        // Scan for HTML files and build the pages array
        $pages = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($siteDir, FilesystemIterator::SKIP_DOTS));
        
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile() && strtolower($fileInfo->getExtension()) === 'html') {
                $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', substr($fileInfo->getPathname(), strlen($siteDir) + 1));
                $pages[] = [
                    'id' => 'page-' . bin2hex(random_bytes(6)),
                    'name' => pathinfo($fileInfo->getFilename(), PATHINFO_FILENAME),
                    'url' => 'sites/' . $slug . '/' . $relativePath,
                    'references' => [], // References are managed separately
                ];
            }
        }

        return $this->repository->partialUpdate($siteId, ['pages' => $pages]);
    }

    /**
     * @throws InvalidArgumentException|RuntimeException
     */
    public function addReference(string $siteId, string $pageId, array $file): array
    {
        $site = $this->repository->findById($siteId);
        if (!$site) {
            throw new InvalidArgumentException('Site not found');
        }

        $pageIndex = -1;
        foreach ($site['pages'] as $index => $page) {
            if ($page['id'] === $pageId) {
                $pageIndex = $index;
                break;
            }
        }

        if ($pageIndex === -1) {
            throw new InvalidArgumentException('Page with the given ID not found in this site.');
        }

        $slug = $site['slug'] ?? $siteId;
        $refDir = FILES_PATH . DIRECTORY_SEPARATOR . 'references' . DIRECTORY_SEPARATOR . 'sites' . DIRECTORY_SEPARATOR . $slug;
        
        $fileName = $this->fileHelper->upload($file, $refDir, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
        $publicRefPath = 'references/sites/' . $slug . '/' . $fileName;

        // Add the new reference path to the page's references array
        $site['pages'][$pageIndex]['references'][] = $publicRefPath;

        return $this->repository->update($siteId, $site);
    }
    
    /**
     * @throws InvalidArgumentException|RuntimeException
     */
    public function deleteReference(string $siteId, string $fileName): array
    {
        $site = $this->repository->findById($siteId);
        if (!$site) {
            throw new InvalidArgumentException('Site not found');
        }
        $slug = $site['slug'] ?? $siteId;
        
        $publicRefPath = 'references/sites/' . $slug . '/' . $fileName;
        $fullFilePath = FILES_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $publicRefPath);

        // Delete the physical file
        if (file_exists($fullFilePath)) {
            unlink($fullFilePath);
        }

        // Remove the reference from the JSON data
        $refFound = false;
        foreach ($site['pages'] as &$page) {
            if (in_array($publicRefPath, $page['references'])) {
                $page['references'] = array_values(array_filter($page['references'], fn($ref) => $ref !== $publicRefPath));
                $refFound = true;
            }
        }
        
        if (!$refFound) {
            // If the reference wasn't in the JSON, no need to update the file.
            // We still consider it a success because the desired state is achieved.
            return $site;
        }

        return $this->repository->update($siteId, $site);
    }
}
