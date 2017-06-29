<?php

namespace Rickycezar\Impersonate\Exceptions;


class CantImpersonateSelfException extends \Exception{

    public function __construct($message = 'It is not possible to impersonate self.', $code = 403){
        $this->message     = $message;
        $this->code        = $code;
    }
}