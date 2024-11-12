<?php
// Parámetros de conexión a la base de datos
$host = 'localhost';    // Dirección del servidor de base de datos (generalmente 'localhost')
$db = 'meyorki';        // Nombre de la base de datos
$user = 'root';         // Usuario de MySQL
$pass = '';             // Contraseña de MySQL (generalmente vacío en XAMPP)

try {
    // Crear una nueva conexión PDO
    $conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

    // Configurar PDO para que lance excepciones en caso de error
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Opcional: Mostrar un mensaje si la conexión es exitosa (puedes comentar esta línea en producción)
    // echo 'Conexión exitosa';
} catch (PDOException $e) {
    // Mostrar mensaje de error si ocurre una excepción
    echo 'Error de conexión: ' . $e->getMessage();
    exit(); // Detener la ejecución si hay un error de conexión
}
?>
