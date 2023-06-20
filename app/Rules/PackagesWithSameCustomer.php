<?php

namespace App\Rules;

use App\Models\OrderPackage;
use Illuminate\Contracts\Validation\Rule;

class PackagesWithSameCustomer implements Rule
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
        return OrderPackage::query()->findMany($value)->groupBy('customer_id')->count() == 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.do_not_same_customer');
    }
}
