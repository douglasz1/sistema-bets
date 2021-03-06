<?php

namespace Bets\Services;


use Bets\Models\Role;

class RolesService
{
    /**
     * @var Role
     */
    private $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * @param $id
     * @return Role|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function find($id)
    {
        return $this->role->with('permissions')->findOrFail($id);
    }

    public function get()
    {
        return $this->role->orderBy('label', 'ASC')->get();
    }

    public function update($id, array $data)
    {
        $role = $this->find($id);

        if (isset($data['permissions']) && !empty($data['permissions'])) {
            $this->cleanPermissions($role);
            $this->attachPermissions($role, $data['permissions']);
            unset($data['permissions']);
        }

        $result = $role->update($data);

        cache()->forget('permissions');

        return $result;
    }

    private function cleanPermissions(Role $role)
    {
        return $role->permissions()->detach();
    }

    private function attachPermissions(Role $role, array $permissions)
    {
        foreach ($permissions as $permission) {
            $role->permissions()->attach($permission);
        }
    }
}
