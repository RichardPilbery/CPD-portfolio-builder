<?php

namespace Database\Factories;

use App\Models\Swot;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class SwotFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Swot::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $year = rand(2016, 2022);
        $month = rand(1, 12);
        $day = rand(1, 28);

        $date = Carbon::create($year,$month ,$day , 0, 0, 0);

        return [
            "portfolio_id" => 12345677,
            "strength" => ucwords($this->faker->word(3)),
            "weakness" => $this->faker->realText,
            "opportunity" => $this->faker->realText,
            "threat" => $this->faker->realText
        ];
    }
}
