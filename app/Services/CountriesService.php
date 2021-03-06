<?php


namespace Bets\Services;


use Bets\Jobs\PegarBandeiraPais;
use Bets\Models\Country;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CountriesService
{
    public function firstOrCreate(array $attributes)
    {
        $query = Country::query();
        try {
            $country = $query
                ->where('api_id', $attributes['api_id'])
                ->firstOrFail();

            $country->update($attributes);
        } catch (ModelNotFoundException $exception) {
            $country = $query->create($attributes);
            dispatch((new PegarBandeiraPais($country))->onQueue('matches'));
        }
        return $country;
    }
}
