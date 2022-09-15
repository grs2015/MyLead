<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Actions\DeleteProductAction;
use App\Actions\UpsertProductAction;
use App\Actions\GetPaginatedProducts;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\ProductResource;
use App\DataTransferObjects\ProductData;
use App\Http\Requests\UpsertProductRequest;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private readonly UpsertProductAction $upsertProduct
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $products = QueryBuilder::for(Product::class)
            ->allowedFilters(['title', 'description'])
            ->allowedSorts(['title', 'description'])
            ->allowedIncludes(['prices'])
            ->get();

        return ProductResource::collection(GetPaginatedProducts::execute($products));
    }

    public function show(Product $product): ProductResource
    {
        return ProductResource::make($product);
    }

    public function store(UpsertProductRequest $request): JsonResponse
    {
        return ProductResource::make($this->upsert($request, new Product()))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpsertProductRequest $request, Product $product): HttpResponse
    {
        $this->upsert($request, $product);
        return response()->noContent();
    }

    public function destroy(Product $product): HttpResponse
    {
        DeleteProductAction::execute($product);
        return response()->noContent();
    }

    private function upsert(UpsertProductRequest $request, Product $product): Product
    {
        $productData = new ProductData(...$request->validated());
        return $this->upsertProduct::execute($product, $productData);
    }
}
