<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class MalagasyCin implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (!is_string($value)) {
            $fail("The :attribute field must be a string.");

            return;
        }

        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) !== 8) {
            $fail("The :attribute field must contain 8 digits.");

            return;
        }

        $bureau = substr($digits, 0, 2);
        $year = substr($digits, 2, 2);
        $sequence = substr($digits, 4, 4);

        $bureau = (int) $bureau;
        $year = (int) $year;
        $sequence = (int) $sequence;

        if ($bureau < 1 || $bureau > 22) {
            $fail("The :attribute field has an invalid bureau code.");

            return;
        }

        if ($year < 0 || $year > 99) {
            $fail("The :attribute field has an invalid year code.");

            return;
        }

        if ($sequence < 0 || $sequence > 9999) {
            $fail("The :attribute field has an invalid sequence code.");
        }
    }
}
