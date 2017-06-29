<?php

namespace Rickycezar\Impersonate\Exceptions;


class NotImpersonatingException extends \Exception{

    public function __construct($message = 'This user is not impersonating.', $code = 403){
        $this->message     = $message;
        $this->code        = $code;
    }
}