<?php

namespace Rickycezar\Impersonate\Exceptions;


class CantBeImpersonatedException extends \Exception{

    protected $errorMessage;
    protected $errorCode;
    protected $patternMessage;

    public function __construct(){
        $this->patternMessage     = 'Target user can\'t be impersonated.';
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