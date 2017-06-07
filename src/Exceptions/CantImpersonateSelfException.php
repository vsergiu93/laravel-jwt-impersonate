<?php

namespace Rickycezar\Impersonate\Exceptions;


class CantImpersonateSelfException extends \Exception{

    protected $errorMessage;
    protected $errorCode;
    protected $patternMessage;

    public function __construct(){
        $this->patternMessage     = 'It is not possible to impersonate self.';
        $this->errorMessage     = $this->patternMessage;
        $this->errorCode        = 403;
    }

    public function getErrorMessage(){
        return $this->errorMessage;
    }

    public function getErroCode(){
        return $this->errorCode;
    }
}