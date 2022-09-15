<?php

namespace App\DataTransferObjects;

use App\Models\Product;
use App\Http\Requests\UpsertPriceRequest;

class PriceData
{
    public function __construct(
        public readonly string $group_description,
        public readonly int $priceA,
        public readonly int $priceB,
        public readonly int $priceC,
        public readonly Product $product
    ) {}

    public static function fromRequest(UpsertPriceRequest $request): self
    {
        return new static(
            $request->group_description,
            $request->priceA,
            $request->priceB,
            $request->priceC,
            $request->getProduct()
        );
    }
}
