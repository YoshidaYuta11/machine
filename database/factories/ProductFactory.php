<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product; // この行を追加
use App\Models\Company; // この行を追加

class ProductFactory extends Factory
{
    

    protected $model = Product::class;  // この行を追加

    public function definition(): array
{
    return [
        'company_id' => Company::factory(),
        'product_name' => $this->faker->word,  
        'price' => $this->faker->numberBetween(100, 10000),  
        'stock' => $this->faker->randomDigit,  
        'comment' => $this->faker->sentence,  
        'img_path' => $this->faker->optional()->imageUrl(200, 300, 'products', true, 'Dummy Image'),  // ランダムな画像
    ];
}

}

