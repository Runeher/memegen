<?php

// class/ImageSource.php
namespace XoopsModules\Memegen;

abstract class ImageSource
{
    abstract public function getImageData(): array;
    abstract public function validate(): bool;
    abstract public function cleanup(): void;
}

