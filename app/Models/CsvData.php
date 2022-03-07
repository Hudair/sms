<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static find(mixed $csv_data_file_id)
 */
class CsvData extends Model
{
    const TYPE_CONTACT = 'contact';
    const TYPE_CAMPAIGN = 'campaign';

    protected $fillable = [
            'user_id',
            'csv_filename',
            'csv_header',
            'csv_data',
            'ref_id',
            'ref_type',
    ];
}
