<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class MalagasyCin implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute field must be a string.');

            return;
        }

        // The real Malagasy CIN is exactly 12 digits (XXXXXXXXXXXX), no separators.
        // Separators typed by users (spaces, dashes, dots) are tolerated and stripped.
        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) !== 12) {
            $fail('The :attribute field must contain exactly 12 digits.');
        }
    }
}
