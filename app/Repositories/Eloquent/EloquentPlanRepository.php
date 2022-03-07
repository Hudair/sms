<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\GeneralException;
use App\Models\Plan;
use App\Models\PlansSendingServer;
use App\Models\SendingServer;
use App\Repositories\Contracts\PlanRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class EloquentPlanRepository extends EloquentBaseRepository implements PlanRepository
{

    /**
     * EloquentPlanRepository constructor.
     *
     * @param  Plan  $plan
     */
    public function __construct(Plan $plan)
    {
        parent::__construct($plan);
    }

    /**
     * @param  array  $input
     * @param  array  $options
     * @param  array  $billingCycle
     *
     * @return Plan|mixed
     * @throws GeneralException
     */

    public function store(array $input, array $options, array $billingCycle): Plan
    {

        /** @var Plan $plan */
        $plan = $this->make(Arr::only($input, [
                'name',
                'price',
                'billing_cycle',
                'frequency_amount',
                'frequency_unit',
                'currency_id',
                'is_popular',
                'tax_billing_required',
        ]));

        if (isset($input['tax_billing_required'])) {
            $plan->tax_billing_required = true;
        }

        if (isset($input['is_popular'])) {
            $plan->is_popular = true;
        }

        if (isset($input['billing_cycle']) && $input['billing_cycle'] != 'custom') {
            $limits                 = $billingCycle[$input['billing_cycle']];
            $plan->frequency_amount = $limits['frequency_amount'];
            $plan->frequency_unit   = $limits['frequency_unit'];
        }

        $plan->options = json_encode($options);

        $plan->status  = true;
        $plan->user_id = auth()->user()->id;

        if ( ! $this->save($plan)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $plan;

    }

    /**
     * @param  Plan  $plan
     *
     * @return bool
     */
    private function save(Plan $plan): bool
    {
        if ( ! $plan->save()) {
            return false;
        }

        return true;
    }

    /**
     * @param  Plan  $plan
     * @param  array  $input
     *
     * @param  array  $billingCycle
     *
     * @return Plan
     * @throws GeneralException
     */
    public function update(Plan $plan, array $input, array $billingCycle): Plan
    {
        if (isset($input['tax_billing_required'])) {
            $input['tax_billing_required'] = true;
        } else {
            $input['tax_billing_required'] = false;
        }

        if (isset($input['is_popular'])) {
            $input['is_popular'] = true;
        } else {
            $input['is_popular'] = false;
        }

        if (isset($input['billing_cycle']) && $input['billing_cycle'] != 'custom') {
            $limits                    = $billingCycle[$input['billing_cycle']];
            $input['frequency_amount'] = $limits['frequency_amount'];
            $input['frequency_unit']   = $limits['frequency_unit'];
        }

        if ( ! $plan->update($input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $plan;
    }

    /**
     * @param  Plan  $plan
     *
     * @return bool|null
     * @throws Exception|Throwable
     *
     */
    public function destroy(Plan $plan)
    {
        if ( ! $plan->delete()) {
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


    /**
     * update fitness
     *
     * @param  array  $hash
     * @param  Plan  $plan
     *
     * @return bool
     */
    public function updateFitnesses(array $hash, Plan $plan): bool
    {

        foreach ($hash as $uid => $value) {
            $sendingServer = SendingServer::findByUid($uid);
            PlansSendingServer::where('sending_server_id', $sendingServer->id)->where('plan_id', $plan->id)->update(['fitness' => $value]);
        }

        return true;
    }


    /**
     * update primary sending server
     *
     * @param  Plan  $plan
     * @param  array  $input
     *
     * @return bool
     */
    public function setPrimarySendingServer(Plan $plan, array $input): bool
    {
        $plan->plansSendingServers()->update(['is_primary' => false]);
        $server = SendingServer::findByUid($input['server_id']);
        $plan->plansSendingServers()->where('sending_server_id', $server->id)->update(['is_primary' => true]);

        $plan->update([
                'status' => true,
        ]);

        return true;
    }

    /**
     * remove plan sending server
     *
     * @param  Plan  $plan
     * @param  array  $input
     *
     * @return bool|mixed
     */

    public function removeSendingServerByUid(Plan $plan, array $input): bool
    {

        $server = SendingServer::findByUid($input['server_id']);
        $plan->plansSendingServers()->where('sending_server_id', $server->id)->delete();

        // First primary
        $query = $plan->plansSendingServers()->where('is_primary', true);

        if ( ! $query->count()) {
            $first = $plan->plansSendingServers()->first();
            if (is_object($first)) {
                $first->is_primary = true;
                $first->save();
            }

            $plan->update([
                    'status' => false,
            ]);
        }


        return true;

    }

    /**
     * update speed limit
     *
     * @param  Plan  $plan
     * @param  array  $input
     *
     * @return bool|mixed
     * @throws GeneralException
     */

    public function updateSpeedLimits(Plan $plan, array $input): bool
    {
        $get_options = json_decode($plan->options, true);
        $output      = array_replace($get_options, $input);

        if ( ! $plan->update(['options' => $output])) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return true;

    }

    /**
     * update cutting system
     *
     * @param  Plan  $plan
     * @param  array  $input
     *
     * @return bool|mixed
     * @throws GeneralException
     */

    public function updateCuttingSystem(Plan $plan, array $input): bool
    {
        $get_options = json_decode($plan->options, true);
        $output      = array_replace($get_options, $input);

        if ( ! $plan->update(['options' => $output])) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return true;

    }


    /**
     * update sms pricing
     *
     * @param  Plan  $plan
     * @param  array  $input
     *
     * @return bool|mixed
     * @throws GeneralException
     */

    public function updatePricing(Plan $plan, array $input): bool
    {
        $get_options = json_decode($plan->options, true);
        $output      = array_replace($get_options, $input);

        if ( ! $plan->update(['options' => $output])) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return true;

    }


    /**
     * copy existing plan
     *
     * @param  Plan  $plan
     * @param  array  $input
     *
     * @return Plan|mixed
     * @throws GeneralException
     */

    public function copy(Plan $plan, array $input): Plan
    {

        if ( ! $new_plan = $plan->replicate()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        $new_plan->name         = $input['plan_name'];
        $new_plan->custom_order = 0;
        $new_plan->status       = false;
        $new_plan->created_at   = Carbon::now();
        $new_plan->updated_at   = Carbon::now();

        if ( ! $new_plan->save()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $new_plan;
    }
}
