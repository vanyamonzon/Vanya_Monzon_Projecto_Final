
<?php

// =============================================
// INICIO DE SESIÓN Y CONEXIÓN
// =============================================

// Iniciamos una sesión para guardar los datos del usuario.
session_start();

// Conectamos con nuestra base de datos.
require_once "conexion.php";

// Variable para mostrar mensajes en pantalla.
$mensaje = "";

// Comprobamos si el usuario envió el formulario.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recuperamos los datos ingresados.
    $correo = trim($_POST["correo"]);
    $password = $_POST["password"];

    // Buscamos al usuario y comprobamos que no esté archivado.
    $consulta = $conexion->prepare(
        "SELECT * FROM usuarios
        WHERE correo = ? AND fecha_archivado IS NULL"
    );

    // Ejecutamos la consulta utilizando el correo ingresado.
    $consulta->execute([$correo]);

    // Recuperamos los datos del usuario encontrado.
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    // Verificamos que el usuario exista y que la contraseña sea correcta.
    if ($usuario && password_verify($password, $usuario["contraseña"])) {

        // Renovamos el identificador para proteger la sesión.
        session_regenerate_id(true);

        // Guardamos los datos necesarios del usuario en la sesión.
        $_SESSION["id"] = $usuario["id"];
        $_SESSION["nombre"] = $usuario["nombre"];
        $_SESSION["rol"] = $usuario["rol"];

        // Redirigimos al usuario a su panel personal.
        header("Location: panel.php");
        exit();

    } else {

        // Mostramos un mensaje si los datos son incorrectos.
        $mensaje = "Correo o contraseña incorrectos.";
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
    <title>Iniciar sesión | Travelora</title>

    <!-- Conectamos la página con nuestra hoja de estilos. -->
    <link rel="stylesheet" href="/Travelora/style.css?v=8">

</head>

<body class="auth-page">

    <!-- =====================================
         BARRA DE NAVEGACIÓN
    ====================================== -->

    <header class="navbar">

        <!-- Logo principal de Travelora. -->
        <h1 class="logo">Travel<span>ora</span></h1>

        <!-- Menú de navegación de la aplicación. -->
        <nav class="menu">
            <a href="index.php">Inicio</a>
            <a href="destinos.php">Destinos</a>
            <a href="registro.php" class="boton-menu">Registrarse</a>
        </nav>

    </header>


    <!-- =====================================
         CONTENEDOR DEL INICIO DE SESIÓN
    ====================================== -->

    <main class="auth-container">

        <!-- Tarjeta principal del formulario. -->
        <section class="auth-card">

            <!-- Título y presentación. -->
            <div class="auth-heading">

                <span class="auth-tag">BIENVENIDO A TRAVELORA</span>

                <h2>Iniciá sesión</h2>

                <p>
                    Ingresá a tu cuenta y seguí descubriendo
                    nuevos destinos.
                </p>

            </div>


            <!-- Mostramos un mensaje si los datos son incorrectos. -->
            <?php if (!empty($mensaje)): ?>

                <div class="auth-message">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>

            <?php endif; ?>


            <!-- =====================================
                 FORMULARIO DE INICIO DE SESIÓN
            ====================================== -->

            <form method="POST" action="login.php" class="auth-form">

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


                <!-- Campo para ingresar la contraseña. -->
                <div class="auth-field">

                    <label for="password">Contraseña</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Ingresá tu contraseña"
                        required
                    >

                </div>


                <!-- Botón que envía los datos para iniciar sesión. -->
                <button type="submit" class="auth-button">
                    Ingresar a mi cuenta
                </button>

            </form>


            <!-- Enlace para quienes todavía no tienen una cuenta. -->
            <div class="auth-footer">

                ¿Todavía no tenés una cuenta?

                <a href="registro.php">Registrate</a>

            </div>


            <!-- Enlace para volver a la página principal. -->
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
