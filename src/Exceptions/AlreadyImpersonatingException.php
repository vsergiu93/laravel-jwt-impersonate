<?php

namespace Rickycezar\Impersonate\Exceptions;


class AlreadyImpersonatingException extends \Exception{

    public function __construct($message = 'This user is already impersonating someone.', $code = 403){
        $this->message     = $message;
        $this->code        = $code;
    }
}