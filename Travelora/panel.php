
<?php

// Iniciamos la sesión para identificar al usuario.
session_start();

// Comprobamos que haya iniciado sesión.
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

// Incluimos la conexión con la base de datos.
require_once "conexion.php";

// Buscamos los datos personales del usuario conectado.
$consulta = $conexion->prepare(
    "SELECT nombre, apellido, dni, correo, rol
    FROM usuarios
    WHERE id = ?"
);

$consulta->execute([$_SESSION["id"]]);

// Recuperamos la información del usuario.
$usuario = $consulta->fetch(PDO::FETCH_ASSOC);

// Si no encontramos la cuenta, cerramos el acceso.
if (!$usuario) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Guardamos los datos para mostrarlos en pantalla.
$nombre = $usuario["nombre"];
$apellido = $usuario["apellido"];
$dni = $usuario["dni"];
$correo = $usuario["correo"];
$rol = $usuario["rol"];

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <!-- Configuramos los caracteres de la página. -->
    <meta charset="UTF-8">

    <!-- Adaptamos la página a celulares y computadoras. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Título de la pestaña. -->
    <title>Mi cuenta | Travelora</title>

    <!-- Conectamos la hoja de estilos principal. -->
    <link rel="stylesheet" href="style.css?v=16">

</head>

<body class="panel-page">

    <!-- Barra de navegación principal. -->
    <header class="navbar">

        <a href="index.php" class="logo">
            Travel<span>ora</span>
        </a>

        <nav class="menu">
            <a href="index.php">Inicio</a>
            <a href="destinos.php">Cruceros</a>
            <a href="cerrar_sesion.php">Cerrar sesión</a>
        </nav>

    </header>

    <!-- Contenido principal del panel. -->
    <main class="panel-container">

        <section class="panel-card">

            <!-- Presentación del usuario. -->
            <div class="panel-heading">

                <span class="panel-tag">MI ESPACIO PERSONAL</span>

                <h1>
                    ¡Bienvenido/a, <?php echo htmlspecialchars($nombre); ?>!
                </h1>

                <p>Tu próximo viaje comienza acá.</p>

            </div>

            <!-- Información personal de la cuenta. -->
            <div class="panel-info">

                <div class="panel-detail">
                    <span>Nombre</span>
                    <strong><?php echo htmlspecialchars($nombre); ?></strong>
                </div>

                <div class="panel-detail">
                    <span>Apellido</span>
                    <strong><?php echo htmlspecialchars($apellido); ?></strong>
                </div>

                <div class="panel-detail">
                    <span>DNI</span>
                    <strong><?php echo htmlspecialchars($dni); ?></strong>
                </div>

                <div class="panel-detail">
                    <span>Correo electrónico</span>
                    <strong><?php echo htmlspecialchars($correo); ?></strong>
                </div>

                <div class="panel-detail">
                    <span>Tipo de cuenta</span>
                    <strong>
                        <?php
                        // Mostramos un nombre más claro para cada rol.
                        echo ($rol === "admin") ? "Administrador" : "Usuario";
                        ?>
                    </strong>
                </div>

            </div>

            <!-- Acciones disponibles para el usuario. -->
            <div class="panel-actions">

                <!-- Acceso al listado de cruceros. -->
                <a href="destinos.php" class="panel-button">
                    Explorar cruceros
                </a>

                <!-- Acceso a las reservas del usuario. -->
                <a href="mis_reservas.php" class="panel-button panel-button-secondary">
                    Mis reservas
                </a>

                <?php if ($rol === "admin"): ?>

                    <!-- Este acceso solo aparece para administradores. -->
                    <a href="admin_usuarios.php" class="panel-button panel-button-secondary">
                        Administrar usuarios
                    </a>

                <?php endif; ?>

                <!-- Opción para cerrar la sesión. -->
                <a href="cerrar_sesion.php" class="panel-button panel-button-secondary">
                    Cerrar sesión
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
