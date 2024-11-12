<?php
require_once('conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = intval($_POST['pro_id']);
    $nombre = $_POST['nom_p'];
    $precio = $_POST['pre_p'];
    $cantidad = $_POST['can_p'];
    $proveedor = $_POST['pro_p'];
    $telefono = $_POST['tel_p'];
    $descripcion = $_POST['des_p'];

    $sql = "UPDATE producto SET nom_p = ?, pre_p = ?, can_p = ?, pro_p = ?, tel_p = ?, des_p = ? WHERE pro_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$nombre, $precio, $cantidad, $proveedor, $telefono, $descripcion, $id_producto]);

    echo "Producto actualizado correctamente";
}
?>