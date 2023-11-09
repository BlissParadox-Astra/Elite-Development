<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_code' => $this->faker->unique()->ean8,
            'barcode' => $this->faker->ean13,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'reorder_level' => $this->faker->numberBetween(1, 100),
            'stock_on_hand' => $this->faker->numberBetween(0, 500),
            'product_type_id' => 1, // Replace with your actual product type ID
            'category_id' => 1,    // Replace with your actual category ID
            'brand_id' => 1,       // Replace with your actual brand ID
        ];
    }
}
