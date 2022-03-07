<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\GeneralException;
use App\Models\Currency;
use App\Repositories\Contracts\CurrencyRepository;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class EloquentCurrencyRepository extends EloquentBaseRepository implements CurrencyRepository
{
    /**
     * EloquentCurrencyRepository constructor.
     *
     * @param  Currency  $currency
     */
    public function __construct(Currency $currency)
    {
        parent::__construct($currency);
    }

    /**
     * @param  array  $input
     *
     * @return Currency|mixed
     *
     * @throws GeneralException
     */
    public function store(array $input): Currency
    {
        /** @var Currency $currency */
        $currency = $this->make(Arr::only($input, ['name', 'code', 'format']));

        $currency->status  = true;
        $currency->user_id = auth()->user()->id;

        if ( ! $this->save($currency)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $currency;

    }

    /**
     * @param  Currency  $currency
     *
     * @return bool
     */
    private function save(Currency $currency): bool
    {
        if ( ! $currency->save()) {
            return false;
        }

        return true;
    }

    /**
     * @param  Currency  $currency
     * @param  array  $input
     *
     * @return Currency
     * @throws Exception|Throwable
     *
     * @throws Exception
     */
    public function update(Currency $currency, array $input): Currency
    {
        if ( ! $currency->update($input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $currency;
    }

    /**
     * @param  Currency  $currency
     *
     * @return bool|null
     * @throws Exception|Throwable
     *
     */
    public function destroy(Currency $currency)
    {
        if ( ! $currency->delete()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return true;
    }

    /**
     * @param  array  $ids
     *
     * @return mixed
     * @throws Exception|Throwable
     *
     */
    public function batchDestroy(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            // This wont call eloquent events, change to destroy if needed
            if ($this->query()->whereIn('uid', $ids)->delete()) {
                return true;
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        });

        return true;
    }

    /**
     * @param  array  $ids
     *
     * @return mixed
     * @throws Exception|Throwable
     *
     */
    public function batchActive(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)
                    ->update(['status' => true])
            ) {
                return true;
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        });

        return true;
    }

    /**
     * @param  array  $ids
     *
     * @return mixed
     * @throws Exception|Throwable
     *
     */
    public function batchDisable(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)
                    ->update(['status' => false])
            ) {
                return true;
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        });

        return true;
    }

}
