
<?php

// Iniciamos la sesión del usuario.
session_start();

// Conectamos con la base de datos.
require_once "conexion.php";

// Comprobamos que haya iniciado sesión.
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

// Comprobamos que sea administrador.
if ($_SESSION["rol"] !== "admin") {
    die("Acceso denegado.");
}

// Creamos un token de seguridad si todavía no existe.
if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Comprobamos si se solicitó reactivar un usuario.
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validamos el token de seguridad CSRF.
    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        die("Solicitud no válida.");
    }

    // Recuperamos y validamos el ID enviado por el formulario.
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if (!$id || $id <= 0) {
        die("El ID del usuario no es válido.");
    }

    // Quitamos la fecha de archivado.
    // Al colocar NULL, el usuario vuelve a estar activo.
    $consulta = $conexion->prepare(
        "UPDATE usuarios
        SET fecha_archivado = NULL
        WHERE id = ? AND fecha_archivado IS NOT NULL"
    );

    // Ejecutamos la consulta.
    $consulta->execute([$id]);

    // Actualizamos la página después de reactivar.
    header("Location: usuarios_archivados.php");
    exit();
}

// Buscamos los usuarios que están archivados.
$consulta = $conexion->prepare(
    "SELECT id, nombre, apellido, dni, correo
    FROM usuarios
    WHERE fecha_archivado IS NOT NULL
    ORDER BY id DESC"
);

// Ejecutamos la consulta.
$consulta->execute();

// Guardamos los resultados.
$usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <!-- Configuración de caracteres. -->
    <meta charset="UTF-8">

    <!-- Adaptación a celulares y computadoras. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Título de la pestaña. -->
    <title>Usuarios archivados | Travelora</title>

    <!-- Conectamos la hoja de estilos principal. -->
    <link rel="stylesheet" href="style.css?v=12">

</head>

<body class="archivados-page">

    <!-- Barra de navegación principal. -->
    <header class="navbar">

        <a href="index.php" class="logo">
            Travel<span>ora</span>
        </a>

        <nav class="menu">
            <a href="index.php">Inicio</a>
            <a href="destinos.php">Destinos</a>
            <a href="admin_usuarios.php">Administración</a>
            <a href="cerrar_sesion.php">Cerrar sesión</a>
        </nav>

    </header>

    <!-- Contenido principal de usuarios archivados. -->
    <main class="archivados-container">

        <section class="archivados-card">

            <!-- Encabezado de la página. -->
            <div class="archivados-heading">

                <span class="archivados-tag">GESTIÓN DE CUENTAS</span>

                <h1>Usuarios archivados</h1>

                <p>Desde acá podés recuperar las cuentas que fueron archivadas.</p>

            </div>

            <!-- Tabla con las cuentas archivadas. -->
            <div class="archivados-table-wrapper">

                <table class="archivados-table">

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>DNI</th>
                            <th>Correo</th>
                            <th>Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (count($usuarios) > 0): ?>

                            <?php foreach ($usuarios as $usuario): ?>

                                <tr>

                                    <!-- Mostramos los datos del usuario. -->
                                    <td><?php echo htmlspecialchars($usuario["id"]); ?></td>

                                    <td><?php echo htmlspecialchars($usuario["nombre"]); ?></td>

                                    <td><?php echo htmlspecialchars($usuario["apellido"]); ?></td>

                                    <td><?php echo htmlspecialchars($usuario["dni"]); ?></td>

                                    <td><?php echo htmlspecialchars($usuario["correo"]); ?></td>

                                    <td>

                                        <!-- Formulario para reactivar la cuenta. -->
                                        <form
                                            method="POST"
                                            onsubmit="return confirm('¿Querés reactivar este usuario?');"
                                        >

                                            <!-- Enviamos el ID del usuario. -->
                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?php echo htmlspecialchars($usuario['id']); ?>"
                                            >

                                            <!-- Enviamos el token de seguridad. -->
                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?php echo htmlspecialchars($_SESSION["csrf_token"]); ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="archivados-reactivate"
                                            >
                                                Reactivar
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <!-- Mensaje cuando no hay usuarios archivados. -->
                            <tr>
                                <td colspan="6" class="archivados-empty">
                                    No hay usuarios archivados.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

            <!-- Cantidad de cuentas archivadas. -->
            <p class="archivados-results">
                Usuarios archivados: <?php echo count($usuarios); ?>
            </p>

            <!-- Enlaces para volver a administración. -->
            <div class="archivados-actions">

                <a href="admin_usuarios.php" class="archivados-back">
                    Volver al panel de administración
                </a>

            </div>

        </section>

    </main>

    <!-- Pie de página. -->
    <footer class="footer">
        <p>© 2026 Travelora | Tu próxima aventura te espera.</p>
    </footer>

</body>

</html>
