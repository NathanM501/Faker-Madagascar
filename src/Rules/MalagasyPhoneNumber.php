<?php

declare(strict_types=1);

namespace Manguithre\FakerMadagascar\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Manguithre\FakerMadagascar\Data\PhonePrefixRepository;

class MalagasyPhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute field must be a string.');

            return;
        }

        $digits = preg_replace('/\D/', '', $value);

        // Accept the international form: +261 32 12 345 67 -> 0321234567
        if (strlen($digits) === 12 && str_starts_with($digits, '261')) {
            $digits = '0' . substr($digits, 3);
        }

        if (strlen($digits) !== 10 || !str_starts_with($digits, '0')) {
            $fail('The :attribute field is not a valid Malagasy phone number.');

            return;
        }

        if (!PhonePrefixRepository::isValidPrefix($digits)) {
            $fail('The :attribute field has an invalid Malagasy mobile prefix.');
        }
    }
}
