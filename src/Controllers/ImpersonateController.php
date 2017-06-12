<?php

namespace Rickycezar\Impersonate\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Rickycezar\Impersonate\Services\ImpersonateManager;

class ImpersonateController extends Controller
{
    /** @var ImpersonateManager */
    protected $manager;

    public function __construct()
    {
        $this->manager = app()->make(ImpersonateManager::class);
    }

    /**
     * @param   int $id
     * @return  RedirectResponse
     */
    public function take(Request $request, $id)
    {
        $persona = $this->manager->findUserById(intval($id));
        $impersonator = $request->user();
        $token = $this->manager->take($impersonator, $persona);
        $response = [
            'data' => [
                'requested_id' => intval($id),
                'persona' => $request->user(),
                'impersonator' => $impersonator,
                'token' => $token,
            ],
        ];
        return response()->json($response);
    }

    /*
     * @return Response
     */
    public function leave(Request $request)
    {
        $token = $request->user()->leaveImpersonation();
        $response = [
            'data' => [
                'persona' => $request->user(),
                'token' => $token,
            ],
        ];
        return response()->json($response);
    }

    public function info(Request $request)
    {
        $token = $request->bearerToken();
        $response = [
            'impersonating' => $this->manager->isImpersonating(),
            'data' => [
                'persona' => $request->user(),
                'impersonator' => $this->manager->isImpersonating() ? $this->manager->findUserById($this->manager->getImpersonatorId()) : null,
                'token' => $token,
            ],
        ];
        return response()->json($response);
    }
}
