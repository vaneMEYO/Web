<?php
require_once('conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'])) {
    $id_producto = intval($_POST['id_producto']);

    // Consulta para eliminar el producto basado en su ID
    $sql = "DELETE FROM producto WHERE pro_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id_producto]);

    // Redirigir de vuelta a la lista de productos con un mensaje de éxito
    header("Location: consultar.php?message=success");
    exit();
}
?>
