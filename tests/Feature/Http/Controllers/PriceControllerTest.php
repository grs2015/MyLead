<?php

use App\Models\User;
use App\Models\Price;
use App\Models\Product;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\getJson;
use function Pest\Laravel\putJson;

use function Pest\Laravel\postJson;
use function Pest\Laravel\deleteJson;
use App\Http\Controllers\PriceController;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Http\Controllers\ProductPriceController;

uses()->group('prices');

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create(), ['*']);
});

/* ------------------------------ @index method ----------------------------- */
it('renders the list of product prices when logged-in', function() {
    $product = Product::factory()->hasPrices(3)->create();
    $price = Price::first();

    getJson(action([ProductPriceController::class, 'index'], $product))
        ->assertSuccessful()
        ->assertJson(function(AssertableJson $json) use ($price) {
            $json
                ->has('data', 3)
                ->has('data.0', function(AssertableJson $json) use ($price) {
                    $json
                        ->has('id')
                        ->where('id', $price->uuid)
                        ->has('type')
                        ->where('type', 'prices')
                        ->has('attributes.group_description')
                        ->where('attributes.group_description', $price->group_description)
                        ->has('attributes.priceA')
                        ->where('attributes.priceA', $price->priceA)
                        ->has('attributes.priceB')
                        ->where('attributes.priceB', $price->priceB)
                        ->has('attributes.priceC')
                        ->where('attributes.priceC', $price->priceC)
                        ->etc();
                })
                ->etc();
        });
});

/* ------------------------------ @store method ----------------------------- */
it('checks the correct response code when logged-in', function() {
    $priceData = [
        'group_description' => 'Group#1',
        'priceA' => 100,
        'priceB' => 200,
        'priceC' => 300,
        'product_id' => Product::factory()->create()->uuid
    ];

    postJson(action([PriceController::class, 'store'], $priceData))->assertStatus(201);
});

it('checks the correct response after successful storing when logged-in', function () {
    $this->withoutExceptionHandling();

    $priceData = [
        'group_description' => 'Group#1',
        'priceA' => 100,
        'priceB' => 200,
        'priceC' => 300,
        'product_id' => Product::factory()->create()->uuid
    ];

    postJson(action([PriceController::class, 'store'], $priceData))
        ->assertJsonPath('data.attributes.group_description', $priceData['group_description'])
        ->assertJsonPath('data.attributes.priceA', $priceData['priceA'])
        ->assertJsonPath('data.attributes.priceB', $priceData['priceB'])
        ->assertJsonPath('data.attributes.priceC', $priceData['priceC']);
});

it('checks the 422 response if data validation fails by different reasons when logged-in', function(?string $group, ?int $priceA, ?int $priceB, ?int $priceC) {
    $product_uuid = Product::factory()->create()->uuid;

    $priceData = [
        'group_description' => $group,
        'priceA' => $priceA,
        'priceB' => $priceB,
        'priceC' => $priceC,
        'product_id' => $product_uuid
    ];

    postJson(action([PriceController::class, 'store'], $priceData))->assertStatus(422);
})->with([
    [null, 100, 200, 300],
    ['Group', null, 200, 300],
    ['Group', 100, null, 300],
    ['Group', 100, 200, null],
]);

it('checks the stored price in database when logged-in', function() {
    $product_uuid = Product::factory()->create()->uuid;

    $priceData = [
        'group_description' => 'Group#1',
        'priceA' => 100,
        'priceB' => 200,
        'priceC' => 300,
        'product_id' => $product_uuid
    ];

    postJson(action([PriceController::class, 'store'], $priceData))->assertStatus(201);

    $this->assertDatabaseHas('prices', ['group_description' => 'Group#1', 'priceA' => 100, 'priceB' => 200, 'priceC' => 300]);
});

/* ----------------------------- @update method ----------------------------- */
it('checks the empty response body in case of successful update when logged-in', function() {
    //Arrange #1
    $product_uuid = Product::factory()->create()->uuid;

    $priceData = [
        'group_description' => 'Group#1',
        'priceA' => 100,
        'priceB' => 200,
        'priceC' => 300,
        'product_id' => $product_uuid
    ];
    //Action & Assert #1
    postJson(action([PriceController::class, 'store'], $priceData))->assertStatus(201);
    $price = Price::first();
    //Arrange #2
    $priceData = [
        'group_description' => 'Group#1',
        'priceA' => 150,
        'priceB' => 250,
        'priceC' => 350,
        'product_id' => $product_uuid
    ];
    //Action & Assert #2
    putJson(action([PriceController::class, 'update'], $price), $priceData)->assertNoContent();
});

it('checks the updated price in database when logged-in', function() {
    $this->withoutExceptionHandling();
    //Arrange #1
    $product_uuid = Product::factory()->create()->uuid;

    $priceData = [
        'group_description' => 'Group#1',
        'priceA' => 100,
        'priceB' => 200,
        'priceC' => 300,
        'product_id' => $product_uuid
    ];
    //Action & Assert #1
    postJson(action([PriceController::class, 'store'], $priceData))->assertStatus(201);
    $this->assertDatabaseHas('prices', ['group_description' => 'Group#1', 'priceA' => 100]);
    $price = Price::first();
    //Arrange #2
    $priceData = [
        'group_description' => 'Group#2',
        'priceA' => 150,
        'priceB' => 250,
        'priceC' => 350,
        'product_id' => $product_uuid
    ];
    //Action & Assert #2
    putJson(action([PriceController::class, 'update'], $price), $priceData)->assertNoContent();
    $this->assertDatabaseMissing('prices', ['group_description' => 'Group#1', 'priceA' => 100]);
    $this->assertDatabaseHas('prices', ['group_description' => 'Group#2', 'priceA' => 150]);
});

/* ----------------------------- @destroy method ---------------------------- */
it('checks the deletion of entry when logged-in', function() {
    //Arrange #1
    $product_uuid = Product::factory()->create()->uuid;

    $priceData = [
        'group_description' => 'Group#1',
        'priceA' => 100,
        'priceB' => 200,
        'priceC' => 300,
        'product_id' => $product_uuid
    ];
    //Action & Assert #1
    postJson(action([PriceController::class, 'store'], $priceData))->assertStatus(201);
    $this->assertDatabaseHas('prices', ['group_description' => 'Group#1', 'priceA' => 100]);
    $price = Price::first();

    deleteJson(action([PriceController::class, 'destroy'], $price))->assertNoContent();
    $this->assertDatabaseMissing('prices', ['group_description' => 'Group#1', 'priceA' => 100]);
});
