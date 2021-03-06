<?php

namespace Bets\Services\Admin;


use Bets\Models\Company;
use Bets\Services\BaseService;

class CompaniesService extends BaseService
{
    protected $modelClass = Company::class;

    private $selectFields;

    public function getAll()
    {
        $query = $this->newQuery();
        $query->orderBy('name');

        if (isset($this->selectFields)) {
            $query->select($this->selectFields);
        }

        return $this->doQuery($query, false, false);
    }

    public function lists()
    {
        return $this->pluck();
    }

    public function attach(array $supervisors, $id)
    {
        $company = $this->find($id);

        $company->supervisors()->sync($supervisors);

        return $company;
    }

    public function updateQuotations(bool $up)
    {
        if ($up) {
            return $this->newQuery()->whereNotNull('created_at')->increment('quotation_modifier');
        }

        return $this->newQuery()->whereNotNull('created_at')->decrement('quotation_modifier');
    }

    /**
     * @param mixed $selectFields
     * @return CompaniesService
     */
    public function setSelectFields($selectFields)
    {
        $this->selectFields = $selectFields;
        return $this;
    }
}
