<?php

// src/Storage/StorageInterface.php
namespace App\Storage;

interface StorageInterface
{
    public function save(array $data): void;
}
