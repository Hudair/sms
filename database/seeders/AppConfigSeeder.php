<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use App\Models\AppConfig;

    class AppConfigSeeder extends Seeder
    {
        /**
         * Run the database seeders.
         *
         * @return void
         */
        public function run()
        {
            $app_config = new AppConfig();
            $app_config->truncate();

            $appconf = $app_config->defaultSettings();

            foreach ($appconf as $conf) {
                $app_config->create($conf);
            }
        }

    }
