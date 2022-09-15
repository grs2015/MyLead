<?php

namespace App\DataTransferObjects;

class ProductData
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description
    ) {  }
}
