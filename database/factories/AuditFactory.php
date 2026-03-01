<?php

namespace Database\Factories;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AuditFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Audit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $year = rand(2018, 2019);
        $month = rand(1, 12);
        $day = rand(1, 28);
        $hour = rand(1,23);
        $minute = rand(1,59);

        $date = Carbon::create($year,$month ,$day , $hour, $minute, 0);

        return [
            'user_id' => $this->faker->numberBetween(1,100),
            "age" => $this->faker->numberBetween(1,100),
            "ageunit" =>$this->faker->randomElement(['years','years','years','years','years','months','days']),
            'sex' => $this->faker->randomElement(['male','female']),
            'incdatetime'=>$date,
            'incnumber'=>$this->faker->randomNumber(8),
            'simulation'=>$this->faker->numberBetween(0,1),
            'provdiag' => $this->faker->sentence(3),
            'note'=>$this->faker->realText
        ];
    }
}
