<?php

// src/Service/FileProcessing/FileProcessorInterface.php
namespace App\Service\FileProcessing;

// The interface is smaller and more focused without any unnecessary methods
// Hence it supports the Interface Segregation Principle
interface FileProcessorInterface
{
    public function process(string $filePath): iterable;

    public function getType(): string;
}
