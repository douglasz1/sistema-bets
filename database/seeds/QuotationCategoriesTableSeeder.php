<?php

use Bets\Models\QuotationCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuotationCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quotations = [
            ['name' => 'goals_over_under', 'label' => 'Gols Mais/Menos'],
            ['name' => 'alternative_total_goals', 'label' => 'Total de Gols - Mais Opções'],
            ['name' => 'result_total_goals', 'label' => 'Resultado / Total de gols', 'active' => false],
            ['name' => 'total_goals_both_teams_to_score', 'label' => 'Total de Gols / Ambas marcam'],
            ['name' => 'exact_total_goals', 'label' => 'Total exato de gols'],
            ['name' => 'number_of_goals_in_match', 'label' => 'Número de gols'],
            ['name' => 'both_teams_to_score', 'label' => 'Ambas marcam'],
            ['name' => 'teams_to_score', 'label' => 'Equipes que irão marcar', 'active' => false],
            ['name' => 'both_teams_to_score_in_1st_half', 'label' => '1º tempo - Ambas marcam'],
            ['name' => 'both_teams_to_score_in_2nd_half', 'label' => '2º tempo - Ambas marcam'],
            ['name' => 'both_teams_to_score_1st_half_2nd_half', 'label' => 'Ambas marcam no 1º / 2º tempo'],
            ['name' => 'first_half_goals', 'label' => '1º tempo - Total de gols'],
            ['name' => 'exact_1st_half_goals', 'label' => '1º tempo - Gols exatos'],
            ['name' => 'first_team_to_score', 'label' => 'Primeira equipe a marcar', 'active' => false],
            ['name' => '2nd_half_goals', 'label' => '2º tempo - Total de gols'],
            ['name' => 'exact_2nd_half_goals', 'label' => '2º tempo - Gols exatos'],
            ['name' => '1st_half_goals_odd_even', 'label' => '1º tempo - Total de gols par ou ímpar'],
            ['name' => 'half_with_most_goals', 'label' => 'Tempo com mais gols'],
            ['name' => 'home_team_exact_goals', 'label' => 'Casa - Gols exatos'],
            ['name' => 'away_team_exact_goals', 'label' => 'Visitante - Gols exatos'],
            ['name' => 'goals_odd_even', 'label' => 'Total de gols par ou ímpar'],
            ['name' => 'half_time_result', 'label' => '1º tempo - Vencedor'],
            ['name' => 'half_time_double_chance', 'label' => '1º tempo - Dupla chance'],
            ['name' => 'half_time_result_both_teams_to_score', 'label' => 'Vencedor do 1º tempo / Ambas marcam'],
            ['name' => 'half_time_result_total_goals', 'label' => 'Vencedor do 1º tempo / Total de gols'],
            ['name' => 'half_time_correct_score', 'label' => '1º tempo - Placar exato'],
            ['name' => '2nd_half_result', 'label' => '2º tempo - Vencedor'],
            ['name' => 'full_time_result', 'label' => 'Vencedor da partida'],
            ['name' => 'double_chance', 'label' => 'Dupla chance'],
            ['name' => 'correct_score', 'label' => 'Placar exato'],
            ['name' => 'half_time_full_time', 'label' => 'Vencedor do 1º / Vencedor final'],
            ['name' => 'draw_no_bet', 'label' => 'Empate anula a aposta'],
            ['name' => 'result_both_teams_to_score', 'label' => 'Vencedor / Ambas marcam'],
            ['name' => 'winning_margin', 'label' => 'Margem de Vitória', 'active' => false],
        ];

        foreach ($quotations as $quotation) {
            factory(QuotationCategory::class)->create($quotation);
        }
    }
}
