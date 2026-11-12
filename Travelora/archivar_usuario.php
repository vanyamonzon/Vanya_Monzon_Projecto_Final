
<?php

// Iniciamos la sesión.
session_start();

// Conectamos con la base de datos.
require_once "conexion.php";

// Comprobamos que haya una sesión iniciada.
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

// Comprobamos que el usuario sea administrador.
if ($_SESSION["rol"] !== "admin") {
    die("Acceso denegado.");
}

// Comprobamos que la operación se haya enviado mediante POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin_usuarios.php");
    exit();
}

// Validamos el token de seguridad CSRF.
if (
    !isset($_POST["csrf_token"]) ||
    !isset($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    die("Solicitud no válida.");
}

// Recuperamos y validamos el ID del usuario.
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    die("El ID del usuario no es válido.");
}

// Evitamos que el administrador archive su propia cuenta.
if ($id == $_SESSION["id"]) {
    die("No podés archivar tu propia cuenta.");
}

// Archivamos al usuario guardando la fecha actual.
$consulta = $conexion->prepare(
    "UPDATE usuarios
    SET fecha_archivado = NOW()
    WHERE id = ? AND fecha_archivado IS NULL"
);

// Ejecutamos la consulta.
$consulta->execute([$id]);

// Volvemos al panel de administración.
header("Location: admin_usuarios.php");
exit();

?>
