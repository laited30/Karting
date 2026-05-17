<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kart>
 */
class KartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Згенерує унікальний номер: "Карт №1", "Карт №2" тощо
            'number' => 'Cart №' . $this->faker->unique()->numberBetween(1, 30),
            // Випадкова модель з трьох популярних брендів
            'model' => $this->faker->randomElement(['Rimo Alpha', 'Sodi RT8', 'MS Kart']), 
            // Випадкова потужність: 9 к.с. (для дітей/новачків) або 13-15 (для профі)
            'power' => $this ->faker->randomElement([9, 13, 15]),
            // За замовчуванням робимо карт активним
            'is_active' => $this->faker->randomElement([true, false]),
            // Випадковий коментар про стан карта
            'notes' => $this->faker->randomElement(['New rubber', 'Run-in engine', 'Requires maintenance in 5 hours', 'In perfect condition']), 
        ];
    }
}