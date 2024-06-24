<?php

// src/Service/FileProcessing/FileProcessorFactory.php
namespace App\Service\FileProcessing;

use InvalidArgumentException;

class FileProcessorFactory
{
    private array $processors;

    public function __construct(iterable $processors)
    {
        foreach ($processors as $processor) {
            $this->processors[$processor->getType()] = $processor;
        }
    }

    public function create(string $type): FileProcessorInterface
    {
        if (!isset($this->processors[$type])) {
            throw new InvalidArgumentException("File type '$type' is not registered.");
        }

        return $this->processors[$type];
    }
}
