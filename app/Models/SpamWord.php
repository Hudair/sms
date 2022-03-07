<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    /**
     * @method static where(string $string, string $uid)
     * @method static count()
     * @method static offset($start)
     * @method static whereLike(string[] $array, $search)
     * @method static cursor()
     * @method static truncate()
     * @method static create(string[] $word)
     * @property mixed name
     */
    class SpamWord extends Model
    {

        /**
         * The attributes for assign table
         *
         * @var string
         */

        protected $table = 'spam_word';


        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */

        protected $fillable = [
            'word',
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
