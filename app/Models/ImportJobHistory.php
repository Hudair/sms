<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static where(string $string, string $id)
 */
class ImportJobHistory extends Model
{

    const STATUS_PROCESSING = 'processing';
    const STATUS_FINISHED = 'finished';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
            'name',
            'type',
            'status',
            'options',
            'import_id',
            'batch_id',
    ];

    /**
     * is processing
     *
     * @return string
     */
    public function isProcessing(): string
    {
        return self::STATUS_PROCESSING;
    }

    /**
     * is finished
     *
     * @return string
     */
    public function isFinished(): string
    {
        return self::STATUS_FINISHED;
    }

    /**
     * is failed
     *
     * @return string
     */
    public function isFailed(): string
    {
        return self::STATUS_FAILED;
    }

    /**
     * is cancelled
     *
     * @return string
     */
    public function isCancelled(): string
    {
        return self::STATUS_CANCELLED;
    }

    /**
     * get single option
     *
     * @param $name
     *
     * @return mixed|string
     */
    public function getOption($name): string
    {
        return $this->getOptions()[$name];
    }


    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        if (empty($this->options)) {
            return [];
        } else {
            return json_decode($this->options, true);
        }
    }

    /**
     * get status
     *
     * @return string
     */

    public function getStatus(): string
    {
        $status = $this->status;

        if ($status == self::STATUS_FAILED || $status == self::STATUS_CANCELLED) {
            return '<div class="badge badge-danger text-uppercase mr-1 mb-1"><span>'.__('locale.labels.'.$status).'</span></div>';
        }
        if ($status == self::STATUS_PROCESSING) {
            return '<div class="badge badge-primary text-uppercase mr-1 mb-1"><span>'.__('locale.labels.processing').'</span></div>';
        }

        return '<div class="badge badge-success text-uppercase mr-1 mb-1"><span>'.__('locale.labels.finished').'</span></div>';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }


}
