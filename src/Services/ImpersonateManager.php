<?php

namespace Rickycezar\Impersonate\Services;

use Rickycezar\Impersonate\Exceptions\AlreadyImpersonatingException;
use Rickycezar\Impersonate\Exceptions\CantBeImpersonatedException;
use Rickycezar\Impersonate\Exceptions\CantImpersonateException;
use Rickycezar\Impersonate\Exceptions\CantImpersonateSelfException;
use Rickycezar\Impersonate\Exceptions\NotImpersonatingException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Rickycezar\Impersonate\Events\LeaveImpersonation;
use Rickycezar\Impersonate\Events\TakeImpersonation;

class ImpersonateManager
{
    /**
     * @var Application
     */
    private $app;

    /**
     * UserFinder constructor.
     *
     * @param Application $app
     * @param Cache $cache
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param   int $id
     * @return  Model
     */
    public function findUserById($id)
    {
        $model = $this->app['config']->get('auth.providers.users.model');

        $user = call_user_func([
            $model,
            'findOrFail'
        ], $id);

        return $user;
    }

    /**
     * @return bool
     */
    public function isImpersonating()
    {
        return !empty($this->getImpersonatorId());
    }

    /**
     * @param   void
     * @return  int|null
     */
    public function getImpersonatorId()
    {
        return $this->app['auth']->parseToken()->getPayLoad()->get($this->getSessionKey());
    }

    /**
     * @param Model $from
     * @param Model $to
     * @return bool
     */
    public function take($from, $to)
    {
        if (!$this->isImpersonating()) {
            if (!($to->getKey() == $from->getKey())) {
                if ($to->canBeImpersonated()) {
                    if ($from->canImpersonate()) {
                        $this->app['auth']->logout();
                        $this->app['auth']->customClaims([$this->getSessionKey() => $from->getKey()]);
                        $token = $this->app['auth']->login($to);
                        $this->app['events']->fire(new TakeImpersonation($from, $to));
                        return $token;
                    } else {
                        throw new CantImpersonateException();
                    }
                } else {
                    throw new CantBeImpersonatedException();
                }
            } else {
                throw new CantImpersonateSelfException();
            }
        } else {
            throw new AlreadyImpersonatingException();
        }
    }

    /**
     * @return  bool
     */
    public function leave()
    {
        if ($this->isImpersonating()) {
            $impersonated = $this->app['auth']->user();
            $impersonator = $this->findUserById($this->getImpersonatorId());

            $this->app['auth']->logout();
            $token = $this->app['auth']->login($impersonator);

            $this->clear();

            $this->app['events']->fire(new LeaveImpersonation($impersonator, $impersonated));

            return $token;
        } else {
            throw new NotImpersonatingException();
        }
    }

    /**
     * @return void
     */
    public function clear()
    {
        $this->app['auth']->customClaims([$this->getSessionKey() => null]);
    }

    /**
     * @return string
     */
    public function getSessionKey()
    {
        return config('laravel-jwt-impersonate.session_key');
    }

    /**
     * @return  string
     */
    public function getTakeRedirectTo()
    {
        try {
            $uri = route(config('laravel-jwt-impersonate.take_redirect_to'));
        } catch (\InvalidArgumentException $e) {
            $uri = config('laravel-jwt-impersonate.take_redirect_to');
        }

        return $uri;
    }

    /**
     * @return  string
     */
    public function getLeaveRedirectTo()
    {
        try {
            $uri = route(config('laravel-jwt-impersonate.leave_redirect_to'));
        } catch (\InvalidArgumentException $e) {
            $uri = config('laravel-jwt-impersonate.leave_redirect_to');
        }

        return $uri;
    }
}
