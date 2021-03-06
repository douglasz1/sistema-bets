<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Http\Requests\Admin\TechnicalCreate;
use Bets\Http\Requests\Admin\TechnicalUpdate;
use Bets\Services\Admin\TechnicalService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class TechnicalController extends Controller
{
    /**
     * @var TechnicalService
     */
    private $service;

    public function __construct(TechnicalService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('admin.technical.index');
    }

    public function create()
    {
        $technical = [
            'quotation_modifier' => 0, 'profit_percentage' => 0,
            'balance' => 1000, 'limit' => 1000,
            'max_prize' => 12000, 'max_prize_multiplier' => 1000,
            'tips_min' => 3, 'tips_max' => 10,
            'commission1' => 10, 'value_min1' => 5, 'value_max1' => 50,
            'commission2' => 5, 'value_min2' => 5, 'value_max2' => 50,
            'commission3' => 2, 'value_min3' => 5, 'value_max3' => 50,
        ];

        return view('admin.technical.create', compact('technical'));
    }

    public function store(TechnicalCreate $request)
    {
        $data = $request->except(['_token', 'password_confirmation']);

        $this->service->createTechnical($data);

        return redirect()->route('admin.technical.index')->with('success', 'Técnico cadastrado com sucesso');
    }

    public function edit($id)
    {
        $technical = $this->service->find($id);

        return view('admin.technical.edit', compact('technical'));
    }

    public function update(TechnicalUpdate $request, $id)
    {
        $data = $request->except(['_token', 'password_confirmation']);

        $this->service->updateTechnical($data, $id);

        return redirect()->route('admin.technical.index')->with('success', 'Técnico atualizado com sucesso');
    }

    public function all()
    {
        $technical = $this->service->getAll();

        return response()->json([
            'technical' => $technical
        ]);
    }

    public function changeStatus($id)
    {
        $user = $this->service->alternateStatus($id);

        $msg = $user->active ? 'Técnico ativado com sucesso!' : 'Técnico desativado com sucesso!';

        return redirect()->route('admin.technical.index')->with('success', $msg);
    }
}
