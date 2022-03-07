<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    /**
     * @method static where(string $string, string $uid)
     * @method static select(string $string)
     * @method static offset($start)
     * @method static whereLike(string[] $array, $search)
     * @method static count()
     * @method static cursor()
     * @method static insert(array $insert_data)
     * @method static create(array $blacklist)
     * @property mixed name
     * @property mixed user_id
     */
    class Blacklists extends Model
    {

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */

        protected $fillable = [
            'uid',
            'user_id',
            'number',
            'reason',
        ];


        /**
         * Bootstrap any application services.
         */
        public static function boot()
        {
            parent::boot();

            // Create uid when creating list.
            static::creating(function ($item) {
                // Create new uid
                $uid = uniqid();
                while (self::where('uid', $uid)->count() > 0) {
                    $uid = uniqid();
                }
                $item->uid = $uid;
            });
        }


        /**
         * get user
         *
         * @return BelongsTo
         *
         */
        public function user(): BelongsTo
        {
            return $this->belongsTo(User::class);
        }

        /**
         * Find item by uid.
         *
         * @param $uid
         *
         * @return object
         */
        public static function findByUid($uid): object
        {
            return self::where('uid', $uid)->first();
        }


        /**
         * get all plans
         *
         * @return mixed
         */

        public static function getAll()
        {
            return self::select('*');
        }


        /**
         * get route key by uid
         *
         * @return string
         */
        public function getRouteKeyName(): string
        {
            return 'uid';
        }


        /**
         * @return string
         */
        public function __toString(): string
        {
            return $this->name;
        }

    }
