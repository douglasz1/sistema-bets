<?php

namespace Bets\Services\Supervisor;


use Bets\Jobs\ChangeManagersCompany;
use Bets\Models\Role;
use Bets\Models\User;
use Bets\Services\BaseService;

class ManagersService extends BaseService
{
    protected $modelClass = User::class;

    private $companiesId;
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

        $query->whereIn('company_id', $this->companiesId);

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

        $query->whereIn('company_id', $this->companiesId);

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
     * @param mixed $companiesId
     * @return ManagersService
     */
    public function setCompaniesId($companiesId)
    {
        if (is_array($companiesId)) {
            $this->companiesId = $companiesId;
        } else {
            $this->companiesId = [$companiesId];
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
