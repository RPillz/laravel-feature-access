<?php

namespace RPillz\FeatureAccess\Database\Factories;

use RPillz\FeatureAccess\Models\Feature;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeatureFactory extends Factory
{
    protected $model = Feature::class;

    public function definition()
    {
        return [
            'feature' => $this->faker->randomElement(['sample-feature', 'pages']),
            'level' => $this->faker->randomElement(['basic', 'pro', null]),
            'read' => $this->faker->randomElement([true, false, null]),
            'edit' => $this->faker->randomElement([true, false, null]),
            'create' => $this->faker->randomElement([true, false, null]),
            'destroy' => $this->faker->randomElement([true, false, null]),
            'limit' => $this->faker->numberBetween(0, 100),
            'expires_at' => $this->faker->randomElement([now()->addDays(30), null]),
        ];
    }
}
