<?php

namespace App\Repositories\Contracts;

/* *
 * Interface CountryRepository
 */

use App\Models\Country;

interface CountriesRepository extends BaseRepository
{

    /**
     * @param  array  $input
     *
     * @return mixed
     */
    public function store(array $input);

    /**
     * @param  Country  $country
     *
     * @return mixed
     */

    public function destroy(Country $country);

}
