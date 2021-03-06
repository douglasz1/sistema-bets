<?php

namespace Bets\Services\Supervisor;


use Bets\Models\Company;
use Bets\Services\BaseService;

class CompaniesService extends BaseService
{
    protected $modelClass = Company::class;

    private $selectFields;
    private $userId;

    /**
     * @param mixed $userId
     * @return CompaniesService
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function getAll()
    {
        $query = $this->newQuery();

        $query->orderBy('name');

        $userId = $this->userId;

        if (isset($this->selectFields)) {
            $query->select($this->selectFields);
        }

        $query->whereHas('supervisors', function ($query) use ($userId) {
            return $query->where('company_supervisor.user_id', $userId);
        });

        return $this->doQuery($query, false, false);
    }

    public function lists()
    {
        $query = $this->newQuery();

        $query->orderBy('name');

        $userId = $this->userId;

        $query->whereHas('supervisors', function ($query) use ($userId) {
            return $query->where('company_supervisor.user_id', $userId);
        });

        return $this->pluck($query);
    }

    /**
     * @param mixed $selectFields
     * @return CompaniesService
     */
    public function setSelectFields($selectFields): CompaniesService
    {
        $this->selectFields = $selectFields;
        return $this;
    }
}