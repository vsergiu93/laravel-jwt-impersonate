<?php

namespace Rickycezar\Impersonate\Exceptions;


class CantImpersonateException extends \Exception{

    protected $errorMessage;
    protected $errorCode;
    protected $patternMessage;

    public function __construct(){
        $this->patternMessage   = 'This user can\'t impersonate.';
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