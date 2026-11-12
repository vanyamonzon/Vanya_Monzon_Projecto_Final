
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Travelora | Descubrí el mundo</title>

    <!-- Conectamos nuestra hoja de estilos. -->
   <link rel="stylesheet" href="/Travelora/style.css?v=3">
</head>

<body>

    <!-- Barra de navegación. -->
    <header>
        <nav class="navbar">
            <h1 class="logo">Travelora<span>.</span></h1>

            <div class="menu">
                <a href="index.php">Inicio</a>
                <a href="destinos.php">Destinos</a>
                <a href="login.php">Iniciar sesión</a>
                <a href="registro.php" class="boton-menu">Registrarse</a>
            </div>
        </nav>
    </header>

    <main>

        <!-- Presentación principal. -->
        <section class="portada">
            <div class="contenido-portada">

                <p class="subtitulo">TU PRÓXIMA AVENTURA COMIENZA AQUÍ</p>

                <h2>El mundo está esperando por vos.</h2>

                <p>
                    Descubrí nuevos lugares, viví experiencias únicas
                    y encontrá inspiración para tu próximo viaje.
                </p>

                <a href="registro.php" class="boton-principal">
                    Comenzar mi viaje
                </a>

            </div>
        </section>

        <!-- Sección de destinos. -->
        <section class="destinos" id="destinos">

            <p class="subtitulo">INSPIRATE Y EXPLORÁ</p>

            <h2>Destinos para soñar</h2>

            <div class="tarjetas">

                <article class="tarjeta">
                    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600"
                         alt="Playa tropical">
                    <h3>Maldivas</h3>
                    <p>Playas paradisíacas y aguas cristalinas.</p>
                </article>

                <article class="tarjeta">
                    <img src="https://images.unsplash.com/photo-1519046904884-53103b34b206?w=600"
                         alt="Paisaje de playa">
                    <h3>Brasil</h3>
                    <p>Sol, naturaleza y alegría en cada rincón.</p>
                </article>

                <article class="tarjeta">
                    <img src="https://images.unsplash.com/photo-1530789253388-582c481c54b0?w=600"
                         alt="Paisaje de montaña">
                    <h3>Indonesia</h3>
                    <p>Aventuras y paisajes inolvidables.</p>
                </article>

            </div>
        </section>

    </main>

    <!-- Pie de página. -->
    <footer>
        <p>© 2026 Travelora - Explorá el mundo a tu manera.</p>
    </footer>

</body>
</html>
