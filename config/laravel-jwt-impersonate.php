<?php

return [
    
    /**
     * The key used to store the original user id inside the token.
     */
    'session_key' => 'impersonated_by',

    /**
     * The alias for the authentication middleware to be used in the routes.
     */
    'auth_middleware' => 'auth',

];
