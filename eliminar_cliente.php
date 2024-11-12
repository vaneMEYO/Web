<?php
// incluir el archivo de conexión
require 'conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Preparar la consulta SQL
        $sql = "DELETE FROM clientes WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        
        // Vincular el parámetro
        $stmt->bindParam(':id', $id);
        
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
