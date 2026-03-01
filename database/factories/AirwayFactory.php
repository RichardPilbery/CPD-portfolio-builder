<?php

namespace Database\Factories;

use App\Models\Airway;
use Illuminate\Database\Eloquent\Factories\Factory;

class AirwayFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Airway::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "audit_id" => $faker->$faker->numberBetween(1,100),
            "airwaytype_id" => $faker->numberBetween(1,17),
            "success" => $faker->numberBetween(0,1),
            "grade" => $faker->numberBetween(1,4),
            "size" => $faker->randomElement([4.0,4.5,5.0,5.5,6.0,6.5,7.0,7.5,8.0,8.5,9.0]),
            "bougie" => $faker->numberBetween(0,1),
            "capnography_id" => $faker->numberBetween(1,5),
            "notes" => $faker->realText
        ];
    }
}
