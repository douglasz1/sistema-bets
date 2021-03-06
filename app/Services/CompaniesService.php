<?php

namespace Bets\Services;


use Bets\Models\Company;

class CompaniesService
{
    /**
     * @var Company
     */
    private $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function pluck()
    {
        return $this->company->orderBy('name', 'ASC')->pluck('name', 'id');
    }

    /**
     * @return Company
     */
    public function first()
    {
        return $this->company->first();
    }

    public function get()
    {
        return $this->company->get();
    }

    public function find($id)
    {
        return $this->company->find($id);
    }

    public function create(array $data)
    {
        return $this->company->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->company->where('id', $id)->update($data);
    }

    public function attach(array $supervisors, $id)
    {
        $company = $this->find($id);

//        $company->supervisors()->deatch();

        $company->supervisors()->sync($supervisors);
    }
}