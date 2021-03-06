<?php

namespace Bets\Services\Admin;


use Bets\Models\Role;
use Bets\Models\User;
use Bets\Services\BaseService;

class TechnicalService extends BaseService
{
    protected $modelClass = User::class;

    public function createTechnical(array $data)
    {
        $data['password'] = bcrypt($data['password']);

        $user = $this->create($data);

        $role = Role::where('name', 'technical')->first();

        $user->roles()->attach($role->id);

        return $user;
    }

    public function updateTechnical(array $data, $id)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->update($data, $id);
    }

    public function alternateStatus($id)
    {
        $user = $this->find($id);

        $user->active = !$user->active;

        $user->save();

        return $user;
    }

    public function getAll()
    {
        $query = $this->newQuery();

        $query->select('name', 'active', 'id');

        $query->orderBy('name');

        $query->whereHas('roles', function ($query) {
            return $query->where('name', 'technical');
        });

        return $this->doQuery($query, false, false);
    }
}