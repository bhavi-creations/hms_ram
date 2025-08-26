<?php

namespace App\Validation;

class CustomRules
{
    /**
     * Checks if the value is a valid date and is after or equal to the compare_to_value.
     *
     * @param string $str The field value to validate.
     * @param string $field The field name to compare against.
     * @param array $data The full request data.
     * @return boolean
     */
    public function after_or_equal_to(string $str = null, string $field = null, array $data = []): bool
    {
        // Get the value of the field to compare against
        $compareToValue = $data[$field] ?? null;

        // If either date is not valid, the rule fails.
        // The dates must be in a format that strtotime() can understand.
        if (empty($str) || empty($compareToValue) || strtotime($str) === false || strtotime($compareToValue) === false) {
            return false;
        }

        // Compare the timestamps
        return strtotime($str) >= strtotime($compareToValue);
    }
}
