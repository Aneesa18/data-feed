<?php

// src/Service/FileProcessing/XmlFileProcessor.php
namespace App\Service\FileProcessing;

use DOMNode;
use InvalidArgumentException;
use XMLReader;

/* A new FileProcessor can be used by simply implementing FileProcessorInterface
So, the processors can be used interchangeably without changing the correctness
Hence, it supports Liskov Substitution Principle
*/
class XmlFileProcessor implements FileProcessorInterface
{
    public function process(string $filePath): iterable
    {
        //streaming is used to support large files
        $reader = new XMLReader();

        if (!$reader->open($filePath)) {
            throw new InvalidArgumentException("Failed to open XML file: $filePath");
        }

        try {
            while ($reader->read()) {
                if ($reader->nodeType === XMLReader::ELEMENT && $reader->name === 'item') {
                    $node = $reader->expand();
                    yield $this->parseRecord($node);
                }
            }
        } finally {
            $reader->close();
        }
    }

    private function parseRecord(DOMNode $node): array
    {
        $record = [];

        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XMLReader::ELEMENT) {
                $record[$child->nodeName] = $child->textContent;
            }
        }

        return $record;
    }

    public function getType(): string
    {
        return 'xml';
    }
}
