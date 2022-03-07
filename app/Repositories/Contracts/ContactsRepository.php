<?php

namespace App\Repositories\Contracts;

use App\Models\ContactGroups;

/**
 * Interface ContactsRepository.
 */
interface ContactsRepository extends BaseRepository
{

    /**
     * @param  array  $input
     *
     * @return mixed
     */
    public function store(array $input);

    /**
     * @param  ContactGroups  $contactGroups
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(ContactGroups $contactGroups, array $input);

    /**
     * @param  ContactGroups  $contactGroups
     *
     * @return mixed
     */

    public function destroy(ContactGroups $contactGroups);

    /**
     * @param  array  $ids
     *
     * @return mixed
     */
    public function batchDestroy(array $ids);

    /**
     * @param  array  $ids
     *
     * @return mixed
     */
    public function batchActive(array $ids);

    /**
     * @param  array  $ids
     *
     * @return mixed
     */
    public function batchDisable(array $ids);

    /**
     * @param  ContactGroups  $contactGroups
     * @param  array  $input
     *
     * @return mixed
     */
    public function storeContact(ContactGroups $contactGroups, array $input);

    /**
     * @param  ContactGroups  $contactGroups
     * @param  array  $input
     *
     * @return mixed
     */
    public function updateContactStatus(ContactGroups $contactGroups, array $input);

    /**
     * @param  ContactGroups  $contactGroups
     * @param  array  $input
     *
     * @return mixed
     */
    public function updateContact(ContactGroups $contactGroups, array $input);


    /**
     * delete single contact
     *
     * @param  ContactGroups  $contactGroups
     * @param  array  $id
     *
     * @return mixed
     */
    public function contactDestroy(ContactGroups $contactGroups, array $id);

    /**
     * @param  ContactGroups  $contactGroups
     * @param  array  $ids
     *
     * @return mixed
     */
    public function batchContactDestroy(ContactGroups $contactGroups, array $ids);

    /**
     * @param  ContactGroups  $contactGroups
     * @param  array  $ids
     *
     * @return mixed
     */
    public function batchContactSubscribe(ContactGroups $contactGroups, array $ids);

    /**
     * @param  ContactGroups  $contactGroups
     * @param  array  $ids
     *
     * @return mixed
     */
    public function batchContactUnsubscribe(ContactGroups $contactGroups, array $ids);

    /**
     * @param  ContactGroups  $contactGroups
     * @param  array  $input
     *
     * @return mixed
     */
    public function batchContactCopy(ContactGroups $contactGroups, array $input);

    /**
     * @param  ContactGroups  $contactGroups
     * @param  array  $input
     *
     * @return mixed
     */
    public function batchContactMove(ContactGroups $contactGroups, array $input);


}
