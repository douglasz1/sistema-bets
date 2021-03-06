<?php

namespace Bets\Services;


use Bets\Models\Role;
use Bets\Models\User;

class UsersService
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param array $data
     * @param string $role
     * @return \Bets\Models\User
     */
    public function create(array $data, $role = 'seller')
    {
        $data['password'] = bcrypt($data['password']);

        $user = $this->user->create($data);

        $role = Role::where('name', $role)->first();

        $user->roles()->attach($role->id);

        return $user;
    }

    public function update(array $data, $id)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        unset($data['_token']);
        unset($data['password_confirmation']);

        return $this->user->where('id', $id)->update($data);
    }

    public function increaseBalance($userId, $balance)
    {
        $user = $this->user->find($userId);
        $user->balance += $balance;
        $user->save();

        return $user;
    }

    public function decreaseBalance($userId, $balance)
    {
        $user = $this->user->find($userId);
        $user->balance -= $balance;
        $user->save();

        return $user;
    }

    public function getByRole(String $role, bool $onlyActives = true)
    {
        return $this->user
            ->when($onlyActives, function ($query) {
                return $query->where('active', true);
            })
            ->whereHas('roles', function ($query) use ($role) {
                return $query->where('name', $role);
            })
            ->with(['parentUser' => function ($query) {
                return $query->select('id', 'name', 'user_id')
                    ->with(['parentUser' => function ($query) {
                        return $query->select('id', 'name');
                    }]);
            }, 'company'])
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function getByFilters(array $filters, $withParents = false, $onlyActives = true)
    {
        return $this->user
            ->when(!empty($filters['select']), function ($query) use ($filters) {
                return $query->select($filters['select']);
            })
            ->when(!empty($filters['role']), function ($query) use ($filters) {
                return $query->whereHas('roles', function ($query) use ($filters) {
                    return $query->where('name', $filters['role']);
                });
            })
            ->when(!empty($filters['parent_id']), function ($query) use ($filters) {
                if (is_array($filters['parent_id'])) {
                    return $query->whereIn('user_id', $filters['parent_id']);
                }
                return $query->where('user_id', $filters['parent_id']);
            })
            ->when($withParents, function ($query) {
                return $query->with([
                    'parentUser' => function ($query) {
                        return $query->select('id', 'name');
                    },
                    'company' => function ($query) {
                        return $query->select('id', 'name');
                    },
                ]);
            })
            ->when($onlyActives, function ($query) {
                return $query->where('active', true);
            })
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function pluckByFilters(array $filters)
    {
        return $this->user
            ->when(!empty($filters['role']), function ($query) use ($filters) {
                return $query->whereHas('roles', function ($query) use ($filters) {
                    return $query->where('name', $filters['role']);
                });
            })
            ->when(!empty($filters['parent_id']), function ($query) use ($filters) {
                return $query->where('user_id', $filters['parent_id']);
            })
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id');
    }

    public function pluckByRole($role, bool $onlyActives = true)
    {
        return $this->user
            ->when($onlyActives, function ($query) {
                return $query->where('active', true);
            })
            ->whereHas('roles', function ($query) use ($role) {
                return $query->where('name', $role);
            })
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id');
    }

    public function findById($id)
    {
        return $this->user->find($id);
    }

    /**
     * @param int $userId
     * @return \Bets\Models\User
     */
    public function alternateStatus($userId)
    {
        $user = $this->user->find($userId);

        $user->active = !$user->active;

        $user->save();

        return $user;
    }

    public function isMyUser($userId)
    {
        $user = $this->findById($userId);

        if ($user->user_id === auth()->id()) return true;

        if (auth()->user()->hasRoles('supervisor')) {
            $parent = $this->findById($user->user_id);
            return auth()->id() === $parent->user_id;

        } elseif (auth()->user()->hasRoles('manager')) {
            return $user->user_id === auth()->id();
        }

        return false;
    }
}
