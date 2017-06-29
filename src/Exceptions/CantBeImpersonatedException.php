<?php

namespace Rickycezar\Impersonate\Exceptions;


class CantBeImpersonatedException extends \Exception{

    public function __construct($message = 'Target user can\'t be impersonated.', $code = 403){
        $this->message     = $message;
        $this->code        = $code;
    }
}