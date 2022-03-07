<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Phone implements Rule
{

    protected $value;

    /**
     * Create a new rule instance.
     *
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {

        return preg_match('%^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-. \\\/]?)?((?:\(?\d+\)?[\-. \\\/]?)*)(?:[\-. \\\/]?(?:#|ext\.?|extension|x)[\-. \\\/]?(\d+))?$%i', $value) && strlen($value) >= 10 && strlen($value) <= 14;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('locale.customer.invalid_phone_number', ['phone' => $this->value]);
    }
}
