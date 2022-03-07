<?php

    namespace App\Repositories\Eloquent;

    use App\Exceptions\GeneralException;
    use App\Models\SpamWord;
    use App\Repositories\Contracts\SpamWordRepository;
    use Exception;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\DB;
    use Throwable;

    class EloquentSpamWordRepository extends EloquentBaseRepository implements SpamWordRepository
    {
        /**
         * EloquentSpamWordRepository constructor.
         *
         * @param SpamWord $spam_word
         */
        public function __construct(SpamWord $spam_word)
        {
            parent::__construct($spam_word);
        }

        /**
         * @param array $input
         *
         * @return SpamWord|mixed
         * @throws GeneralException
         */
        public function store(array $input): SpamWord
        {

            /** @var SpamWord $spam_word */
            $spam_word = $this->make(Arr::only($input, [
                'word',
            ]));

            if ( ! $this->save($spam_word)) {
                throw new GeneralException(__('locale.exceptions.something_went_wrong'));
            }

            return $spam_word;
        }


        /**
         * @param  SpamWord  $spam_word
         *
         * @return bool
         */
        private function save(SpamWord $spam_word): bool
        {
            if ( ! $spam_word->save()) {
                return false;
            }

            return true;
        }


        /**
         * @param  SpamWord  $spamWord
         *
         * @return bool|null
         * @throws GeneralException
         * @throws Exception
         */
        public function destroy(SpamWord $spamWord)
        {
            if ( ! $spamWord->delete()) {
                throw new GeneralException(__('locale.exceptions.something_went_wrong'));
            }

            return true;
        }

        /**
         * @param array $ids
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

    }
