
<?php

// Iniciamos la sesión para identificar al usuario.
session_start();

// Incluimos la conexión con la base de datos.
require_once "conexion.php";

// Comprobamos que el usuario haya iniciado sesión.
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

// Comprobamos que tenga permisos de administrador.
if ($_SESSION["rol"] !== "admin") {
    die("Acceso denegado. No tenés permisos de administrador.");
}

// Creamos un token de seguridad para proteger los formularios.
if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Variables para mostrar mensajes del registro.
$mensajeRegistro = "";
$tipoMensaje = "";

// =============================================
// REGISTRO DE USUARIOS DESDE ADMINISTRACIÓN
// =============================================

// Comprobamos si se envió el formulario de nuevo usuario.
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["crear_usuario"])) {

    // Comprobamos que el token de seguridad sea válido.
    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        die("Solicitud no válida.");
    }

    // Recuperamos los datos ingresados.
    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $dni = trim($_POST["dni"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $password = $_POST["password"] ?? "";

    // Comprobamos que todos los campos estén completos.
    if (
        empty($nombre) ||
        empty($apellido) ||
        empty($dni) ||
        empty($correo) ||
        empty($password)
    ) {
        $mensajeRegistro = "Todos los campos son obligatorios.";
        $tipoMensaje = "error";

    // Validamos el formato del correo electrónico.
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensajeRegistro = "El correo electrónico no es válido.";
        $tipoMensaje = "error";

    } else {

        // Comprobamos que el DNI o correo no estén registrados.
        $consultaDuplicados = $conexion->prepare(
            "SELECT id FROM usuarios WHERE dni = ? OR correo = ?"
        );

        $consultaDuplicados->execute([$dni, $correo]);

        if ($consultaDuplicados->fetch()) {

            $mensajeRegistro = "Ese DNI o correo ya está registrado.";
            $tipoMensaje = "error";

        } else {

            // Protegemos la contraseña mediante un hash seguro.
            $passwordSegura = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Guardamos el nuevo usuario con el rol común.
            $insertar = $conexion->prepare(
                "INSERT INTO usuarios
                (nombre, apellido, dni, correo, contraseña, rol)
                VALUES (?, ?, ?, ?, ?, 'usuario')"
            );

            $insertar->execute([
                $nombre,
                $apellido,
                $dni,
                $correo,
                $passwordSegura
            ]);

            $mensajeRegistro = "¡Usuario registrado correctamente!";
            $tipoMensaje = "exito";
        }
    }
}

// =============================================
// BÚSQUEDA DE USUARIOS ACTIVOS
// =============================================

// Recuperamos el texto escrito en el buscador.
$busqueda = $_GET["buscar"] ?? "";

// Preparamos la consulta para obtener usuarios activos.
$consulta = $conexion->prepare(
    "SELECT id, nombre, apellido, dni, correo, rol
    FROM usuarios
    WHERE fecha_archivado IS NULL
    AND (
        nombre LIKE :buscar
        OR apellido LIKE :buscar
        OR dni LIKE :buscar
        OR correo LIKE :buscar
    )
    ORDER BY id DESC"
);

// Agregamos comodines para encontrar coincidencias parciales.
$consulta->bindValue(":buscar", "%" . $busqueda . "%");

// Ejecutamos la consulta.
$consulta->execute();

// Guardamos los usuarios encontrados.
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
    <title>Administración | Travelora</title>

    <!-- Conectamos la hoja de estilos principal. -->
    <link rel="stylesheet" href="style.css?v=10">

</head>

<body class="admin-page">

    <!-- Barra de navegación principal. -->
    <header class="navbar">

        <a href="index.php" class="logo">
            Travel<span>ora</span>
        </a>

        <nav class="menu">
            <a href="index.php">Inicio</a>
            <a href="destinos.php">Destinos</a>
            <a href="panel.php">Mi cuenta</a>
            <a href="cerrar_sesion.php">Cerrar sesión</a>
        </nav>

    </header>

    <!-- Contenido principal de administración. -->
    <main class="admin-container">

        <section class="admin-card">

            <!-- Encabezado del panel. -->
            <div class="admin-heading">

                <span class="admin-tag">GESTIÓN DE CUENTAS</span>

                <h1>Panel de administración</h1>

                <p>Administrá los usuarios registrados en Travelora.</p>

            </div>

            <!-- Acceso a los usuarios archivados. -->
            <div class="admin-top-actions">

                <a href="usuarios_archivados.php" class="admin-archive-link">
                    Ver usuarios archivados
                </a>

            </div>

            <!-- =====================================
                 FORMULARIO PARA CREAR USUARIOS
            ====================================== -->

            <div class="admin-heading">

                <h2>Registrar nuevo usuario</h2>

                <p>Completá los datos para crear una cuenta.</p>

            </div>

            <!-- Mostramos los mensajes del registro. -->
            <?php if (!empty($mensajeRegistro)): ?>

                <div class="auth-message">
                    <?php echo htmlspecialchars($mensajeRegistro); ?>
                </div>

            <?php endif; ?>

            <!-- Formulario de creación de usuarios. -->
            <form method="POST" action="admin_usuarios.php" class="auth-form">

                <!-- Token de seguridad del formulario. -->
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php echo htmlspecialchars($_SESSION["csrf_token"]); ?>"
                >

                <div class="auth-field">
                    <label for="nombre">Nombre</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        placeholder="Nombre"
                        required
                    >
                </div>

                <div class="auth-field">
                    <label for="apellido">Apellido</label>
                    <input
                        type="text"
                        id="apellido"
                        name="apellido"
                        placeholder="Apellido"
                        required
                    >
                </div>

                <div class="auth-field">
                    <label for="dni">DNI</label>
                    <input
                        type="text"
                        id="dni"
                        name="dni"
                        placeholder="Número de documento"
                        required
                    >
                </div>

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

                <div class="auth-field">
                    <label for="password">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Contraseña"
                        required
                    >
                </div>

                <!-- Este botón envía los datos para crear la cuenta. -->
                <button
                    type="submit"
                    name="crear_usuario"
                    class="auth-button"
                >
                    Registrar usuario
                </button>

            </form>

            <!-- =====================================
                 BUSCADOR DE USUARIOS
            ====================================== -->

            <h2>Usuarios registrados</h2>

            <form method="GET" action="admin_usuarios.php" class="admin-search">

                <div class="admin-search-field">

                    <label for="buscar">Buscar usuario</label>

                    <input
                        type="text"
                        id="buscar"
                        name="buscar"
                        placeholder="Nombre, apellido, DNI o correo"
                        value="<?php echo htmlspecialchars($busqueda); ?>"
                    >

                </div>

                <button type="submit" class="admin-search-button">
                    Buscar
                </button>

                <a href="admin_usuarios.php" class="admin-clear-button">
                    Limpiar
                </a>

            </form>

            <!-- Tabla con los usuarios registrados. -->
            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>DNI</th>
                            <th>Correo</th>
                            <th>Rol</th>
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
                                        <span class="admin-role">
                                            <?php echo htmlspecialchars($usuario["rol"]); ?>
                                        </span>
                                    </td>

                                    <!-- Acciones disponibles para cada usuario. -->
                                    <td class="admin-actions">

                                        <!-- Enlace para editar al usuario. -->
                                        <a
                                            href="editar_usuario.php?id=<?php echo urlencode($usuario['id']); ?>"
                                            class="admin-edit-button"
                                        >
                                            Editar
                                        </a>

                                        <!-- No permitimos archivar la propia cuenta. -->
                                        <?php if ($usuario["id"] != $_SESSION["id"]): ?>

                                            <!-- Formulario para archivar al usuario. -->
                                            <form
                                                method="POST"
                                                action="archivar_usuario.php"
                                                onsubmit="return confirm('¿Querés archivar este usuario?');"
                                            >

                                                <!-- Enviamos el ID del usuario seleccionado. -->
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

                                                <button type="submit" class="admin-delete-button">
                                                    Archivar
                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <span>Tu cuenta</span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <!-- Mensaje cuando no hay usuarios para mostrar. -->
                            <tr>
                                <td colspan="7" class="admin-no-results">
                                    No se encontraron usuarios.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

            <!-- Información sobre la cantidad de resultados. -->
            <p class="admin-results">
                Usuarios encontrados: <?php echo count($usuarios); ?>
            </p>

            <!-- Acceso para volver al panel personal. -->
            <div class="admin-bottom-actions">

                <a href="panel.php" class="admin-back-link">
                    Volver a mi cuenta
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
