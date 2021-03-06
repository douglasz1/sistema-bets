<?php

namespace Bets\Services\Admin;


use Bets\Models\User;
use Bets\Services\BaseService;

class SupervisorsService extends BaseService
{
    protected $modelClass = User::class;

    public function getAll()
    {
        $query = $this->newQuery();

        $query->orderBy('name');

        $query->whereHas('roles', function ($query) {
            return $query->where('name', 'supervisor');
        });

        $query->with('companies');

        return $this->doQuery($query, false, false);
    }

}