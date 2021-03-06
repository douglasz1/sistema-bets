<?php

namespace Bets\Services;


use Bets\Models\Permission;

class PermissionsService
{
    /**
     * @var Permission
     */
    private $permission;

    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    public function find($id)
    {
        return $this->permission->with('roles')->findOrFail($id);
    }

    public function get()
    {
        return $this->permission->orderBy('name', 'ASC')->get();
    }
}
