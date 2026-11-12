
<?php

// 1. Guardamos el servidor donde está alojada nuestra base de datos.
$host = "localhost";

// 2. Indicamos el nombre de la base de datos que creamos en phpMyAdmin.
$dbname = "travelora";

// 3. Usuario de MySQL. En XAMPP normalmente es "root".
$user = "root";

// 4. Contraseña de MySQL. En una instalación local de XAMPP suele estar vacía.
$password = "";

// 5. try permite intentar realizar una operación que podría generar un error.
try {

    // 6. Creamos la conexión entre PHP y MySQL utilizando PDO.
    // También indicamos el servidor, la base de datos y la codificación de caracteres.
    $conexion = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password
    );

    // 7. Configuramos PDO para que informe los errores de conexión o consultas.
    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

// 8. catch captura el error si la conexión falla.
} catch (PDOException $e) {

    // 9. Mostramos un mensaje indicando que hubo un problema.
    die("Error de conexión: " . $e->getMessage());
}

?>
