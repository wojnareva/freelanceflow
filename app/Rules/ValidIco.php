<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidIco implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($value)) {
            $fail(__('validation.ico_invalid'));
        }
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes(mixed $value): bool
    {
        // Convert to string and remove spaces
        $ico = (string) $value;
        $ico = str_replace(' ', '', $ico);

        // Check if it's exactly 8 digits
        if (! preg_match('/^[0-9]{8}$/', $ico)) {
            return false;
        }

        // IČO kontrolní algoritmus (Czech business ID validation algorithm)
        $weights = [8, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        // Calculate weighted sum of first 7 digits
        for ($i = 0; $i < 7; $i++) {
            $sum += (int) $ico[$i] * $weights[$i];
        }

        // Calculate check digit according to Czech IČO rules
        $remainder = $sum % 11;
        // If the remainder is 0 or 1, the control digit is 0, otherwise 11 - remainder
        $checkDigit = ($remainder === 0 || $remainder === 1)
            ? 0
            : 11 - $remainder;

        // Compare with the 8th digit
        return (int) $ico[7] === $checkDigit;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('validation.ico_invalid');
    }
}
