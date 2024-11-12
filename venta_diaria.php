<?php
require('conexion.php'); // Conexión a la base de datos

// Obtener la fecha actual en formato YYYY-MM-DD
$fechaActual = date('Y-m-d');

// Consulta para obtener la cantidad total de productos vendidos y el total por cada producto del día actual
$sql = "
    SELECT 
        p.nom_p AS nombre_producto,
        SUM(d.can_d) AS cantidad_total,
        SUM(d.can_d * p.pre_p) AS total_producto
    FROM 
        detalle d
    JOIN 
        producto p ON d.fk_prod = p.pro_id
    JOIN 
        factura f ON d.fk_fac_id = f.fac_id
    WHERE 
        f.fec_f = :fechaActual
    GROUP BY 
        p.nom_p
";

$consulta = $conexion->prepare($sql);
$consulta->bindParam(':fechaActual', $fechaActual);
$consulta->execute();

// Calcular el total de todas las ventas del día
$totalGeneral = 0;

echo "<h2>Reporte de Ventas del Día</h2>";
echo "<table border='1'>";
echo "<tr><th>Producto</th><th>Cantidad Vendida</th><th>Total Producto</th></tr>";

while ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>{$fila['nombre_producto']}</td>";
    echo "<td>{$fila['cantidad_total']}</td>";
    echo "<td>$" . number_format($fila['total_producto'], 2) . "</td>";
    echo "</tr>";

    // Sumar el total de cada producto al total general
    $totalGeneral += $fila['total_producto'];
}

echo "<tr><td colspan='2'><strong>Total General</strong></td><td><strong>$" . number_format($totalGeneral, 2) . "</strong></td></tr>";
echo "</table>";
?>
