<?php

class JsonRepository
{
    private string $filePath;

    public function __construct(string $fileName)
    {
        // Use the globally defined DATA_PATH constant
        $this->filePath = DATA_PATH . DIRECTORY_SEPARATOR . $fileName;
        
        if (!file_exists($this->filePath)) {
            // Attempt to create the file if it doesn't exist, assuming it's intended
            if (file_put_contents($this->filePath, '[]') === false) {
                 throw new RuntimeException("Data file not found and could not be created: {$fileName}");
            }
        }
    }

    private function readData(): array
    {
        $json = file_get_contents($this->filePath);
        if ($json === false) {
            throw new RuntimeException("Could not read data file: {$this->filePath}");
        }
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Invalid JSON in file: {$this->filePath}");
        }
        return $data ?: [];
    }

    private function writeData(array $data): bool
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new RuntimeException("Could not encode data to JSON.");
        }
        return file_put_contents($this->filePath, $json, LOCK_EX) !== false;
    }

    private function generateId(string $prefix = ''): string
    {
        return $prefix . uniqid(bin2hex(random_bytes(6)));
    }

    public function findAll(): array
    {
        return $this->readData();
    }

    public function findById(string $id): ?array
    {
        $items = $this->readData();
        foreach ($items as $item) {
            if (isset($item['id']) && $item['id'] === $id) {
                return $item;
            }
        }
        return null;
    }

    public function create(array $data, string $idPrefix = ''): array
    {
        $items = $this->readData();
        $newItem = $data;
        $newItem['id'] = $this->generateId($idPrefix);
        $items[] = $newItem;
        
        if (!$this->writeData($items)) {
            throw new RuntimeException("Failed to write data after create.");
        }
        
        return $newItem;
    }

    public function update(string $id, array $data): ?array
    {
        $items = $this->readData();
        $found = false;
        foreach ($items as $i => $item) {
            if (isset($item['id']) && $item['id'] === $id) {
                // Completely replace the old item with the new data, but keep the ID
                $items[$i] = $data;
                $items[$i]['id'] = $id;
                $found = true;
                break;
            }
        }

        if ($found) {
            if (!$this->writeData($items)) {
                throw new RuntimeException("Failed to write data after update.");
            }
            return $items[$i];
        }

        return null;
    }
    
    public function partialUpdate(string $id, array $data): ?array
    {
        $items = $this->readData();
        $found = false;
        foreach ($items as $i => $item) {
            if (isset($item['id']) && $item['id'] === $id) {
                // Merge new data into the existing item
                $items[$i] = array_merge($item, $data);
                $items[$i]['id'] = $id; // Ensure ID remains the same
                $found = true;
                break;
            }
        }
        
        if ($found) {
            if (!$this->writeData($items)) {
                throw new RuntimeException("Failed to write data after partial update.");
            }
            return $items[$i];
        }

        return null;
    }

    public function delete(string $id): bool
    {
        $items = $this->readData();
        $initialCount = count($items);
        
        $items = array_filter($items, function ($item) use ($id) {
            return !isset($item['id']) || $item['id'] !== $id;
        });

        if (count($items) < $initialCount) {
            // Re-index the array to prevent it from becoming an object in JSON
            return $this->writeData(array_values($items));
        }

        return false;
    }
}
