<?php
// incluir el archivo de conexión
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    try {
        // Preparar la consulta SQL
        $sql = "UPDATE clientes SET nombre = :nombre, email = :email, telefono = :telefono, direccion = :direccion WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        
        // Vincular los parámetros
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        
        // Ejecutar la consulta
        $stmt->execute();
        
        // Redireccionar a la lista de clientes
        header("Location: lista_cliente.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
