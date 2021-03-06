<?php

use Illuminate\Database\Seeder;
use Bets\Models\Role;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Role::class)->create([
            'name' => 'admin',
            'label' => 'Administrador'
        ]);

        factory(Role::class)->create([
            'name' => 'supervisor',
            'label' => 'Supervisor'
        ]);

        factory(Role::class)->create([
            'name' => 'manager',
            'label' => 'Gerente'
        ]);

        factory(Role::class)->create([
            'name' => 'seller',
            'label' => 'Vendedor'
        ]);

        factory(Role::class)->create([
            'name' => 'technical',
            'label' => 'Técnico'
        ]);

        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 1,
        ]);

        DB::table('role_user')->insert([
            'user_id' => 2,
            'role_id' => 2,
        ]);

        DB::table('role_user')->insert([
            'user_id' => 3,
            'role_id' => 3,
        ]);

        DB::table('role_user')->insert([
            'user_id' => 4,
            'role_id' => 4,
        ]);
    }
}
