<?php
// Incluir el header para acceder a la constante BASE_URL
require_once 'includes/header.php';

// Destruir todas las variables de sesión.
$_SESSION = array();

// Si se desea destruir la sesión completamente, borre también la cookie de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión.
session_destroy();

// Redirigir a la página de inicio usando la URL base
header("Location: " . BASE_URL . "index.php");
exit();
?>
