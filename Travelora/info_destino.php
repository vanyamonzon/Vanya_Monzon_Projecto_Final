
<?php

// Iniciamos la sesión para identificar al usuario.
session_start();

// Conectamos con la base de datos.
require_once "conexion.php";

// Recuperamos el ID del crucero desde la URL.
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

// Comprobamos que se haya recibido un ID válido.
if (!$id) {
    header("Location: destinos.php");
    exit();
}

// Consultamos los datos del crucero seleccionado.
$consulta = $conexion->prepare("
    SELECT *
    FROM cruceros
    WHERE id = :id
");

$consulta->execute(["id" => $id]);

// Recuperamos la información del crucero.
$crucero = $consulta->fetch(PDO::FETCH_ASSOC);

// Si el crucero no existe, regresamos al listado.
if (!$crucero) {
    header("Location: destinos.php");
    exit();
}

// Imágenes correspondientes a cada destino.
$imagenes = [
    "Maldivas" => "https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=1600",
    "Brasil" => "https://images.unsplash.com/photo-1483729558449-99ef09a8c325?w=1600",
    "Bali" => "https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=1600",
    "París" => "https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=1600",
    "Nueva York" => "https://images.unsplash.com/photo-1519608487953-e999c86e7455?w=1600",
    "Italia" => "https://images.unsplash.com/photo-1516483638261-f4dbaf036963?w=1600"
];

// Seleccionamos la imagen según el destino.
$imagen = $imagenes[$crucero["destino"]]
    ?? "https://images.unsplash.com/photo-1500375592092-40eb2168fd21?w=1600";

// Formateamos las fechas para mostrarlas.
$salida = date("d/m/Y", strtotime($crucero["fecha_salida"]));
$regreso = date("d/m/Y", strtotime($crucero["fecha_regreso"]));

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($crucero["destino"]); ?> | Travelora
    </title>

    <!-- Conectamos la hoja de estilos principal. -->
    <link rel="stylesheet" href="style.css?v=15">
</head>

<body class="info-destino-page">

    <!-- Barra de navegación principal. -->
    <header class="navbar">

        <h1 class="logo">Travel<span>ora</span></h1>

        <nav class="menu">
            <a href="index.php">Inicio</a>
            <a href="destinos.php">Cruceros</a>

            <?php if (isset($_SESSION["id"])): ?>

                <a href="panel.php">Mi cuenta</a>
                <a href="cerrar_sesion.php">Cerrar sesión</a>

            <?php else: ?>

                <a href="login.php">Iniciar sesión</a>
                <a href="registro.php" class="boton-menu">Registrarse</a>

            <?php endif; ?>

        </nav>

    </header>

    <!-- Portada del crucero seleccionado. -->
    <section class="info-portada"
        style="background-image: url('<?php echo htmlspecialchars($imagen, ENT_QUOTES, "UTF-8"); ?>');">

        <div class="info-portada-contenido">

            <p>
                <?php echo htmlspecialchars($crucero["pais"]); ?>
            </p>

            <h2>
                <?php echo htmlspecialchars($crucero["destino"]); ?>
            </h2>

            <span>Una nueva aventura comienza a bordo de Travelora.</span>

        </div>

    </section>

    <main class="info-contenedor">

        <!-- Presentación del viaje. -->
        <section class="info-presentacion">

            <div>

                <p class="info-etiqueta">DESCUBRÍ TU PRÓXIMO VIAJE</p>

                <h2>Una experiencia inolvidable en el mar</h2>

                <p class="info-descripcion">
                    <?php echo htmlspecialchars($crucero["descripcion"]); ?>
                </p>

                <p class="info-descripcion">
                    Todos nuestros viajes parten desde Buenos Aires
                    para que puedas disfrutar de una experiencia única
                    a bordo de Travelora.
                </p>

            </div>

            <div class="info-sello">

                <span>TRAVELORA</span>

                <strong>Explore<br>the world</strong>

            </div>

        </section>

        <!-- Información principal del crucero. -->
        <section class="info-datos">

            <div class="info-titulo-seccion">

                <p class="info-etiqueta">INFORMACIÓN DEL VIAJE</p>

                <h2>Detalles del crucero</h2>

            </div>

            <div class="info-datos-grid">

                <article>
                    <span>⚓</span>
                    <h3>Puerto de salida</h3>
                    <p>Buenos Aires</p>
                </article>

                <article>
                    <span>📅</span>
                    <h3>Fecha de salida</h3>
                    <p><?php echo $salida; ?></p>
                </article>

                <article>
                    <span>🛳️</span>
                    <h3>Fecha de regreso</h3>
                    <p><?php echo $regreso; ?></p>
                </article>

                <article>
                    <span>⏳</span>
                    <h3>Duración</h3>
                    <p>
                        <?php echo (int)$crucero["duracion"]; ?> días
                    </p>
                </article>

            </div>

        </section>

        <!-- Información sobre el precio y los lugares disponibles. -->
        <section class="info-experiencias">

            <div class="info-titulo-seccion">

                <p class="info-etiqueta">PREPARÁ TUS VALIJAS</p>

                <h2>Tu próximo viaje</h2>

            </div>

            <div class="info-experiencias-grid">

                <article class="info-experiencia-card">

                    <span>💰</span>

                    <h3>Precio por persona</h3>

                    <p>
                        $
                        <?php echo number_format($crucero["precio"], 0, ",", "."); ?>
                    </p>

                </article>

                <article class="info-experiencia-card">

                    <span>🎟️</span>

                    <h3>Lugares disponibles</h3>

                    <p>
                        <?php echo (int)$crucero["cupos"]; ?> pasajeros
                    </p>

                </article>

                <article class="info-experiencia-card">

                    <span>🚢</span>

                    <h3>Experiencia Travelora</h3>

                    <p>
                        Disfrutá de tu viaje y descubrí nuevos destinos
                        desde el mar.
                    </p>

                </article>

            </div>

        </section>

        <!-- Sección para iniciar el proceso de reserva. -->
        <section class="info-final">

            <p>EL MUNDO TE ESPERA</p>

            <h2>¿Te gustaría viajar con nosotros?</h2>

          
<a href="reservar.php?id=<?php echo (int)$crucero["id"]; ?>"
   class="boton-principal">

    Reservar este crucero

</a>
        </section>

    </main>

    <!-- Pie de página. -->
    <footer>

        <p>© 2026 Travelora - Tu próxima aventura comienza acá.</p>

    </footer>

</body>
</html>
