<?php

namespace Database\Factories;

use App\Models\Pdp;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class PdpFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pdp::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $year = rand(2016, 2019);
        $month = rand(1, 12);
        $day = rand(1, 28);

        $date = Carbon::create($year,$month ,$day , 0, 0, 0);

        return [
            "finishdate" => $date->format('Y-m-d'),
            "objective" => $this->faker->realText,
            "activity" => $this->faker->realText,
            "measure" => $this->faker->realText,
            "support" => $this->faker->realText,
            "barriers" => $this->faker->realText,
            'user_id' => $this->faker->numberBetween(1,100),
            'portfolio_id' => $this->faker->numberBetween(1,100)
        ];
    }
}
