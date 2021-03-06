<?php

namespace Bets\Http\Controllers\Technical;

use Bets\Http\Requests\QuotationCategoriesRequest;
use Bets\Services\QuotationCategoriesService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

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

        return view('technical.quotations.categories.index', compact('quotationCategories'));
    }

    public function edit($id)
    {
        $quotationCategory = $this->categoriesService->findById($id);

        return view('technical.quotations.categories.edit', compact('quotationCategory'));
    }

    public function update(QuotationCategoriesRequest $request, $id)
    {
        $data = $request->except('_token');

        $this->categoriesService->update($data, $id);

        return redirect()->route('technical.quotations.categories.index')
            ->with('success', 'Categoria alterada com sucesso!');
    }

    public function changeStatus($id)
    {
        $quotationCategory = $this->categoriesService->alternateStatus($id);

        $message = $quotationCategory->active ? 'Categoria ativada com sucesso!' : 'Categoria desativada com sucesso!';

        return redirect()->route('technical.quotations.categories.index')->with('success', $message);
    }
}
