<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Services\PermissionsService;
use Bets\Services\RolesService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class RolesController extends Controller
{
    /**
     * @var RolesService
     */
    private $rolesService;
    /**
     * @var PermissionsService
     */
    private $permissionsService;

    public function __construct(RolesService $rolesService, PermissionsService $permissionsService)
    {
        $this->rolesService = $rolesService;
        $this->permissionsService = $permissionsService;
    }

    public function index()
    {
        $this->authorize('roles.list');

        $roles = $this->rolesService->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function edit($id)
    {
        $this->authorize('roles.edit');

        $role = $this->rolesService->find($id);
        $permissions = $this->permissionsService->get();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('roles.edit');

        $data = $request->all();

        $this->rolesService->update($id, $data);

        return redirect()->route('admin.roles.index');
    }
}
