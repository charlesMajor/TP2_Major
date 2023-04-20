<?php

namespace App\Exceptions;

use Exception;
use App\Http\Controllers\Controller;

class NotAdminException extends Exception
{
    public function status()
    {
        return FORBIDDEN;
    }

    public function message()
    {
        return 'User is not an admin';
    }
}
