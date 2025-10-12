<?php namespace App\Traits;

trait HasErrors
{
    protected $errors = [];

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function addError(string $error, $contents = null)
    {
        if (is_null($contents)) {
            $this->errors[] = $error;
        } else {
            $this->errors[$error] = $contents;
        }

    }

    public function addErrors(array $errors)
    {
        foreach ($errors as $error => $contents) {
            if (is_int($error)) {
                $this->errors[] = $contents;
            } else {
                $this->errors[$error] = $contents;
            }
        }
    }
}
