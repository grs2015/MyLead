<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class PriceResource extends JsonApiResource
{
    protected function toAttributes(Request $request): array
    {
        return [
            'group_description' => $this->group_description,
            'priceA' => $this->priceA,
            'priceB' => $this->priceB,
            'priceC' => $this->priceC
        ];
    }


}
