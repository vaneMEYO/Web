<?php
session_start();
require('conexion.php'); // Conexión a la base de datos
require('fpdf/fpdf.php'); // Biblioteca FPDF

// Inicializar variables
$cliente = isset($_SESSION['cliente']) ? $_SESSION['cliente'] : null;
$facturaId = isset($_SESSION['facturaId']) ? $_SESSION['facturaId'] : null;
$productos = isset($_SESSION['productos']) ? $_SESSION['productos'] : [];
$total = 0;

// Verificar si se ha enviado el formulario para buscar el cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar_cliente'])) {
    $cliente_id = filter_input(INPUT_POST, 'cliente_id', FILTER_SANITIZE_STRING);
    
    // Consultar el cliente en la tabla usuarios
    $consulta = $conexion->prepare("SELECT * FROM usuarios WHERE id = :id");
    $consulta->bindParam(':id', $cliente_id);
    $consulta->execute();

    if ($consulta->rowCount() > 0) {
        $cliente = $consulta->fetch(PDO::FETCH_ASSOC);
        $facturaId = uniqid('FAC-');
        $_SESSION['facturaId'] = $facturaId;
        $_SESSION['cliente'] = $cliente;
    } else {
        echo "<div class='alert alert-danger'>Cliente no encontrado. Debe crear un cliente.</div>";
    }
}

// Verificar si se ha enviado el formulario para agregar productos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_producto'])) {
    $producto_id = filter_input(INPUT_POST, 'producto_id', FILTER_SANITIZE_NUMBER_INT);
    $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_SANITIZE_NUMBER_INT);

    // Consultar el producto en la tabla producto
    $consulta_producto = $conexion->prepare("SELECT * FROM producto WHERE pro_id = :pro_id");
    $consulta_producto->bindParam(':pro_id', $producto_id);
    $consulta_producto->execute();

    if ($consulta_producto->rowCount() > 0) {
        $producto = $consulta_producto->fetch(PDO::FETCH_ASSOC);

        if ($producto['can_p'] >= $cantidad) {
            // Restar la cantidad del inventario
            $nueva_cantidad = $producto['can_p'] - $cantidad;
            $actualizar_cantidad = $conexion->prepare("UPDATE producto SET can_p = :nueva_cantidad WHERE pro_id = :pro_id");
            $actualizar_cantidad->bindParam(':nueva_cantidad', $nueva_cantidad);
            $actualizar_cantidad->bindParam(':pro_id', $producto_id);
            $actualizar_cantidad->execute();

            // Agregar producto a la factura
            $subtotal_producto = $producto['pre_p'] * $cantidad;
            $productos[] = [
                'id' => $producto['pro_id'],
                'nombre' => $producto['nom_p'],
                'precio' => $producto['pre_p'],
                'cantidad' => $cantidad,
                'subtotal' => $subtotal_producto
            ];

            $_SESSION['productos'] = $productos;
        } else {
            echo "<div class='alert alert-danger'>Cantidad insuficiente en inventario.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Producto no encontrado.</div>";
    }
}

// Verificar si se ha enviado el formulario para eliminar un producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_producto'])) {
    $producto_id = filter_input(INPUT_POST, 'producto_id', FILTER_SANITIZE_NUMBER_INT);
    // Filtrar el producto eliminado
    $productos = array_filter($productos, function($producto) use ($producto_id) {
        return $producto['id'] != $producto_id;
    });

    $_SESSION['productos'] = $productos;
}

// Calcular total para productos al iniciar
if (!empty($productos)) {
    $subtotal = array_sum(array_column($productos, 'subtotal'));
    $impuesto = $subtotal * 0.19;
    $total = $subtotal + $impuesto;
}

// Verificar si se ha enviado el formulario para anular la factura
if (isset($_POST['anular_factura'])) {
    // Devolver la cantidad de productos al inventario
    foreach ($productos as $producto) {
        $producto_id = $producto['id'];
        $cantidad = $producto['cantidad'];

        // Consultar el producto en la base de datos
        $consulta_producto = $conexion->prepare("SELECT can_p FROM producto WHERE pro_id = :pro_id");
        $consulta_producto->bindParam(':pro_id', $producto_id);
        $consulta_producto->execute();
        $producto_db = $consulta_producto->fetch(PDO::FETCH_ASSOC);

        // Actualizar la cantidad en la tabla producto
        $nueva_cantidad = $producto_db['can_p'] + $cantidad;
        $actualizar_cantidad = $conexion->prepare("UPDATE producto SET can_p = :nueva_cantidad WHERE pro_id = :pro_id");
        $actualizar_cantidad->bindParam(':nueva_cantidad', $nueva_cantidad);
        $actualizar_cantidad->bindParam(':pro_id', $producto_id);
        $actualizar_cantidad->execute();
    }

    // Limpiar la sesión
    unset($_SESSION['cliente']);
    unset($_SESSION['facturaId']);
    unset($_SESSION['productos']);

    // Redirigir al menú
    header("Location: consultar.php?anulacion=true");
    exit;
}

