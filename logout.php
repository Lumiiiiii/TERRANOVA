<?php
session_start();

// Cancella tutte le variabili di sessione
$_SESSION = [];

// Cancella il cookie di sessione se esiste
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Distrugge la sessione
session_destroy();

// Reindirizza al login
header('Location: login.php');
exit;
