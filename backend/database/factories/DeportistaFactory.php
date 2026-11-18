<?php

namespace Database\Factories;

use App\DataAccess\Models\DeportistaModel;
use App\DataAccess\Models\ModalidadModel;
use App\DataAccess\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeportistaModel>
 */
class DeportistaFactory extends Factory
{
    protected $model = DeportistaModel::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-40 years', '-12 years')->format('Y-m-d'),
            'foto' => null,
            'modalidad_id' => ModalidadModel::factory(),
            'user_id' => UserModel::factory(),
        ];
    }
}
