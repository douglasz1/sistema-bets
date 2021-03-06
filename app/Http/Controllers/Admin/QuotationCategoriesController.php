<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Http\Controllers\Controller;
use Bets\Services\QuotationCategoriesService;
use Illuminate\Http\Request;

class QuotationCategoriesController extends Controller
{
    /**
     * @var QuotationCategoriesService
     */
    private $categoriesService;

    public function __construct(QuotationCategoriesService $categoriesService)
    {
        $this->categoriesService = $categoriesService;
    }

    public function index()
    {
        $quotationCategories = $this->categoriesService->getAll();

        return view('admin.quotations.categories.index', compact('quotationCategories'));
    }

    public function edit($id)
    {
        $quotationCategory = $this->categoriesService->findById($id);

        return view('admin.quotations.categories.edit', compact('quotationCategory'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->only('alterar_cotacao');

        $this->categoriesService->update($data, $id);

        return redirect()->route('admin.quotations.categories.index')
            ->with('success', 'Categoria alterada com sucesso!');
    }

    public function changeStatus($id)
    {
        $quotationCategory = $this->categoriesService->alternateStatus($id);

        $message = $quotationCategory->ativo ? 'Categoria ativada com sucesso!' : 'Categoria desativada com sucesso!';

        return redirect()->route('admin.quotations.categories.index')->with('success', $message);
    }
}
