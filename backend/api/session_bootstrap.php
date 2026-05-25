<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);

    // خليه 0 على localhost
    // خليه 1 فقط لما يصير الموقع على https
    ini_set('session.cookie_secure', 0);

    session_set_cookie_params([
        'httponly' => true,
        'secure'   => false, // خليه true مع https
        'samesite' => 'Lax'
    ]);

    session_start();
}