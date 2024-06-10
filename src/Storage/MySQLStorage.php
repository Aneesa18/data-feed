<?php

namespace App\Storage;

use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;

class MySQLStorage implements StorageInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(array $data): void
    {
        $item = new Item();
        $this->setProperties($item, $data);
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    private function setProperties(Item $item, array $data): void
    {
        foreach ($data as $key => $value) {
            // Converting the key from snake_case or UpperCamelCase to camelCase
            $propertyName = lcfirst(str_replace('_', '', ucwords($key, '_')));
            $setterMethod = 'set' . ucfirst($propertyName);
            if (method_exists($item, $setterMethod)) {
                $item->$setterMethod($value);
            }
        }
    }
}
