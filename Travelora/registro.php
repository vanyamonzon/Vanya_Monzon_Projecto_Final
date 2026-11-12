
<?php

// =============================================
// CONEXIÓN Y PROCESAMIENTO DEL REGISTRO
// =============================================

// Incluimos la conexión con nuestra base de datos.
require_once "conexion.php";

// Variable para guardar los mensajes que mostraremos.
$mensaje = "";

// Comprobamos si el usuario envió el formulario.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recuperamos los datos ingresados y eliminamos espacios innecesarios.
    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $dni = trim($_POST["dni"]);
    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];

    // Verificamos que todos los campos estén completos.
    if (
        empty($nombre) ||
        empty($apellido) ||
        empty($dni) ||
        empty($correo) ||
        empty($password)
    ) {
        $mensaje = "Todos los campos son obligatorios.";

    // Validamos que el correo tenga un formato correcto.
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "El correo electrónico no es válido.";

    } else {

        // Buscamos si ya existe un usuario con el mismo DNI o correo.
        // Utilizamos una consulta preparada para proteger los datos.
        $consulta = $conexion->prepare(
            "SELECT id FROM usuarios WHERE dni = ? OR correo = ?"
        );

        // Ejecutamos la consulta con los valores ingresados.
        $consulta->execute([$dni, $correo]);

        // Comprobamos si encontramos un usuario existente.
        if ($consulta->fetch()) {

            $mensaje = "Ese DNI o correo ya está registrado.";

        } else {

            // Ciframos la contraseña antes de guardarla.
            // password_hash() genera un hash seguro.
            $password_segura = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Preparamos la consulta para insertar el nuevo usuario.
            $insertar = $conexion->prepare(
                "INSERT INTO usuarios
                (nombre, apellido, dni, correo, contraseña)
                VALUES (?, ?, ?, ?, ?)"
            );

            // Guardamos los datos del nuevo usuario.
            $insertar->execute([
                $nombre,
                $apellido,
                $dni,
                $correo,
                $password_segura
            ]);

            // Mostramos un mensaje de confirmación.
            $mensaje = "¡Cuenta creada correctamente en Travelora!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <!-- Configuración general de la página. -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Título de la pestaña del navegador. -->
    <title>Crear cuenta | Travelora</title>

    <!-- Conectamos la página con nuestra hoja de estilos CSS. -->
    <link rel="stylesheet" href="/Travelora/style.css?v=8">

</head>

<body class="auth-page">

    <!-- =====================================
         BARRA DE NAVEGACIÓN
    ====================================== -->

    <header class="navbar">

        <!-- Logo de Travelora. -->
        <h1 class="logo">Travel<span>ora</span></h1>

        <!-- Enlaces para navegar por la aplicación. -->
        <nav class="menu">
            <a href="index.php">Inicio</a>
            <a href="destinos.php">Destinos</a>
            <a href="login.php">Iniciar sesión</a>
        </nav>

    </header>


    <!-- =====================================
         CONTENEDOR PRINCIPAL DEL REGISTRO
    ====================================== -->

    <main class="auth-container">

        <!-- Tarjeta que contiene el formulario. -->
        <section class="auth-card">

            <!-- Título y descripción del registro. -->
            <div class="auth-heading">

                <span class="auth-tag">TU AVENTURA COMIENZA AQUÍ</span>

                <h2>Creá tu cuenta</h2>

                <p>
                    Registrate en Travelora y empezá a descubrir
                    el mundo.
                </p>

            </div>


            <!-- Mostramos los mensajes de validación o confirmación. -->
            <?php if (!empty($mensaje)): ?>

                <div class="auth-message">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>

            <?php endif; ?>


            <!-- =====================================
                 FORMULARIO DE REGISTRO
            ====================================== -->

            <form method="POST" action="registro.php" class="auth-form">

                <!-- Campo para ingresar el nombre. -->
                <div class="auth-field">
                    <label for="nombre">Nombre</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        placeholder="Tu nombre"
                        required
                    >
                </div>

                <!-- Campo para ingresar el apellido. -->
                <div class="auth-field">
                    <label for="apellido">Apellido</label>
                    <input
                        type="text"
                        id="apellido"
                        name="apellido"
                        placeholder="Tu apellido"
                        required
                    >
                </div>

                <!-- Campo para ingresar el DNI. -->
                <div class="auth-field">
                    <label for="dni">DNI</label>
                    <input
                        type="text"
                        id="dni"
                        name="dni"
                        placeholder="Tu número de documento"
                        required
                    >
                </div>

                <!-- Campo para ingresar el correo electrónico. -->
                <div class="auth-field">
                    <label for="correo">Correo electrónico</label>
                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        placeholder="ejemplo@correo.com"
                        required
                    >
                </div>

                <!-- Campo para crear una contraseña. -->
                <div class="auth-field">
                    <label for="password">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Creá una contraseña"
                        required
                    >
                </div>

                <!-- Botón que envía los datos al servidor. -->
                <button type="submit" class="auth-button">
                    Crear mi cuenta
                </button>

            </form>


            <!-- Enlace para quienes ya tienen una cuenta. -->
            <div class="auth-footer">
                ¿Ya tenés una cuenta?
                <a href="login.php">Iniciá sesión</a>
            </div>

            <!-- Enlace para regresar a la página principal. -->
            <a href="index.php" class="auth-back">
                ← Volver al inicio
            </a>

        </section>

    </main>


    <!-- =====================================
         PIE DE PÁGINA
    ====================================== -->

    <footer>
        <p>© 2026 Travelora - Tu próxima aventura comienza acá.</p>
    </footer>

</body>
</html>
