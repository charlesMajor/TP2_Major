<?php

namespace App\Exceptions;

use Exception;
use App\Http\Controllers\Controller;

class MoreThanOneCriticException extends Exception
{
    public function status()
    {
        return FORBIDDEN;
    }

    public function message()
    {
        return 'User already created critic for this movie';
    }
}
