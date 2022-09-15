<?php

namespace App\Actions;

use App\Models\Price;

class DeletePriceAction
{
    public static function execute(Price $price): void
    {
        $price->delete();
    }
}
