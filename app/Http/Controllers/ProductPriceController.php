<?php

namespace App\Http\Controllers;

use App\Models\Price;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\PriceResource;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Requests\ProductPriceRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductPriceController extends Controller
{
    public function index(Product $product): AnonymousResourceCollection
    {
        $prices = QueryBuilder::for(Price::class)
            ->allowedFilters(['group_description'])
            ->allowedSorts(['priceA', 'priceB', 'priceC'])
            ->whereBelongsTo($product)
            ->get();

        return PriceResource::collection($prices);
    }
}
