<?php

class FileHelper
{
    /**
     * Safely uploads a file to a specified directory.
     *
     * @param array $file The file array from $_FILES.
     * @param string $destinationDir The absolute path to the destination directory.
     * @param array $allowedMimes Optional array of allowed MIME types.
     * @return string|null The sanitized file name on success, null on failure.
     */
    public function upload(array $file, string $destinationDir, array $allowedMimes = []): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('File upload error: ' . $file['error']);
        }

        $tmpPath = $file['tmp_name'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpPath);

        if (!empty($allowedMimes) && !in_array($mime, $allowedMimes, true)) {
            throw new InvalidArgumentException('Invalid file type: ' . $mime);
        }

        $fileName = $this->sanitizeFileName($file['name']);
        if (empty($fileName)) {
            throw new InvalidArgumentException('Invalid or empty file name provided.');
        }

        if (!is_dir($destinationDir) && !mkdir($destinationDir, 0775, true)) {
             throw new RuntimeException("Failed to create destination directory: {$destinationDir}");
        }

        $destinationPath = $destinationDir . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file($tmpPath, $destinationPath)) {
            throw new RuntimeException('Failed to move uploaded file.');
        }

        return $fileName;
    }

    /**
     * Unpacks a ZIP archive to a specified directory.
     *
     * @param string $zipPath The absolute path to the ZIP file.
     * @param string $destinationDir The absolute path to the extraction directory.
     * @return bool True on success, false on failure.
     */
    public function unpackZip(string $zipPath, string $destinationDir): bool
    {
        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            return false;
        }

        if (!is_dir($destinationDir) && !mkdir($destinationDir, 0775, true)) {
             $zip->close();
             return false;
        }
        
        $zip->extractTo($destinationDir);
        $zip->close();
        
        unlink($zipPath);

        return true;
    }

    /**
     * Sanitizes a file name to prevent directory traversal and other attacks.
     *
     * @param string $fileName The original file name.
     * @return string The sanitized file name.
     */
    public function sanitizeFileName(string $fileName): string
    {
        // Basic sanitization
        $fileName = basename($fileName);
        // Remove characters that are illegal in Windows and Linux filenames
        $fileName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '', $fileName);
        // Prevent path traversal
        $fileName = trim($fileName, '.-_');

        return $fileName;
    }
     
    /**
     * Deletes a file safely.
     *
     * @param string $baseDir The base directory where files are stored.
     * @param string $relativePath The relative path to the file from the base directory.
     * @return bool True on success, false on failure.
     */
    public function deleteFile(string $baseDir, string $relativePath): bool
    {
        $fullPath = realpath($baseDir . DIRECTORY_SEPARATOR . $relativePath);

        // Security check: ensure the file is within the intended directory
        if ($fullPath === false || strpos($fullPath, realpath($baseDir)) !== 0) {
            return false;
        }

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return true; // File doesn't exist, so it's "deleted"
    }
}
