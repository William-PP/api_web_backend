<?php

namespace Database\Factories;

use App\DataAccess\Models\ModalidadModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModalidadModel>
 */
class ModalidadFactory extends Factory
{
    protected $model = ModalidadModel::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->words(2, true),
            'descripcion' => fake()->sentence(),
        ];
    }
}
