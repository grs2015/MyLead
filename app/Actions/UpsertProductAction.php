<?php

namespace App\Actions;

use App\DataTransferObjects\ProductData;
use App\Models\Product;

class UpsertProductAction
{
    public static function execute(Product $product, ProductData $productData): Product
    {
        return Product::updateOrCreate(
            ['id' => $product->id],
            ['title' => $productData->title, 'description' => $productData->description]
        );
    }
}
