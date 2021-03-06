<?php

namespace Bets\Services;


use Bets\Models\Esportes\CategoriaCotacao;

class QuotationCategoriesService
{
    public function getAll()
    {
        return CategoriaCotacao::query()
            ->orderBy('mercado_descricao')
            ->orderBy('palpite_descricao')
            ->get();
    }

    public function findById($id)
    {
        return CategoriaCotacao::query()->find($id);
    }

    /**
     * @param array $data
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function update(array $data, $id)
    {
        $category = $this->findById($id);

        $category->fill($data)->save();

        cache()->tags('categoriasCotacao')->flush();

        return $category;
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function alternateStatus($id)
    {
        $quotationCategory = $this->findById($id);

        $quotationCategory->ativo = !$quotationCategory->ativo;

        $quotationCategory->save();

        cache()->tags('categoriasCotacao')->flush();

        return $quotationCategory;
    }

}
