<?php

namespace Rickycezar\Impersonate\Exceptions;


class NotImpersonatingException extends \Exception{

    protected $errorMessage;
    protected $errorCode;
    protected $patternMessage;

    public function __construct(){
        $this->patternMessage   = 'This user is not impersonating.';
        $this->errorMessage     = $this->patternMessage;
        $this->errorCode        = 403;
    }

    public function getErrorMessage(){
        return $this->errorMessage;
    }

    public function getErrorCode(){
        return $this->errorCode;
    }
}