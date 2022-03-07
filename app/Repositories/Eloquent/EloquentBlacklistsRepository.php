<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\GeneralException;
use App\Models\Blacklists;
use App\Models\Contacts;
use App\Repositories\Contracts\BlacklistsRepository;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class EloquentBlacklistsRepository extends EloquentBaseRepository implements BlacklistsRepository
{
    /**
     * EloquentBlacklistsRepository constructor.
     *
     * @param  Blacklists  $blacklists
     */
    public function __construct(Blacklists $blacklists)
    {
        parent::__construct($blacklists);
    }

    /**
     * store blacklist
     *
     * @param  array  $input
     *
     * @return Collection|mixed
     */
    public function store(array $input): Collection
    {
        $results = explode($input['delimiter'], $input['number']);

        return collect($results)->reject(function ($number) {
            return empty($number) || strlen($number) > 14;
        })->unique()->chunk(1000)->each(function ($numbers) use ($input) {

            $insert_data = [];
            foreach ($numbers as $number) {

                $contact = Contacts::where('phone', $number)->first();
                if ($contact) {
                    $contact->update([
                            'status' => 'unsubscribe',
                    ]);
                }

                $insert_data[] = [
                        'uid'     => uniqid(),
                        'user_id' => auth()->user()->id,
                        'number'  => $number,
                        'reason'  => $input['reason'],
                ];
            }

            Blacklists::insert($insert_data);
        });
    }


    /**
     * @param  Blacklists  $blacklists
     *
     * @return bool|null
     * @throws GeneralException
     * @throws Exception
     */
    public function destroy(Blacklists $blacklists)
    {
        $contact = Contacts::where('phone', $blacklists->number)->first();
        if ($contact) {
            $contact->update([
                    'status' => 'subscribe',
            ]);
        }
        if ( ! $blacklists->delete()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return true;
    }

    /**
     * @param  array  $ids
     *
     * @return mixed
     * @throws Throwable
     */
    public function batchDestroy(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)->delete()) {
                return true;
            }
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        });

        return true;
    }

}
