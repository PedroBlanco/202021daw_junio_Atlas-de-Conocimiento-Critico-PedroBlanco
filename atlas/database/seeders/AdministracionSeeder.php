<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Administracion

class AdministracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Si estamos 'sembrando' la aplicación desde 0, ¿sería mejor un insert en vez de upsert para que salgan errores?
        Administracion::upsert([
            'nombre' => '',
            'descripcion' => '',
        ],
        ['nombre'],
        ['nombre', 'descripcion'],
        );
    }
}
