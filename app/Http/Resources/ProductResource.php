<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\Link;
use App\Http\Resources\PriceResource;
use TiMacDonald\JsonApi\JsonApiResource;
use App\Http\Controllers\ProductController;

class ProductResource extends JsonApiResource
{
    protected function toAttributes(Request $request): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    protected function toRelationships(Request $request): array
    {
        return [
            'prices' => fn () => PriceResource::collection($this->prices)
        ];
    }

    protected function toLinks(Request $request): array
    {
        return [
            Link::self(action([ProductController::class, 'show'], $this->resource)),
        ];
    }
}
