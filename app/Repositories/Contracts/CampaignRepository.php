<?php

namespace App\Repositories\Contracts;

use App\Models\Campaigns;

interface CampaignRepository extends BaseRepository
{
    /**
     * send quick message
     *
     * @param  Campaigns  $campaign
     * @param  array  $input
     *
     * @return mixed
     */
    public function quickSend(Campaigns $campaign, array $input);

    /**
     * send campaign
     *
     * @param  Campaigns  $campaign
     * @param  array  $input
     *
     * @return mixed
     */
    public function campaignBuilder(Campaigns $campaign, array $input);


    /**
     * send campaign using file
     *
     * @param  Campaigns  $campaign
     * @param  array  $input
     *
     * @return mixed
     */
    public function sendUsingFile(Campaigns $campaign, array $input);

    /**
     * cancel campaign
     *
     * @param  Campaigns  $campaign
     *
     * @return mixed
     */
    public function cancel(Campaigns $campaign);

    /**
     * pause campaign
     *
     * @param  Campaigns  $campaign
     *
     * @return mixed
     */
    public function pause(Campaigns $campaign);

    /**
     * destroy campaign
     *
     * @param  Campaigns  $campaign
     *
     * @return mixed
     */
    public function destroy(Campaigns $campaign);

    /**
     * update existing campaign
     *
     * @param  Campaigns  $campaign
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(Campaigns $campaign, array $input);


    /**
     * resend existing campaign
     *
     * @param  Campaigns  $campaign
     *
     * @return mixed
     */
    public function resend(Campaigns $campaign);
}
