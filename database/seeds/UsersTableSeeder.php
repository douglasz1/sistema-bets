<?php

use Bets\Models\Role;
use Illuminate\Database\Seeder;
use Bets\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->create([
            'name' => 'Administrador',
            'username' => 'admin',
            'company_id' => 1
        ]);

        factory(User::class)->create([
            'name' => 'Supervisor',
            'username' => 'supervisor',
            'user_id' => 1,
            'company_id' => 1
        ]);

        factory(User::class)->create([
            'name' => 'Gerente',
            'username' => 'gerente',
            'user_id' => 2,
            'company_id' => 1
        ]);

        factory(User::class)->create([
            'name' => 'Vendedor',
            'username' => 'vendedor',
            'user_id' => 3,
            'company_id' => 1
        ]);
    }
}
