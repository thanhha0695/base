<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

class FileOrStringRule implements Rule
{
    private string $message = 'The validation error message.';

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
        if ($value instanceof UploadedFile) {
            $mimeType = $value->getClientMimeType();
            if (!in_array($mimeType, ['image/jpg', 'image/gif', 'image/png', 'image/jpeg'])) {
                $this->message = "The $attribute allow file jpg, gif and png";
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
