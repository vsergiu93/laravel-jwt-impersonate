<?php

namespace Rickycezar\Impersonate\Exceptions;


class CantImpersonateException extends \Exception{

    public function __construct($message = 'This user can\'t impersonate.', $code = 403){
        $this->message     = $message;
        $this->code        = $code;
    }
}