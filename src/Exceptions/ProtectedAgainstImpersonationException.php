<?php

namespace Rickycezar\Impersonate\Exceptions;


class ProtectedAgainstImpersonationException extends \Exception{

    protected $errorMessage;
    protected $errorCode;
    protected $patternMessage;

    public function __construct(){
        $this->patternMessage   = 'These data can\'t be accessed by an impersonator.';
        $this->errorMessage     = $this->patternMessage;
        $this->message     = $this->patternMessage;
        $this->errorCode        = 403;
    }

    public function getErrorMessage(){
        return $this->errorMessage;
    }

    public function getErrorCode(){
        return $this->errorCode;
    }
}