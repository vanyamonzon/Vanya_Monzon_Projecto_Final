
<?php

// Iniciamos la sesión del usuario.
session_start();

// Conectamos con la base de datos.
require_once "conexion.php";

// Consultamos los cruceros disponibles.
$consulta = $conexion->prepare("
    SELECT *
    FROM cruceros
    ORDER BY fecha_salida ASC
");

$consulta->execute();

// Guardamos todos los cruceros obtenidos.
$cruceros = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Imágenes correspondientes a cada destino.
$imagenes = [
    "Maldivas" => "https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=800",
    "Brasil" => "https://images.unsplash.com/photo-1483729558449-99ef09a8c325?w=800",
    "Bali" => "https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800",
    "París" => "https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800",
    "Nueva York" => "https://images.unsplash.com/photo-1519608487953-e999c86e7455?w=800",
    "Italia" => "https://images.unsplash.com/photo-1516483638261-f4dbaf036963?w=800"
];

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cruceros | Travelora</title>

    <!-- Conectamos la hoja de estilos principal. -->
    <link rel="stylesheet" href="/Travelora/style.css?v=15">
</head>

<body>

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

    <!-- Presentación de los viajes disponibles. -->
    <section class="encabezado-destinos">

        <p class="subtitulo">VIVÍ LA EXPERIENCIA TRAVELORA</p>

        <h2>Encontrá tu próximo crucero</h2>

        <p>
            Descubrí nuestros viajes, explorá nuevos destinos
            y preparate para una experiencia inolvidable.
        </p>

    </section>

    <!-- Listado de cruceros obtenidos desde MySQL. -->
    <main class="lista-destinos">

        <?php if (count($cruceros) > 0): ?>

            <?php foreach ($cruceros as $crucero): ?>

                <?php
                // Recuperamos la imagen correspondiente al destino.
                $imagen = $imagenes[$crucero["destino"]] ?? 
                    "https://images.unsplash.com/photo-1500375592092-40eb2168fd21?w=800";

                // Formateamos las fechas para mostrarlas.
                $salida = date("d/m/Y", strtotime($crucero["fecha_salida"]));
                $regreso = date("d/m/Y", strtotime($crucero["fecha_regreso"]));
                ?>

                <article class="destino">

                    <!-- Imagen del destino del crucero. -->
                    <img
                        src="<?php echo htmlspecialchars($imagen, ENT_QUOTES, "UTF-8"); ?>"
                        alt="<?php echo htmlspecialchars($crucero["destino"]); ?>"
                    >

                    <div class="info-destino">

                        <span>
                            <?php echo htmlspecialchars($crucero["pais"]); ?>
                        </span>

                        <h3>
                            <?php echo htmlspecialchars($crucero["destino"]); ?>
                        </h3>

                        <p>
                            <?php echo htmlspecialchars($crucero["descripcion"]); ?>
                        </p>

                        <p>
                            🚢 Salida: Buenos Aires
                        </p>

                        <p>
                            📅 <?php echo $salida; ?> al <?php echo $regreso; ?>
                        </p>

                        <p>
                            ⏳ <?php echo (int)$crucero["duracion"]; ?> días
                        </p>

                        <p>
                            💰 Desde $
                            <?php echo number_format($crucero["precio"], 0, ",", "."); ?>
                            por persona
                        </p>

                        <a
                            href="info_destino.php?id=<?php echo (int)$crucero["id"]; ?>"
                            class="boton-principal"
                        >
                            Ver crucero
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        <?php else: ?>

            <p>No hay cruceros disponibles por el momento.</p>

        <?php endif; ?>

    </main>

    <!-- Pie de página. -->
    <footer>
        <p>© 2026 Travelora - Tu próxima aventura comienza acá.</p>
    </footer>

</body>
</html>
