<?php

namespace Buttress\Concrete\Service;

final class Result
{

    private $success;

    private $errors;

    public function __construct($success = true, $errors = [])
    {
        if (!is_array($errors) && !$errors instanceof Iterator) {
            $errors = [$errors];
        }

        $this->success = (bool) $success;
        $this->errors = (array)$errors;
    }

    /**
     * @return success
     */
    public function success()
    {
        return $this->success;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}
