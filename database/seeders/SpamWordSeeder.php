<?php

namespace Database\Seeders;

use App\Models\SpamWord;
use Illuminate\Database\Seeder;

class SpamWordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SpamWord::truncate();

        $spam_words = [
                [
                        'word' => 'POLICE',
                ],
                [
                        'word' => 'RAB',
                ],
                [
                        'word' => 'GOVT',
                ],
                [
                        'word' => 'NYPD',
                ],
                [
                        'word' => 'CIA',
                ],
                [
                        'word' => 'NDP',
                ],
                [
                        'word' => 'FBI',
                ],
        ];

        foreach ($spam_words as $word) {
            SpamWord::create($word);
        }

    }
}
