<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\GeneralException;
use App\Models\TemplateTags;
use App\Repositories\Contracts\TemplateTagsRepository;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class EloquentTemplateTagsRepository extends EloquentBaseRepository implements TemplateTagsRepository
{
    /**
     * EloquentTemplateTagsRepository constructor.
     *
     * @param  TemplateTags  $tags
     */
    public function __construct(TemplateTags $tags)
    {
        parent::__construct($tags);
    }

    /**
     * @param  array  $input
     *
     * @return TemplateTags |mixed
     *
     * @throws GeneralException
     */
    public function store(array $input): TemplateTags
    {
        /** @var TemplateTags $tags */
        $tags = $this->make(Arr::only($input, [
                'name',
                'tag',
                'type',
                'required',
        ]));

        if ( ! $this->save($tags)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $tags;

    }

    /**
     * @param  TemplateTags  $tags
     *
     * @return bool
     */
    private function save(TemplateTags $tags): bool
    {
        if ( ! $tags->save()) {
            return false;
        }

        return true;
    }

    /**
     * @param  TemplateTags  $tags
     * @param  array  $input
     *
     * @return TemplateTags
     * @throws Exception|Throwable
     *
     * @throws Exception
     */
    public function update(TemplateTags $tags, array $input): TemplateTags
    {
        if ( ! $tags->update($input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $tags;
    }

    /**
     * @param  TemplateTags  $tags
     *
     * @return bool|null
     * @throws Exception|Throwable
     *
     */
    public function destroy(TemplateTags $tags): ?bool
    {
        if ( ! $tags->delete()) {
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
    public function batchRequired(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)
                    ->update(['required' => true])
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
    public function batchOptional(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)
                    ->update(['required' => false])
            ) {
                return true;
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        });

        return true;
    }

}
