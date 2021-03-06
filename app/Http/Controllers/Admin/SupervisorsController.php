<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Http\Controllers\Controller;
use Bets\Http\Requests\Admin\SupervisorsCreateRequest;
use Bets\Http\Requests\Admin\SupervisorsUpdateRequest;
use Bets\Services\Admin\SupervisorsService;
use Bets\Services\UsersService;
use Illuminate\Http\Request;

class SupervisorsController extends Controller
{
    /**
     * @var UsersService
     */
    private $usersService;
    /**
     * @var SupervisorsService
     */
    private $service;

    public function __construct(UsersService $usersService, SupervisorsService $service)
    {
        $this->usersService = $usersService;
        $this->service = $service;
    }

    public function index()
    {
        return view('admin.supervisors.index');
    }

    public function create()
    {
        $supervisor = array(
            'quotation_modifier' => 0, 'profit_percentage' => 0,
            'balance' => 1000, 'limit' => 1000,
            'max_prize' => 12000, 'max_prize_multiplier' => 1000,
            'tips_min' => 3, 'tips_max' => 10,
            'commission1' => 10, 'value_min1' => 5, 'value_max1' => 50,
            'commission2' => 5, 'value_min2' => 5, 'value_max2' => 50,
            'commission3' => 2, 'value_min3' => 5, 'value_max3' => 50,
        );

        return view('admin.supervisors.create', compact('supervisor'));
    }

    public function store(SupervisorsCreateRequest $request)
    {
        $data = $request->except(['_token', 'password_confirmation']);

        $data['user_id'] = auth()->id();

        $this->usersService->create($data, 'supervisor');

        return redirect()->route('admin.supervisors.index')->with('success', 'Supervisor cadastrado com sucesso');
    }

    public function edit($id)
    {
        $supervisor = $this->usersService->findById($id);

        return view('admin.supervisors.edit', compact('supervisor'));
    }

    public function update(SupervisorsUpdateRequest $request, $id)
    {
        $data = $request->except(['_token', 'password_confirmation']);
        $data['user_id'] = auth()->id();

        $this->usersService->update($data, $id);

        return redirect()->route('admin.supervisors.index')->with('success', 'Supervisor atualizado com sucesso');
    }

    public function all()
    {
        $supervisors = $this->service->getAll();

        return response()->json([
            'supervisors' => $supervisors
        ]);
    }

    public function changeStatus($id)
    {
        $user = $this->usersService->alternateStatus($id);

        $msg = $user->active ? 'Supervisor ativado com sucesso!' : 'Supervisor desativado com sucesso!';

        return redirect()->route('admin.supervisors.index')->with('success', $msg);
    }

    public function pluck(Request $request)
    {
        $filters = array(
            'select' => array('id AS value', 'name AS label'),
            'role' => 'supervisor',
        );

        if ($request->has('parent_id')) {
            $filters['parent_id'] = $request->get('parent_id');
        }

        $supervisors = $this->usersService->getByFilters($filters);

        return response()->json([
            'supervisors' => $supervisors
        ]);
    }
}