// Generar PDF
if (isset($_POST['generar_factura'])) {
    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'Factura de Venta', 0, 1, 'C');
            $this->Ln(5);
            $this->Cell(0, 10, 'Tienda: Meyorki', 0, 1, 'C'); // Nombre de la tienda
            $this->Ln(5);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
        }

        function FacturaBody($cliente, $productos, $total, $impuesto) {
            $this->SetFont('Arial', '', 12);
            $this->Cell(0, 10, "Fecha: " . date('Y-m-d H:i:s'), 0, 1); // Fecha y hora
            $this->Cell(0, 10, "Cliente: " . $cliente['unombre'], 0, 1);
            $this->Ln(5);

            $this->Cell(30, 10, 'ID', 1);
            $this->Cell(80, 10, 'Nombre', 1);
            $this->Cell(30, 10, 'Precio', 1);
            $this->Cell(30, 10, 'Cantidad', 1);
            $this->Ln();

            // Ordenar productos por nombre
            usort($productos, function($a, $b) {
                return strcmp($a['nombre'], $b['nombre']);
            });

            foreach ($productos as $producto) {
                $this->Cell(30, 10, $producto['id'], 1);
                $this->Cell(80, 10, $producto['nombre'], 1);
                $this->Cell(30, 10, number_format($producto['precio'], 2), 1); // Precio
                $this->Cell(30, 10, $producto['cantidad'], 1); // Cantidad
                $this->Ln();
            }

            $this->Ln(10);
            $this->Cell(0, 10, "IVA Total (19%): $" . number_format($impuesto, 2), 0, 1, 'R');
            $this->Cell(0, 10, "Total: $" . number_format($total, 2), 0, 1, 'R');
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->FacturaBody($cliente, $productos, $total, $impuesto);
    
    // Guardar el PDF en el servidor en la carpeta indicada
    $fecha_actual = date('Y-m-d_H-i-s'); // Formato de fecha y hora
    $mes_actual = date('m');
    $ruta_carpeta = "ventas/2024/$mes_actual/";
    
    // Crear la carpeta si no existe
    if (!is_dir($ruta_carpeta)) {
        mkdir($ruta_carpeta, 0777, true);
    }
    
    $pdf_file_name = $ruta_carpeta . "factura_$fecha_actual.pdf";
    $pdf->Output('F', $pdf_file_name); // Guarda el PDF en el servidor

    // Redirigir para abrir el PDF en una ventana emergente
    echo "<script>window.open('$pdf_file_name', '_blank');</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Registro de Ventas - Meyorki</title>
    <style>
        .container {
            margin-top: 50px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center">Registro de Ventas</h2>
    <form method="post" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label for="cliente_id" class="mr-2">ID Cliente:</label>
            <input type="text" name="cliente_id" class="form-control" required>
        </div>
        <button type="submit" name="buscar_cliente" class="btn btn-primary">Buscar Cliente</button>
    </form>

    <?php if ($cliente): ?>
        <h4>Cliente: <?= $cliente['unombre']; ?></h4>
        <h5>Factura ID: <?= $facturaId; ?></h5>

        <form method="post" class="form-inline mb-3">
            <div class="form-group mr-2">
                <label for="producto_id" class="mr-2">ID Producto:</label>
                <input type="text" name="producto_id" class="form-control" required>
            </div>
            <div class="form-group mr-2">
                <label for="cantidad" class="mr-2">Cantidad:</label>
                <input type="number" name="cantidad" class="form-control" required min="1">
            </div>
            <button type="submit" name="agregar_producto" class="btn btn-success">Agregar Producto</button>
        </form>

        <?php if (!empty($productos)): ?>
            <h4>Productos Agregados</h4>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= $producto['id']; ?></td>
                        <td><?= $producto['nombre']; ?></td>
                        <td>$<?= number_format($producto['precio'], 2); ?></td>
                        <td><?= $producto['cantidad']; ?></td>
                        <td>$<?= number_format($producto['subtotal'], 2); ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="producto_id" value="<?= $producto['id']; ?>">
                                <button type="submit" name="eliminar_producto" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <h4>Total: $<?= number_format($total, 2); ?></h4>
            <h4>IVA Total (19%): $<?= number_format($impuesto, 2); ?></h4>
            <form method="post">
                <button type="submit" name="generar_factura" class="btn btn-primary">Generar Factura</button>
                <button type="submit" name="anular_factura" class="btn btn-danger">Anular Factura</button>
            </form>
        <?php else: ?>
            <h4>No hay productos agregados.</h4>
        <?php endif; ?>
    <?php endif; ?>
    <a href="consultar.php" class="btn btn-secondary mt-3">Volver al Menú</a>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
        function generarFactura() {
            // Abrir la URL de la factura en una nueva pestaña
            window.open('factura.php?generar_factura=1', '_blank');
        }
    </script>
</body>
</html>
