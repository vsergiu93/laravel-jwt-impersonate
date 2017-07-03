<?php

namespace Rickycezar\Impersonate\Middleware;

use Closure;
use Rickycezar\Impersonate\Exceptions\ProtectedAgainstImpersonationException;
use Rickycezar\Impersonate\Services\ImpersonateManager;

class ProtectFromImpersonation
{
    /**
     * Handle an incoming request.
     *
     * @param   \Illuminate\Http\Request $request
     * @param   \Closure $next
     * @return mixed
     * @throws ProtectedAgainstImpersonationException
     */
    public function handle($request, Closure $next)
    {
        $impersonate_manager = app()->make(ImpersonateManager::class);

        if ($impersonate_manager->isImpersonating()) {
            throw new ProtectedAgainstImpersonationException();
        }

        return $next($request);
    }
}
