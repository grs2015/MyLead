<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Actions\DeletePriceAction;
use App\Actions\UpsertPriceAction;
use App\Http\Resources\PriceResource;
use App\DataTransferObjects\PriceData;
use App\Http\Requests\UpsertPriceRequest;
use Illuminate\Http\Response as HttpResponse;

class PriceController extends Controller
{
    public function __construct(
        private readonly UpsertPriceAction $upsertPrice
    ) {}

    public function store(UpsertPriceRequest $request): JsonResponse
    {
        return PriceResource::make($this->upsert($request, new Price()))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpsertPriceRequest $request, Price $price): HttpResponse
    {
        $this->upsert($request, $price);
        return response()->noContent();
    }

    public function destroy(Price $price): HttpResponse
    {
        $ee = $price;

        DeletePriceAction::execute($price);
        return response()->noContent();
    }

    private function upsert(UpsertPriceRequest $request, Price $price): Price
    {
        $priceData = PriceData::fromRequest($request);
        return $this->upsertPrice::execute($price, $priceData);
    }
}
