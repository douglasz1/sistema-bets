<?php
if (!function_exists('categorias_cotacao')) {
    /**
     * @param string $esporte
     * @return mixed
     */
    function categorias_cotacao($esporte = 'futebol')
    {
        return \Cache::tags('categoriasCotacao')
            ->rememberForever("categoriasCotacao-{$esporte}", function () use ($esporte) {
                return \Bets\Models\Esportes\CategoriaCotacao::query()
                    ->where('esporte', $esporte)
                    ->where('ativo', true)
                    ->get();
            });
    }
}
