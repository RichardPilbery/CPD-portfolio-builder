<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'subject_type'  => 'App\Models\Portfolio',
            'subject_id'    => 999,
            'user_id'       => 999,
            'title'         => $this->faker->sentence(4),
            'description'   => $this->faker->realText,
            'format'        => NULL,
            'origfilename'  => NULL,
            'filepath'      => NULL,
            'mimetype'      => NULL,
            'filesize'      => NULL
        ];
    }
}
