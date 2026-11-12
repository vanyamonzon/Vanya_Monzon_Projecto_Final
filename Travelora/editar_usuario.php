
<?php

// Iniciamos la sesión del usuario.
session_start();

// Conectamos con la base de datos.
require_once "conexion.php";

// Verificamos que haya iniciado sesión.
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

// Verificamos que sea administrador.
if ($_SESSION["rol"] !== "admin") {
    die("Acceso denegado.");
}

// Creamos un token de seguridad si todavía no existe.
if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Recuperamos y validamos el ID del usuario que queremos editar.
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

// Si el ID no es válido, volvemos al panel.
if (!$id || $id <= 0) {
    header("Location: admin_usuarios.php");
    exit();
}

// Buscamos al usuario activo en la base de datos.
$consulta = $conexion->prepare(
    "SELECT id, nombre, apellido, dni, correo
    FROM usuarios
    WHERE id = ? AND fecha_archivado IS NULL"
);

$consulta->execute([$id]);

// Recuperamos sus datos.
$usuario = $consulta->fetch(PDO::FETCH_ASSOC);

// Si no existe, mostramos un mensaje.
if (!$usuario) {
    die("El usuario no existe o está archivado.");
}

// Variable para mostrar mensajes.
$mensaje = "";

// Comprobamos si se envió el formulario.
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validamos el token de seguridad CSRF.
    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        die("Solicitud no válida.");
    }

    // Recuperamos los datos modificados.
    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $dni = trim($_POST["dni"] ?? "");
    $correo = trim($_POST["correo"] ?? "");

    // Comprobamos que no haya campos vacíos.
    if (
        empty($nombre) ||
        empty($apellido) ||
        empty($dni) ||
        empty($correo)
    ) {
        $mensaje = "Todos los campos son obligatorios.";

    // Validamos el formato del correo.
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "El correo no es válido.";

    } else {

        // Comprobamos que el DNI y el correo no pertenezcan
        // a otro usuario diferente al que estamos editando.
        $comprobar = $conexion->prepare(
            "SELECT id FROM usuarios
            WHERE (dni = ? OR correo = ?) AND id != ?"
        );

        $comprobar->execute([$dni, $correo, $id]);

        if ($comprobar->fetch()) {

            $mensaje = "El DNI o correo ya pertenece a otro usuario.";

        } else {

            // Actualizamos los datos únicamente si el usuario
            // todavía se encuentra activo.
            $actualizar = $conexion->prepare(
                "UPDATE usuarios
                SET nombre = ?, apellido = ?, dni = ?, correo = ?
                WHERE id = ? AND fecha_archivado IS NULL"
            );

            $actualizar->execute([
                $nombre,
                $apellido,
                $dni,
                $correo,
                $id
            ]);

            // Volvemos al panel de administración.
            header("Location: admin_usuarios.php");
            exit();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <!-- Configuración de caracteres. -->
    <meta charset="UTF-8">

    <!-- Adaptación a celulares y computadoras. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Título de la pestaña. -->
    <title>Editar usuario | Travelora</title>

    <!-- Conectamos la hoja de estilos principal. -->
    <link rel="stylesheet" href="style.css?v=11">

</head>

<body class="editar-page">

    <!-- Barra de navegación principal. -->
    <header class="navbar">

        <a href="index.php" class="logo">
            Travel<span>ora</span>
        </a>

        <nav class="menu">
            <a href="index.php">Inicio</a>
            <a href="destinos.php">Destinos</a>
            <a href="admin_usuarios.php">Administración</a>
        </nav>

    </header>

    <!-- Contenido principal de edición. -->
    <main class="editar-container">

        <section class="editar-card">

            <!-- Encabezado del formulario. -->
            <div class="editar-heading">

                <span class="editar-tag">GESTIÓN DE CUENTAS</span>

                <h1>Editar usuario</h1>

                <p>Modificá los datos de la cuenta seleccionada.</p>

            </div>

            <!-- Mostramos los posibles mensajes de error. -->
            <?php if (!empty($mensaje)): ?>

                <div class="editar-message">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>

            <?php endif; ?>

            <!-- Formulario con los datos actuales del usuario. -->
            <form method="POST" class="editar-form">

                <!-- Token de seguridad del formulario. -->
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php echo htmlspecialchars($_SESSION["csrf_token"]); ?>"
                >

                <div class="editar-field">

                    <label for="nombre">Nombre</label>

                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="<?php echo htmlspecialchars($usuario["nombre"]); ?>"
                        required
                    >

                </div>

                <div class="editar-field">

                    <label for="apellido">Apellido</label>

                    <input
                        type="text"
                        id="apellido"
                        name="apellido"
                        value="<?php echo htmlspecialchars($usuario["apellido"]); ?>"
                        required
                    >

                </div>

                <div class="editar-field">

                    <label for="dni">DNI</label>

                    <input
                        type="text"
                        id="dni"
                        name="dni"
                        value="<?php echo htmlspecialchars($usuario["dni"]); ?>"
                        required
                    >

                </div>

                <div class="editar-field">

                    <label for="correo">Correo electrónico</label>

                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        value="<?php echo htmlspecialchars($usuario["correo"]); ?>"
                        required
                    >

                </div>

                <!-- Botón para guardar los cambios. -->
                <button type="submit" class="editar-button">
                    Guardar cambios
                </button>

            </form>

            <!-- Volvemos al panel de administración. -->
            <a href="admin_usuarios.php" class="editar-back">
                Volver al panel de administración
            </a>

        </section>

    </main>

    <!-- Pie de página. -->
    <footer class="footer">
        <p>© 2026 Travelora | Tu próxima aventura te espera.</p>
    </footer>

</body>

</html>
