<?php

namespace Bets\Services;


use Carbon\Carbon;

class BaseService
{
    protected $modelClass;

    protected function newQuery()
    {
        return app($this->modelClass)->newQuery();
    }

    protected function doQuery($query = null, $take = 15, $paginate = true)
    {
        if (is_null($query)) {
            $query = $this->newQuery();
        }

        if ($paginate) {
            return $query->paginate($take);
        }

        if ($take > 0 || $take !== false) {
            $query->take($take);
        }

        return $query->get();
    }

    public function find($id, $fail = true)
    {
        if ($fail) {
            return $this->newQuery()->findOrFail($id);
        }

        return $this->newQuery()->find($id);
    }

    protected function pluck($query = null, $value = 'name', $key = 'id')
    {
        if (is_null($query)) {
            $query = $this->newQuery();
            $query->orderBy('name');
        }

        return $query->pluck($value, $key);
    }

    public function create(array $data)
    {
        $data['created_at'] = Carbon::now()->toDateTimeString();
        $data['updated_at'] = Carbon::now()->toDateTimeString();

        $insertId = $this->newQuery()->insertGetId($data);
        return $this->find($insertId);
    }

    public function update(array $data, $id)
    {
        $model = $this->find($id);

        $model->fill($data);

        $model->save();

        return $model;
    }

    public function delete($id)
    {
        return $this->newQuery()->find($id)->delete();
    }
}
