<?php

namespace Database\Seeders;

use App\DataAccess\Models\DeportistaModel;
use App\DataAccess\Models\ModalidadModel;
use App\DataAccess\Models\UserModel;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = UserModel::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@club.test',
        ]);

        $modalidades = ModalidadModel::factory()->count(5)->create();

        DeportistaModel::factory()
            ->count(20)
            ->for($admin, 'user')
            ->recycle($modalidades)
            ->create();
    }
}
