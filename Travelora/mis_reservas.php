<?php

// Iniciamos la sesión del usuario.
session_start();

// Conectamos con la base de datos.
require_once "conexion.php";

// Comprobamos que el usuario haya iniciado sesión.
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

// Recuperamos todas las reservas del usuario que inició sesión.
$consulta = $conexion->prepare(
    "SELECT
        reservas.id,
        reservas.pasajeros,
        reservas.fecha_reserva,
        reservas.estado,
        cruceros.destino,
        cruceros.pais,
        cruceros.fecha_salida,
        cruceros.fecha_regreso,
        cruceros.duracion,
        cruceros.precio
    FROM reservas
    INNER JOIN cruceros
        ON reservas.crucero_id = cruceros.id
    WHERE reservas.usuario_id = :usuario_id
    ORDER BY reservas.fecha_reserva DESC"
);

// Ejecutamos la consulta con el ID del usuario.
$consulta->execute([
    ":usuario_id" => $_SESSION["id"]
]);

// Guardamos los resultados.
$reservas = $consulta->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mis reservas | Travelora</title>

    <link rel="stylesheet" href="style.css?v=15">
</head>

<body class="info-destino-page">

    <!-- Barra de navegación -->
    <nav class="navbar">

        <a href="index.php" class="logo">Travelora</a>

        <div class="menu">
            <a href="index.php">Inicio</a>
            <a href="destinos.php">Cruceros</a>
            <a href="panel.php">Mi cuenta</a>
            <a href="cerrar_sesion.php">Cerrar sesión</a>
        </div>

    </nav>

    <!-- Contenido principal -->
    <main class="info-contenedor">

        <!-- Presentación de la página -->
        <section class="info-presentacion">

            <span class="info-etiqueta">TRAVELORA</span>

            <h1>Mis reservas</h1>

            <p class="info-descripcion">
                Consultá los detalles de tus próximos viajes.
            </p>

        </section>

        <!-- Listado de reservas -->
        <section class="info-experiencias">

            <h2 class="info-titulo-seccion">
                Tus viajes reservados
            </h2>

            <?php if (count($reservas) > 0): ?>

                <div class="info-experiencias-grid">

                    <?php foreach ($reservas as $reserva): ?>

                        <!-- Tarjeta de cada reserva -->
                        <article class="info-experiencia-card">

                            <h3>
                                <?php echo htmlspecialchars($reserva["destino"]); ?>
                            </h3>

                            <p>
                                <strong>País o región:</strong>
                                <?php echo htmlspecialchars($reserva["pais"]); ?>
                            </p>

                            <p>
                                <strong>Salida desde:</strong>
                                Buenos Aires
                            </p>

                            <p>
                                <strong>Fecha de salida:</strong>
                                <?php echo date("d/m/Y", strtotime($reserva["fecha_salida"])); ?>
                            </p>

                            <p>
                                <strong>Fecha de regreso:</strong>
                                <?php echo date("d/m/Y", strtotime($reserva["fecha_regreso"])); ?>
                            </p>

                            <p>
                                <strong>Duración:</strong>
                                <?php echo $reserva["duracion"]; ?> días
                            </p>

                            <p>
                                <strong>Pasajeros:</strong>
                                <?php echo $reserva["pasajeros"]; ?>
                            </p>

                            <p>
                                <strong>Precio por pasajero:</strong>
                                $<?php echo number_format($reserva["precio"], 0, ",", "."); ?>
                            </p>

                            <p>
                                <strong>Total estimado:</strong>
                                $<?php echo number_format(
                                    $reserva["precio"] * $reserva["pasajeros"],
                                    0,
                                    ",",
                                    "."
                                ); ?>
                            </p>

                            <p>
                                <strong>Fecha de reserva:</strong>
                                <?php echo date("d/m/Y H:i", strtotime($reserva["fecha_reserva"])); ?>
                            </p>

                            <p>
                                <strong>Estado:</strong>
                                <?php echo ucfirst(htmlspecialchars($reserva["estado"])); ?>
                            </p>

                            <p>
                                <strong>Número de reserva:</strong>
                                #<?php echo $reserva["id"]; ?>
                            </p>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <!-- Mensaje cuando todavía no hay reservas -->
                <div class="info-final">

                    <h2>Todavía no tenés reservas</h2>

                    <p>
                        Explorá nuestros cruceros y elegí tu próximo destino.
                    </p>

                    <a href="destinos.php" class="boton-principal">
                        Ver cruceros
                    </a>

                </div>

            <?php endif; ?>

        </section>

        <!-- Enlace para volver a explorar -->
        <section class="info-final">

            <a href="destinos.php" class="boton-principal">
                Explorar más cruceros
            </a>

        </section>

    </main>

</body>
</html>