<?php

use Illuminate\Database\Seeder;
use Bets\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            ['name' => 'bet.create', 'label' => 'Fazer apostas'],
            ['name' => 'bet.edit', 'label' => 'Editar apostas'],
            ['name' => 'bet.delete', 'label' => 'Apagar apostas'],
            ['name' => 'bet.cancel', 'label' => 'Cancelar apostas'],
            ['name' => 'bet.pin.validate', 'label' => 'Validar PIN'],
            ['name' => 'cashier.index', 'label' => 'Ver caixa'],
            ['name' => 'financial.index', 'label' => 'Relatório financeiro'],
            ['name' => 'results.create', 'label' => 'Criar resultados'],
            ['name' => 'results.edit', 'label' => 'Editar resultados'],
            ['name' => 'results.delete', 'label' => 'Apagar resultados'],
            ['name' => 'matches.list', 'label' => 'Listar partidas'],
            ['name' => 'matches.create', 'label' => 'Criar partidas'],
            ['name' => 'matches.edit', 'label' => 'Editar partidas'],
            ['name' => 'matches.delete', 'label' => 'Apagar partidas'],
            ['name' => 'leagues.list', 'label' => 'Listar ligas'],
            ['name' => 'leagues.create', 'label' => 'Criar ligas'],
            ['name' => 'leagues.edit', 'label' => 'Editar ligas'],
            ['name' => 'leagues.delete', 'label' => 'Apagar ligas'],
            ['name' => 'sellers.list', 'label' => 'Listar cambistas'],
            ['name' => 'sellers.create', 'label' => 'Criar cambistas'],
            ['name' => 'sellers.edit', 'label' => 'Editar cambistas'],
            ['name' => 'sellers.delete', 'label' => 'Apagar cambistas'],
            ['name' => 'supervisors.list', 'label' => 'Listar supervisores'],
            ['name' => 'supervisors.create', 'label' => 'Criar supervisores'],
            ['name' => 'supervisors.edit', 'label' => 'Editar supervisores'],
            ['name' => 'supervisors.delete', 'label' => 'Apagar supervisores'],
            ['name' => 'managers.list', 'label' => 'Listar gerentes'],
            ['name' => 'managers.create', 'label' => 'Criar gerentes'],
            ['name' => 'managers.edit', 'label' => 'Editar gerentes'],
            ['name' => 'managers.delete', 'label' => 'Apagar gerentes'],
            ['name' => 'quotations.categories', 'label' => 'Gerenciar cotações'],
            ['name' => 'tip.cancel', 'label' => 'Cancelar palpite'],
        ];

        foreach ($permissions as $permission) {
            factory(Permission::class)->create($permission);
        }

        DB::table('permission_role')->insert(array(
            ['permission_id' => 1, 'role_id' => 1],
            ['permission_id' => 2, 'role_id' => 1],
            ['permission_id' => 3, 'role_id' => 1],
            ['permission_id' => 1, 'role_id' => 4],
            ['permission_id' => 5, 'role_id' => 4],
            ['permission_id' => 6, 'role_id' => 4],
            ['permission_id' => 7, 'role_id' => 4],
        ));
    }
}
