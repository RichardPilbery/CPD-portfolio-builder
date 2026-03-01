<?php

namespace Database\Factories;

use App\Models\Vascular;
use Illuminate\Database\Eloquent\Factories\Factory;

class VascularFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vascular::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "audit_id" => $faker->numberBetween(1,5),
            "ivtype_id" => $faker->numberBetween(1,5),
            "success" => $faker->numberBetween(0,1),
            "size" => $faker->randomElement(['18g','16g','45mm','']),
            "ivsite_id" => $faker->numberBetween(1,8),
        ];
    }
}
