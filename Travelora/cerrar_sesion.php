
<?php

// Iniciamos la sesión para poder acceder a ella.
session_start();

// Eliminamos todos los datos guardados en la sesión.
session_unset();

// Obtenemos la configuración de la cookie de sesión.
$parametrosCookie = session_get_cookie_params();

// Eliminamos la cookie de sesión del navegador.
setcookie(
    session_name(),
    "",
    time() - 42000,
    $parametrosCookie["path"],
    $parametrosCookie["domain"],
    $parametrosCookie["secure"],
    $parametrosCookie["httponly"]
);

// Destruimos la sesión por completo.
session_destroy();

// Redirigimos al usuario a la página de inicio de sesión.
header("Location: login.php");

// Detenemos la ejecución del código.
exit();

?>
