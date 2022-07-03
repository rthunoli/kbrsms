<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1,4),
            'activity_date' => $this->faker->dateTimeThisMonth(),
            'activity' => $this->faker->text(50),
            'description' => $this->faker->text(),
        ];
    }
}
