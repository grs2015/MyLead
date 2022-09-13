<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Price;
use App\Models\Product;

class PriceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Price::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
            'group_description' => $this->faker->text,
            'priceA' => $this->faker->randomNumber(),
            'priceB' => $this->faker->randomNumber(),
            'priceC' => $this->faker->randomNumber(),
            'product_id' => Product::factory(),
        ];
    }
}
