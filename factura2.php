<?php
session_start();
require('conexion.php'); // Conexión a la base de datos
require('fpdf/fpdf.php'); // Biblioteca FPDF

// Inicializar variables
$cliente = isset($_SESSION['cliente']) ? $_SESSION['cliente'] : null;
$facturaId = isset($_SESSION['facturaId']) ? $_SESSION['facturaId'] : null;
$productos = isset($_SESSION['productos']) ? $_SESSION['productos'] : [];
$subtotal = 0;
$impuesto = 0;
$total = 0;

// Verificar si se ha enviado el formulario para buscar el cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar_cliente'])) {
    $cliente_id = filter_input(INPUT_POST, 'cliente_id', FILTER_SANITIZE_STRING);
    
    // Consultar el cliente en la tabla `usuarios`
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

    // Consultar el producto en la tabla `producto`
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
    
    // Recalcular subtotal, impuesto y total
    $subtotal = 0;
    foreach ($productos as $producto) {
        $subtotal += $producto['subtotal'];
    }
    $impuesto = $subtotal * 0.19;
    $total = $subtotal + $impuesto;

    $_SESSION['productos'] = $productos;
}

// Calcular subtotal y total para productos al iniciar
if (!empty($productos)) {
    $subtotal = array_sum(array_column($productos, 'subtotal'));
    $impuesto = $subtotal * 0.19;
    $total = $subtotal + $impuesto;
}

// Verificar si se ha enviado el formulario para anular la factura
if (isset($_POST['anular_factura'])) {
    unset($_SESSION['cliente']);
    unset($_SESSION['facturaId']);
    unset($_SESSION['productos']);

    $cliente = null;
    $facturaId = null;
    $productos = [];
    $subtotal = 0;
    $impuesto = 0;
    $total = 0;
}

// Generar PDF
if (isset($_GET['generar_factura'])) {
    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'Factura de Venta', 0, 1, 'C');
            $this->Ln(5);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
        }

        function FacturaBody($cliente, $productos, $total) {
            $this->SetFont('Arial', '', 12);
            $this->Cell(0, 10, "Fecha: " . date('Y-m-d H:i:s'), 0, 1); // Fecha y hora
            $this->Cell(0, 10, "Cliente: " . $cliente['unombre'], 0, 1);
            $this->Ln(5);

            $this->Cell(30, 10, 'ID', 1);
            $this->Cell(80, 10, 'Nombre', 1);
            $this->Cell(30, 10, 'IVA (19%)', 1);
            $this->Cell(30, 10, 'Subtotal', 1);
            $this->Cell(30, 10, 'Precio', 1);
            $this->Cell(30, 10, 'Acción', 1);
            $this->Ln();

            // Ordenar productos por nombre
            usort($productos, function($a, $b) {
                return strcmp($a['nombre'], $b['nombre']);
            });

            foreach ($productos as $producto) {
                $this->Cell(30, 10, $producto['id'], 1);
                $this->Cell(80, 10, $producto['nombre'], 1);
                $this->Cell(30, 10, number_format($producto['subtotal'] * 0.19, 2), 1); // IVA
                $this->Cell(30, 10, number_format($producto['subtotal'], 2), 1); // Subtotal
                $this->Cell(30, 10, number_format($producto['precio'], 2), 1); // Precio
                $this->Ln();
            }

            $this->Ln(10);
            $this->Cell(0, 10, "Total: $" . number_format($total, 2), 0, 1, 'R');
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->FacturaBody($cliente, $productos, $total);
    $pdf->Output();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creación de Factura</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('https://img.freepik.com/vector-gratis/fondo-borroso-colores-claros_1034-245.jpg');
            background-size: cover; 
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }
        body {
            margin: 20px;
        }
        .btn-volver-menu {
            position: fixed;
            top: 20px;
            right: 20px;
        }
        .btn-anular-factura {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Crear Factura</h2>

        <a href="consultar.php" class="btn btn-secondary btn-volver-menu">Volver al menú</a>

        <!-- Formulario para buscar cliente -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="cliente_id">ID de Cliente</label>
                <input type="text" name="cliente_id" class="form-control" id="cliente_id" required>
            </div>
            <button type="submit" name="buscar_cliente" class="btn btn-primary">Buscar Cliente</button>
        </form>

        <?php if ($cliente): ?>
            <h3>Factura ID: <?php echo $facturaId; ?></h3>
            <p><strong>Cliente:</strong> <?php echo $cliente['unombre']; ?></p>

            <!-- Formulario para agregar productos -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="producto_id">ID de Producto</label>
                    <input type="text" name="producto_id" class="form-control" id="producto_id" required>
                </div>
                <div class="form-group">
                    <label for="cantidad">Cantidad</label>
                    <input type="number" name="cantidad" class="form-control" id="cantidad" required>
                </div>
                <button type="submit" name="agregar_producto" class="btn btn-success">Agregar Producto</button>
            </form>

            <?php if (!empty($productos)): ?>
                <h4>Productos en la Factura</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?php echo $producto['id']; ?></td>
                                <td><?php echo $producto['nombre']; ?></td>
                                <td><?php echo $producto['cantidad']; ?></td>
                                <td><?php echo number_format($producto['precio'], 2); ?></td>
                                <td><?php echo number_format($producto['subtotal'], 2); ?></td>
                                <td>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                        <button type="submit" name="eliminar_producto" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <h4>Total: $<?php echo number_format($total, 2); ?></h4>

                <!-- Botón para generar factura PDF -->
                <button onclick="generarFactura()" class="btn btn-primary">Generar Factura PDF</button>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Botón para anular la factura -->
        <form method="POST" action="" class="btn-anular-factura">
            <button type="submit" name="anular_factura" class="btn btn-danger">Anular Factura</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function generarFactura() {
            // Abrir la URL de la factura en una nueva pestaña
            window.open('factura.php?generar_factura=1', '_blank');
        }
    </script>
</body>
</html>
