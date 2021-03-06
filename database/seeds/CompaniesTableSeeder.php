<?php

use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\Bets\Models\Company::class)->create([
            'name' => 'Bets',
            'print_name' => 'Bets',
        ]);
    }
}
