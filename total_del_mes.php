<?php
session_start();
require('conexion.php'); // Conexión a la base de datos
require('fpdf/fpdf.php'); // Biblioteca FPDF

// Función para generar el PDF de ventas del mes
function generarPDF($ventas, $mes, $año) {
    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'Ventas del Mes', 0, 1, 'C');
            $this->Ln(5);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
        }

        function Body($ventas) {
            $this->SetFont('Arial', '', 12);
            $this->Cell(0, 10, "Mes: " . date('F'), 0, 1); // Nombre del mes
            $this->Ln(5);

            $this->Cell(30, 10, 'ID', 1);
            $this->Cell(80, 10, 'Producto', 1);
            $this->Cell(30, 10, 'Cantidad', 1);
            $this->Cell(30, 10, 'IVA (19%)', 1);
            $this->Cell(30, 10, 'Subtotal', 1);
            $this->Cell(30, 10, 'Precio', 1);
            $this->Ln();

            foreach ($ventas as $venta) {
                $iva = $venta['subtotal'] * 0.19;
                $this->Cell(30, 10, $venta['id'], 1);
                $this->Cell(80, 10, $venta['producto'], 1);
                $this->Cell(30, 10, $venta['cantidad'], 1);
                $this->Cell(30, 10, number_format($iva, 2), 1); // IVA
                $this->Cell(30, 10, number_format($venta['subtotal'], 2), 1); // Subtotal
                $this->Cell(30, 10, number_format($venta['precio'], 2), 1); // Precio
                $this->Ln();
            }

            $this->Ln(10);
            $this->Cell(0, 10, "Total: $" . number_format(array_sum(array_column($ventas, 'subtotal')), 2), 0, 1, 'R');
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->Body($ventas);
    $pdf->Output('F', "ventas_total_mes_$mes-$anio.pdf"); // Guardar el archivo PDF
}

// Verificar si se debe generar el PDF
$fecha_actual = date('Y-m-d');
$mes_actual = date('m');
$año_actual = date('Y');

// Consultar las ventas del mes
$consulta = $conexion->prepare("SELECT * FROM factura WHERE MONTH(fecha) = :mes AND YEAR(fecha) = :anio");
$consulta->bindParam(':mes', $mes_actual);
$consulta->bindParam(':anio', $año_actual);
$consulta->execute();
$ventas = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Preparar los datos para el PDF
foreach ($ventas as &$venta) {
    $venta['producto'] = ''; // Aquí debes obtener el nombre del producto según tu estructura de base de datos
    $venta['subtotal'] = $venta['total'] - ($venta['total'] * 0.19); // Ajusta según tu estructura
    // Puedes realizar un JOIN con la tabla de productos si es necesario
}

// Generar PDF si hay ventas
if (!empty($ventas)) {
    generarPDF($ventas, $mes_actual, $anio_actual);
    echo "<div class='alert alert-success'>PDF de ventas del mes generado correctamente.</div>";
} else {
    echo "<div class='alert alert-warning'>No hay ventas registradas para este mes.</div>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total del Mes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Total del Mes</h2>
        <a href="control_de_ventas.php" class="btn btn-secondary">Volver al Control de Ventas</a>
        <p>Asegúrate de que las ventas estén registradas para generar el PDF.</p>
    </div>
</body>
</html>
