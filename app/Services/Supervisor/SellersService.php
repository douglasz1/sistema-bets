<?php

namespace Bets\Services\Supervisor;


use Bets\Jobs\ChangeSellersCompany;
use Bets\Models\Role;
use Bets\Models\User;
use Bets\Services\BaseService;

class SellersService extends BaseService
{
    protected $modelClass = User::class;

    private $companiesIDs;
    private $selectFields;
    private $managerId;
    private $onlyActives = true;

    public function getAll()
    {
        $query = $this->newQuery();

        $query->orderBy('name');

        $query->with('company', 'manager');

        $query->whereIn('company_id', $this->companiesIDs);

        $query->whereHas('roles', function ($query) {
            return $query->where('name', 'seller');
        });

        if ($this->onlyActives) {
            $query->where('active', true);
        }

        if (isset($this->selectFields)) {
            $query->select($this->selectFields);
        }

        if (isset($this->managerId)) {
            $query->where('user_id', $this->managerId);
        }

        return $this->doQuery($query, false, false);
    }

    public function lists()
    {
        $query = $this->newQuery();

        $query->orderBy('name');

        $query->whereIn('company_id', $this->companiesIDs);

        if ($this->onlyActives) {
            $query->where('active', true);
        }

        $query->whereHas('roles', function ($query) {
            return $query->where('name', 'seller');
        });

        return $this->pluck($query);
    }

    public function createSeller(array $data)
    {
        $data['password'] = bcrypt($data['password']);

        $user = $this->create($data);

        $role = Role::where('name', 'seller')->first();

        $user->roles()->attach($role->id);

        return $user;
    }

    public function updateSeller(array $data, $id)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $seller = $this->update($data, $id);

        dispatch((new ChangeSellersCompany($seller))->onQueue('betStatus'));

        return $seller;
    }

    /**
     * @param mixed $companiesIDs
     * @return SellersService
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
     * @return SellersService
     */
    public function setSelectFields($selectFields)
    {
        $this->selectFields = $selectFields;
        return $this;
    }

    /**
     * @param mixed $managerId
     * @return SellersService
     */
    public function setManagerId($managerId)
    {
        $this->managerId = $managerId;
        return $this;
    }

    /**
     * @param bool $onlyActives
     * @return SellersService
     */
    public function setOnlyActives(bool $onlyActives): SellersService
    {
        $this->onlyActives = $onlyActives;
        return $this;
    }
}
