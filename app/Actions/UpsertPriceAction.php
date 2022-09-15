<?php

namespace App\Actions;

use App\Models\Price;
use App\DataTransferObjects\PriceData;

class UpsertPriceAction
{
    public static function execute(Price $price, PriceData $priceData): Price
    {
        return Price::updateOrCreate(
            ['id' => $price->id],
            [
                'group_description' => $priceData->group_description,
                'priceA' => $priceData->priceA,
                'priceB' => $priceData->priceB,
                'priceC' => $priceData->priceC,
                'product_id' => $priceData->product->id
            ]
        );
    }
}
