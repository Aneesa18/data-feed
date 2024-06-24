<?php

// src/Storage/StorageInterface.php
namespace App\Storage;

// using interface to support Dependency Inversion Principle
interface StorageInterface
{
    public function save(array $data): void;
}
