<?php
// src/Storage/StorageRegistry.php
namespace App\Storage;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StorageRegistry
{
    private ContainerInterface $container;
    private array $storages = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addStorage(string $name, string $serviceId): void
    {
        $this->storages[$name] = $serviceId;
    }

    public function getStorage(string $name): StorageInterface
    {
        if (!isset($this->storages[$name])) {
            throw new InvalidArgumentException("Storage type '$name' is not registered.");
        }

        $storage = $this->container->get($this->storages[$name]);

        if (!$storage instanceof StorageInterface) {
            throw new RuntimeException("Service '{$this->storages[$name]}' does not implement StorageInterface.");
        }

        return $storage;
    }
}
