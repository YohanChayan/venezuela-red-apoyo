<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin access token
    |--------------------------------------------------------------------------
    |
    | Secret token for the admin panel. There is no admin registration: whoever
    | holds the link /admin/acceso/{token} gains admin access for the session.
    | Keep it out of version control and rotate it if leaked.
    |
    */
    'token' => env('ADMIN_TOKEN', ''),
];
