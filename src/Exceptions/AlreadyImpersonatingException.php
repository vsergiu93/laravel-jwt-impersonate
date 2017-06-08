<?php

namespace Rickycezar\Impersonate\Exceptions;


class AlreadyImpersonatingException extends \Exception{

    protected $errorMessage;
    protected $errorCode;
    protected $patternMessage;

    public function __construct(){
        $this->patternMessage     = 'This user is already impersonating someone.';
        $this->errorMessage     = $this->patternMessage;
        $this->message     = $this->patternMessage;
        $this->errorCode        = 403;
    }

    public function getErrorMessage(){
        return $this->errorMessage;
    }

    public function getErroCode(){
        return $this->errorCode;
    }
}