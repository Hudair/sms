<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\User;
use App\Repositories\Contracts\AccountRepository;
use App\Repositories\Contracts\BlacklistsRepository;
use App\Repositories\Contracts\CampaignRepository;
use App\Repositories\Contracts\ContactsRepository;
use App\Repositories\Contracts\CurrencyRepository;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\KeywordRepository;
use App\Repositories\Contracts\LanguageRepository;
use App\Repositories\Contracts\PhoneNumberRepository;
use App\Repositories\Contracts\PlanRepository;
use App\Repositories\Contracts\RoleRepository;
use App\Repositories\Contracts\SenderIDRepository;
use App\Repositories\Contracts\SendingServerRepository;
use App\Repositories\Contracts\SettingsRepository;
use App\Repositories\Contracts\TemplatesRepository;
use App\Repositories\Contracts\SpamWordRepository;
use App\Repositories\Contracts\SubscriptionRepository;
use App\Repositories\Contracts\TemplateTagsRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Eloquent\EloquentAccountRepository;
use App\Repositories\Eloquent\EloquentBlacklistsRepository;
use App\Repositories\Eloquent\EloquentCampaignRepository;
use App\Repositories\Eloquent\EloquentContactsRepository;
use App\Repositories\Eloquent\EloquentCurrencyRepository;
use App\Repositories\Eloquent\EloquentCustomerRepository;
use App\Repositories\Eloquent\EloquentKeywordRepository;
use App\Repositories\Eloquent\EloquentLanguageRepository;
use App\Repositories\Eloquent\EloquentPhoneNumberRepository;
use App\Repositories\Eloquent\EloquentPlanRepository;
use App\Repositories\Eloquent\EloquentRoleRepository;
use App\Repositories\Eloquent\EloquentSenderIDRepository;
use App\Repositories\Eloquent\EloquentSendingServerRepository;
use App\Repositories\Eloquent\EloquentSettingsRepository;
use App\Repositories\Eloquent\EloquentTemplatesRepository;
use App\Repositories\Eloquent\EloquentSpamWordRepository;
use App\Repositories\Eloquent\EloquentSubscriptionRepository;
use App\Repositories\Eloquent\EloquentTemplateTagsRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use Closure;
use Illuminate\Cache\NullStore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


/**
 * @method where(Closure $param)
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
                UserRepository::class,
                EloquentUserRepository::class
        );

        $this->app->bind(
                AccountRepository::class,
                EloquentAccountRepository::class
        );

        $this->app->bind(
                RoleRepository::class,
                EloquentRoleRepository::class
        );

        $this->app->bind(
                CustomerRepository::class,
                EloquentCustomerRepository::class
        );

        $this->app->bind(
                CurrencyRepository::class,
                EloquentCurrencyRepository::class
        );

        $this->app->bind(
                SendingServerRepository::class,
                EloquentSendingServerRepository::class
        );

        $this->app->bind(
                PlanRepository::class,
                EloquentPlanRepository::class
        );

        $this->app->bind(
                KeywordRepository::class,
                EloquentKeywordRepository::class
        );

        $this->app->bind(
                SenderIDRepository::class,
                EloquentSenderIDRepository::class
        );

        $this->app->bind(
                SettingsRepository::class,
                EloquentSettingsRepository::class
        );

        $this->app->bind(
                LanguageRepository::class,
                EloquentLanguageRepository::class
        );

        $this->app->bind(
                SubscriptionRepository::class,
                EloquentSubscriptionRepository::class
        );

        $this->app->bind(
                PhoneNumberRepository::class,
                EloquentPhoneNumberRepository::class
        );

        $this->app->bind(
                TemplateTagsRepository::class,
                EloquentTemplateTagsRepository::class
        );

        $this->app->bind(
                BlacklistsRepository::class,
                EloquentBlacklistsRepository::class
        );

        $this->app->bind(
                SpamWordRepository::class,
                EloquentSpamWordRepository::class
        );

        $this->app->bind(
                ContactsRepository::class,
                EloquentContactsRepository::class
        );

        $this->app->bind(
                TemplatesRepository::class,
                EloquentTemplatesRepository::class
        );

        $this->app->bind(
                CampaignRepository::class,
                EloquentCampaignRepository::class
        );

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Force SSL if isSecure does not detect HTTPS
        if (config('app.url_force_https')) {
            URL::forceScheme('https');
        }

        Relation::morphMap([
                'user'     => User::class,
                'customer' => Customer::class,
                'admin'    => Admin::class,
        ]);

        Cache::extend('none', function () {
            return Cache::repository(new NullStore());
        });

        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (array_wrap($attributes) as $attribute) {
                    $query->when(
                            str_contains($attribute, '.'),
                            function (Builder $query) use ($attribute, $searchTerm) {
                                [$relationName, $relationAttribute] = explode('.', $attribute);

                                $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                                    $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                                });
                            },
                            function (Builder $query) use ($attribute, $searchTerm) {
                                $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                            }
                    );
                }
            });

            return $this;
        });

    }
}
