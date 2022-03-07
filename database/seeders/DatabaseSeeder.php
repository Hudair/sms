<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AppConfigSeeder::class);
        $this->call(Countries::class);
        $this->call(LanguageSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CurrenciesSeeder::class);
        $this->call(EmailTemplateSeeder::class);
        $this->call(PaymentMethodsSeeder::class);
        //  $this->call(BlacklistSeeder::class);
        //  $this->call(KeywordsSeeder::class);
        //  $this->call(PhoneNumberSeeder::class);
        //  $this->call(SenderIDSeeder::class);
        //  $this->call(SendingServerSeeder::class);
        //  $this->call(SpamWordSeeder::class);
        //  $this->call(PlanSeeder::class);

    }

}
