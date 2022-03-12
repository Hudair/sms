<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\GeneralException;
use App\Models\Country;
use App\Repositories\Contracts\CountriesRepository;
use Exception;
use Illuminate\Support\Arr;
use Throwable;

class EloquentCountriesRepository extends EloquentBaseRepository implements CountriesRepository
{
    /**
     * EloquentCountriesRepository constructor.
     *
     * @param  Country  $country
     */
    public function __construct(Country $country)
    {
        parent::__construct($country);
    }

    /**
     * @param  array  $input
     *
     * @return Country|mixed
     *
     * @throws GeneralException
     */
    public function store(array $input): Country
    {


        $country = $this->make(Arr::only($input, ['name', 'country_code', 'iso_code', 'status']));

        /** @var TYPE_NAME $country */
        if ( ! $this->save($country)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $country;

    }

    /**
     * @param  Country  $country
     *
     * @return bool
     */
    private function save(Country $country): bool
    {
        if ( ! $country->save()) {
            return false;
        }

        return true;
    }


    /**
     * @param  Country  $country
     *
     * @return bool|null
     * @throws Exception|Throwable
     *
     */
    public function destroy(Country $country)
    {
        if ( ! $country->delete()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return true;
    }
}
