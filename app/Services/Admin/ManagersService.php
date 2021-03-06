<?php

namespace Bets\Services\Admin;


use Bets\Jobs\ChangeManagersCompany;
use Bets\Models\Role;
use Bets\Models\User;
use Bets\Services\BaseService;

class ManagersService extends BaseService
{
    protected $modelClass = User::class;

    private $companiesIDs;
    private $selectFields;
    private $onlyActives = true;

    public function getAll()
    {
        $query = $this->newQuery();

        $query->orderBy('name');

        $query->with('company');

        if ($this->onlyActives) {
            $query->where('active', true);
        }

        if (isset($this->companiesIDs)) {
            $query->whereIn('company_id', $this->companiesIDs);
        }

        $query->whereHas('roles', function ($query) {
            return $query->where('name', 'manager');
        });

        if (isset($this->selectFields)) {
            $query->select($this->selectFields);
        }

        return $this->doQuery($query, false, false);
    }

    public function lists()
    {
        $query = $this->newQuery();

        $query->orderBy('name');

        if ($this->onlyActives) {
            $query->where('active', true);
        }

        if (isset($this->companiesIDs)) {
            $query->whereIn('company_id', $this->companiesIDs);
        }

        $query->whereHas('roles', function ($query) {
            return $query->where('name', 'manager');
        });

        return $this->pluck($query);
    }

    public function createManager(array $data)
    {
        $data['password'] = bcrypt($data['password']);

        $user = $this->create($data);

        $role = Role::where('name', 'manager')->first();

        $user->roles()->attach($role->id);

        return $user;
    }

    public function updateManager(array $data, $id)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $manager = $this->update($data, $id);

        dispatch((new ChangeManagersCompany($manager))->onQueue('betStatus'));

        return $manager;
    }

    /**
     * @param mixed $companiesIDs
     * @return ManagersService
     */
    public function setCompaniesIDs($companiesIDs)
    {
        if (is_array($companiesIDs)) {
            $this->companiesIDs = $companiesIDs;
        } elseif (!is_null($companiesIDs)) {
            $this->companiesIDs = [$companiesIDs];
        }

        return $this;
    }

    /**
     * @param mixed $selectFields
     * @return ManagersService
     */
    public function setSelectFields($selectFields)
    {
        $this->selectFields = $selectFields;
        return $this;
    }

    /**
     * @param bool $onlyActives
     * @return ManagersService
     */
    public function setOnlyActives(bool $onlyActives): ManagersService
    {
        $this->onlyActives = $onlyActives;
        return $this;
    }
}
