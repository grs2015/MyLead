<?php

use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

uses()->group('products');

/* ------------------------------ @index method ----------------------------- */
it('renders the list of products', function() {
    $products = Product::factory()->count(5)->create();

    $product = $products->first();

    getJson(action([ProductController::class, 'index']))
        ->assertSuccessful()
        ->assertJson(function(AssertableJson $json) use ($product) {
            $json
                ->has('data', 5)
                ->has('data.0', function(AssertableJson $json) use ($product) {
                    $json
                        ->has('id')
                        ->where('id', $product->uuid)
                        ->has('type')
                        ->where('type', 'products')
                        ->has('attributes.title')
                        ->where('attributes.title', $product->title)
                        ->has('attributes.description')
                        ->where('attributes.description', $product->description)
                        ->etc();
                })
                ->etc();
        });
});

/* ------------------------------ @show method ------------------------------ */
it('renders the single product entry', function() {
    $singleProduct = Product::factory()->create();

    getJson(action([ProductController::class, 'show'], $singleProduct))
        ->assertSuccessful()
        ->assertJsonPath('data.id', $singleProduct->uuid)
        ->assertJsonPath('data.type', 'products')
        ->assertJsonPath('data.attributes.title', $singleProduct->title)
        ->assertJsonPath('data.attributes.description', $singleProduct->description);
});

/* ------------------------------ @store method ----------------------------- */
it('checks the correct response code', function() {
    $this->withoutExceptionHandling();

    $productData = [
        'title' => 'Product title',
        'description' => 'Product description'
    ];

    postJson(action([ProductController::class, 'store'], $productData))->assertStatus(201);
});

it('checks the correct response after successful storing', function () {
    $this->withoutExceptionHandling();

    $productData = [
        'title' => 'Product title',
        'description' => 'Product description'
    ];

    postJson(action([ProductController::class, 'store'], $productData))
        ->assertJsonPath('data.attributes.title', $productData['title'])
        ->assertJsonPath('data.attributes.description', $productData['description']);
});

it('checks the 422 response if data validation fails by different reasons', function(?string $title) {
    Product::factory()->create(['title' => 'Product Title', 'description' => 'Product Description']);

    $productData = [
        'title' => $title,
        'description' => 'Product Description'
    ];

    postJson(action([ProductController::class, 'store'], $productData))->assertStatus(422)->assertInvalid('title');
})->with(['', null, 'Product Title']);

it('checks the stored product in database', function() {
    $productData = [
        'title' => 'Product title',
        'description' => 'Product description'
    ];

    postJson(action([ProductController::class, 'store'], $productData))->assertStatus(201);

    $this->assertDatabaseHas('products', ['title' => 'Product title', 'description' => 'Product description']);
});

/* ----------------------------- @update method ----------------------------- */
it('checks the empty response body in case of successful update', function() {
    //Arrange #1
    $productData = [
        'title' => 'Product title',
        'description' => 'Product description'
    ];
    //Action & Assert #1
    postJson(action([ProductController::class, 'store'], $productData))->assertStatus(201);
    $product = Product::first();
    //Arrange #2
    $productData = [
        'title' => 'Updated product title',
        'description' => 'Updated product description'
    ];
    //Action & Assert #2
    putJson(action([ProductController::class, 'update'], $product), $productData)->assertNoContent();
});

it('checks the correct empty response after successful updating', function(string $title, string $description) {
    //Arrange #1
    $productData = [
        'title' => 'Product title',
        'description' => 'Product description'
    ];
    //Action & Assert #1
    postJson(action([ProductController::class, 'store'], $productData))->assertStatus(201);
    $product = Product::first();
    //Arrange #2
    $productData = [
        'title' => $title,
        'description' => $description
    ];
    //Action & Assert #2
    putJson(action([ProductController::class, 'update'], $product), $productData)->assertNoContent();
})->with([
    ['title' => 'Product title', 'description' => 'Product description'],
    ['title' => 'Updated product title', 'description' => 'Updated product description']
]);

it('checks the updated product in database', function() {
    //Arrange #1
    $productData = [
        'title' => 'Product title',
        'description' => 'Product description'
    ];
    //Action & Assert #1
    postJson(action([ProductController::class, 'store'], $productData))->assertStatus(201);
    $this->assertDatabaseHas('products', ['title' => 'Product title', 'description' => 'Product description']);
    $product = Product::first();
    //Arrange #2
    $productData = [
        'title' => 'Updated product title',
        'description' => 'Updated product description'
    ];
    //Action & Assert #2
    putJson(action([ProductController::class, 'update'], $product), $productData)->assertNoContent();
    $this->assertDatabaseMissing('products', ['title' => 'Product title', 'description' => 'Product description']);
    $this->assertDatabaseHas('products', ['title' => 'Updated product title', 'description' => 'Updated product description']);
});

/* ----------------------------- @destroy method ---------------------------- */
it('checks the deletion of entry with related models', function() {
    //Arrange #1
    $productData = [
        'title' => 'Product title',
        'description' => 'Product description'
    ];
    //Action & Assert #1
    postJson(action([ProductController::class, 'store'], $productData))->assertStatus(201);
    $this->assertDatabaseHas('products', ['title' => 'Product title', 'description' => 'Product description']);
    $uuid = Product::first()->uuid;

    deleteJson(action([ProductController::class, 'destroy'], ['product' => $uuid]))->assertNoContent();
    $this->assertDatabaseMissing('products', ['title' => 'Product title', 'description' => 'Product description']);
});
