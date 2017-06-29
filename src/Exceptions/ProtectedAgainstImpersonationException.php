<?php

namespace Rickycezar\Impersonate\Exceptions;


class ProtectedAgainstImpersonationException extends \Exception{

    public function __construct($message = 'These data can\'t be accessed by an impersonator.', $code = 403){
        $this->message     = $message;
        $this->code        = $code;
    }
}