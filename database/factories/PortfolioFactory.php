<?php

namespace Database\Factories;

use App\Models\Portfolio;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class PortfolioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Portfolio::class;

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
            "actdate" => $date->format('Y-m-d'),
            "title" => ucwords($this->faker->word(3)),
            "description" => $this->faker->realText,
            "benefit" => $this->faker->realText,
            "activity_id" => $this->faker->numberBetween(1,5),
            "profile" => $this->faker->numberBetween(0,1),
            'start'  => $date->addHours(rand(1, 24))->addMinutes(rand(1,50))->format('Y-m-d H:i:s'),
            'end'  => $date->addDays(rand(1, 300))->addHours(rand(1, 24))->addMinutes(rand(1,50))->format('Y-m-d H:i:s'),
            'user_id' => $this->faker->numberBetween(1,100)
        ];
    }
}
