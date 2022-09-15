<?php

namespace App\Actions;

use App\Models\Product;
use Blueprint\Contracts\Model;

class DeleteProductAction
{
    public static function execute(Product $product): void
    {
        $product->delete();
    }
}
