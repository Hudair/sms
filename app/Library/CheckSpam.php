<?php


namespace App\Library;


class CheckSpam
{

    protected $blacklist = [];

    public function blacklist(array $keywords = [])
    {
        $this->blacklist = $keywords;
    }

    /**
     * check spam message
     *
     * @param $message
     *
     * @return bool
     */
    public function isSpam($message): bool
    {
        foreach ($this->blacklist as $keyword) {
            if (preg_match("/{$keyword}/i", $message)) {
                return true;
            }
        }

        return false;
    }

    public function notSpam($message): bool
    {
        return ! $this->isSpam($message);
    }
}
