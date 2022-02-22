<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

// https://aaezha.bearblog.dev/laravel-float-number-validation/

class FloatValidator implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return is_float($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The value must be currency like 100000.00';
    }
}
