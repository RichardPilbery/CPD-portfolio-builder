<?php

namespace Database\Factories;

use App\Models\Summary;
use Illuminate\Database\Eloquent\Factories\Factory;

class SummaryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Summary::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id" => $faker->unique()->numberBetween(1, 5),
            "work_details" => $faker->realText(),
            "service_users" => $faker->realText(),
            "job_description" => $faker->realText(),
            "standard1" => "To meet standard 1, I use an online CPD portfolio builder to keep a continuous up-to-date and accurate record of my CPD activities. It is online, making adding and amending entries easy, whether I am at work or at home. A summary of the previous 2 years CPD activity is listed in the summary sheet",
            "standard2" => "To meet standards 2, 3 and 4, I have included a number of examples across a range of learning activities relevant to my current and future practice. They clearly demonstrate how I have sought to ensure that my CPD has contributed to the quality of my practice and service delivery, and benefited my service users"
        ];
    }
}
