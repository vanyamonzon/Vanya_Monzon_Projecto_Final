
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

// Recuperamos el ID del crucero desde el formulario o la URL.
$idCrucero = $_SERVER["REQUEST_METHOD"] === "POST"
    ? filter_input(INPUT_POST, "crucero_id", FILTER_VALIDATE_INT)
    : filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

// Variables para mostrar mensajes.
$mensaje = "";

// Comprobamos que el ID sea válido.
if (!$idCrucero) {
    header("Location: destinos.php");
    exit();
}

// Si se envió el formulario, procesamos la reserva.
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recuperamos la cantidad de pasajeros.
    $pasajeros = filter_input(INPUT_POST, "pasajeros", FILTER_VALIDATE_INT);

    // Comprobamos que la cantidad sea válida.
    if (!$pasajeros || $pasajeros < 1) {

        $mensaje = "Ingresá una cantidad válida de pasajeros.";

    } else {

        try {

            // Iniciamos una transacción para proteger los cupos.
            $conexion->beginTransaction();

            // Buscamos el crucero y bloqueamos su registro durante la operación.
            $consulta = $conexion->prepare(
                "SELECT * FROM cruceros WHERE id = :id FOR UPDATE"
            );

            $consulta->execute([
                ":id" => $idCrucero
            ]);

            $crucero = $consulta->fetch(PDO::FETCH_ASSOC);

            // Comprobamos que el crucero exista.
            if (!$crucero) {

                throw new Exception("El crucero seleccionado no existe.");

            }

            // Verificamos que haya cupos suficientes.
            if ($pasajeros > $crucero["cupos"]) {

                throw new Exception("No hay suficientes cupos disponibles.");

            }

            // Registramos la reserva asociándola al usuario y al crucero.
            $consulta = $conexion->prepare(
                "INSERT INTO reservas
                (usuario_id, crucero_id, pasajeros, estado)
                VALUES
                (:usuario_id, :crucero_id, :pasajeros, 'pendiente')"
            );

            $consulta->execute([
                ":usuario_id" => $_SESSION["id"],
                ":crucero_id" => $idCrucero,
                ":pasajeros" => $pasajeros
            ]);

            // Descontamos los cupos reservados.
            $consulta = $conexion->prepare(
                "UPDATE cruceros
                SET cupos = cupos - :pasajeros
                WHERE id = :id"
            );

            $consulta->execute([
                ":pasajeros" => $pasajeros,
                ":id" => $idCrucero
            ]);

            // Confirmamos todos los cambios en la base de datos.
            $conexion->commit();

            // Redirigimos al usuario a sus reservas.
            header("Location: mis_reservas.php");
            exit();

        } catch (Exception $e) {

            // Si ocurre un error, deshacemos los cambios.
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }

            // Guardamos el mensaje para mostrarlo en pantalla.
            $mensaje = $e->getMessage();
        }
    }
}

// Recuperamos los datos del crucero para mostrarlos.
$consulta = $conexion->prepare(
    "SELECT * FROM cruceros WHERE id = :id"
);

$consulta->execute([
    ":id" => $idCrucero
]);

$crucero = $consulta->fetch(PDO::FETCH_ASSOC);

// Si el crucero no existe, volvemos al listado.
if (!$crucero) {
    header("Location: destinos.php");
    exit();
}

// Convertimos las fechas al formato día/mes/año.
$salida = date("d/m/Y", strtotime($crucero["fecha_salida"]));
$regreso = date("d/m/Y", strtotime($crucero["fecha_regreso"]));

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reservar crucero | Travelora</title>

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

    <!-- Contenido principal de la reserva -->
    <main class="info-contenedor">

        <section class="info-presentacion">

            <span class="info-etiqueta">TRAVELORA</span>

            <h1>Reservá tu crucero</h1>

            <p class="info-descripcion">
                Completá los datos para reservar tu próximo viaje.
            </p>

        </section>

        <!-- Información del crucero seleccionado -->
        <section class="info-datos">

            <h2 class="info-titulo-seccion">
                Tu viaje seleccionado
            </h2>

            <div class="info-datos-grid">

                <div>
                    <strong>Destino</strong>
                    <p><?php echo htmlspecialchars($crucero["destino"]); ?></p>
                </div>

                <div>
                    <strong>País o región</strong>
                    <p><?php echo htmlspecialchars($crucero["pais"]); ?></p>
                </div>

                <div>
                    <strong>Salida desde</strong>
                    <p>Buenos Aires</p>
                </div>

                <div>
                    <strong>Fecha de salida</strong>
                    <p><?php echo $salida; ?></p>
                </div>

                <div>
                    <strong>Fecha de regreso</strong>
                    <p><?php echo $regreso; ?></p>
                </div>

                <div>
                    <strong>Duración</strong>
                    <p><?php echo $crucero["duracion"]; ?> días</p>
                </div>

                <div>
                    <strong>Precio por pasajero</strong>
                    <p>$<?php echo number_format($crucero["precio"], 0, ",", "."); ?></p>
                </div>

                <div>
                    <strong>Cupos disponibles</strong>
                    <p><?php echo $crucero["cupos"]; ?></p>
                </div>

            </div>

        </section>

<!-- Formulario para realizar la reserva -->
<section class="info-final">

    <p>RESERVÁ TU PRÓXIMA AVENTURA</p>

    <h2>¿Cuántas personas van a viajar?</h2>

    <p>
        Seleccioná la cantidad de pasajeros para calcular
        el precio total de tu viaje.
    </p>

    <!-- Mostramos los mensajes de error -->
    <?php if ($mensaje !== ""): ?>
        <p class="mensaje-error">
            <?php echo htmlspecialchars($mensaje); ?>
        </p>
    <?php endif; ?>

    <form action="reservar.php" method="POST">

        <input
            type="hidden"
            name="crucero_id"
            value="<?php echo $crucero["id"]; ?>"
        >

        <label for="pasajeros">
            Cantidad de pasajeros
        </label>

        <input
            type="number"
            id="pasajeros"
            name="pasajeros"
            min="1"
            max="<?php echo $crucero["cupos"]; ?>"
            value="1"
            required
            oninput="calcularTotal()"
        >

        <div>
            <p>Precio por persona:</p>

            <strong>
                $<?php echo number_format($crucero["precio"], 0, ",", "."); ?>
            </strong>
        </div>

        <div>
            <p>Precio total estimado:</p>

            <strong id="precio-total">
                $<?php echo number_format($crucero["precio"], 0, ",", "."); ?>
            </strong>
        </div>

        <button type="submit" class="boton-principal">
            Confirmar reserva
        </button>

    </form>

    <p>
        La reserva quedará registrada como pendiente.
        No se realizará ningún pago en esta demostración.
    </p>

    <a href="info_destino.php?id=<?php echo $crucero["id"]; ?>">
        Volver al crucero
    </a>

</section>

<!-- Calculamos automáticamente el precio según los pasajeros. -->
<script>

    // Guardamos el precio individual del crucero.
    const precioPersona = <?php echo (float)$crucero["precio"]; ?>;

    // Actualizamos el precio total cuando cambia la cantidad.
    function calcularTotal() {

        const pasajeros = document.getElementById("pasajeros").value;

        const total = precioPersona * Math.max(0, Number(pasajeros));

        document.getElementById("precio-total").textContent =
            "$" + total.toLocaleString("es-AR");

    }

</script>
