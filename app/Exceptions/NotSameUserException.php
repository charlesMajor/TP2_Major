<?php

namespace App\Exceptions;

use Exception;
use App\Http\Controllers\Controller;

class NotSameUserException extends Exception
{
    public function status()
    {
        return FORBIDDEN;
    }

    public function message()
    {
        return 'Trying to access another user';
    }
}
