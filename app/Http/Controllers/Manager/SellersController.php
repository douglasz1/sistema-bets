<?php

namespace Bets\Http\Controllers\Manager;

use Bets\Services\UsersService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class SellersController extends Controller
{
    /**
     * @var UsersService
     */
    private $usersService;

    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }

    public function index()
    {
        return view('manager.sellers.index');
    }

    public function all(Request $request)
    {
        $sellers = $this->usersService->getByFilters(array(
            'role' => 'seller',
            'parent_id' => auth()->id()
        ), false, false);

        if ($request->expectsJson() || $request->wantsJson()) {
            return compact('sellers');
        }

        return null;
    }

    public function pluck()
    {
        $filters = array(
            'select' => array('id AS value', 'name AS label'),
            'role' => 'seller',
            'parent_id' => auth()->id()
        );

        $sellers = $this->usersService->getByFilters($filters);

        return compact('sellers');
    }

    public function changeStatus($id)
    {
        try {
            $seller = $this->usersService->findById($id);

            if ($seller->user_id !== auth()->id()) {
                throw new Exception("Você não pode editar este cambista");
            }

            $seller->active = !$seller->active;
            $seller->save();

            $msg = $seller->active ? 'Cambista ativado com sucesso!' : 'Cambista desativado com sucesso!';

            return redirect()->route('manager.sellers.index')->with('success', $msg);

        } catch (\Throwable $e) {
            return redirect()->route('manager.sellers.index')->with('error', "Erro ao atualizar. {$e->getMessage()}");
        }
    }
}
