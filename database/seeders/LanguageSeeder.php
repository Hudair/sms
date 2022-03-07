<?php

    namespace Database\Seeders;


    use Illuminate\Database\Seeder;
    use App\Models\Language;

    class LanguageSeeder extends Seeder
    {
        /**
         * Run the database seeders.
         *
         * @return void
         */
        public function run()
        {

            $get_language = [
                [
                    'name' => 'English',
                    'code' => 'en',
                    'iso_code' => 'us'
                ],
                [
                    'name' => 'German',
                    'code' => 'de',
                    'iso_code' => 'de',
                ],
                [
                    'name' => 'French',
                    'code' => 'fr',
                    'iso_code' => 'fr',
                ],
                [
                    'name' => 'Portuguese',
                    'code' => 'pt',
                    'iso_code' => 'pt',
                ],
            ];
            foreach ($get_language as $lan) {
                Language::create($lan);
            }
        }

    }
