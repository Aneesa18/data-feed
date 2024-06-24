<?php
// src/Storage/StorageRegistry.php
namespace App\Storage;

use InvalidArgumentException;

class StorageRegistry
{
    private array $storages;

    public function __construct(array $storages)
    {
        $this->storages = $storages;
    }

    public function getStorage(string $type): StorageInterface
    {
        if (!isset($this->storages[$type])) {
            throw new InvalidArgumentException("Storage type '$type' is not registered.");
        }

        return $this->storages[$type];
    }
}
