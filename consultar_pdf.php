<?php
// Directorio donde se guardan los PDFs
$directorio = 'ventas/2024/10/'; // Asegúrate de que esta ruta sea correcta y de que los PDFs se guarden aquí

// Obtener todos los archivos PDF en el directorio
$archivos = [];
if (is_dir($directorio)) {
    $files = scandir($directorio);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
            $archivos[] = $file;
        }
    }
}

// Contar total de ventas
$total_ventas = count($archivos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar PDFs</title>
    <link rel="stylesheet" href="path/to/bootstrap.css"> <!-- Asegúrate de que la ruta sea correcta -->
    <style>
        body {
            background-color: #e9ecef; /* Color de fondo suave (gris claro) */
        }
        .container {
            background-color: #ffffff; /* Fondo blanco para el contenido */
            border-radius: 10px; /* Bordes redondeados más pronunciados */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Sombra más fuerte */
            padding: 30px; /* Espaciado interno */
        }
        h1 {
            margin-bottom: 30px; /* Espaciado inferior */
            color: #343a40; /* Color del título (gris oscuro) */
        }
        .table {
            margin-top: 20px; /* Espaciado superior */
        }
        .table th {
            background-color: #007bff; /* Color de fondo para el encabezado (azul) */
            color: white; /* Color del texto del encabezado */
        }
        .btn-primary {
            background-color: #28a745; /* Color primario (verde) */
            border: none; /* Sin borde */
        }
        .btn-primary:hover {
            background-color: #218838; /* Color primario al pasar el ratón */
        }
        .btn-secondary {
            background-color: #6c757d; /* Color secundario (gris) */
            border: none; /* Sin borde */
        }
        .btn-secondary:hover {
            background-color: #5a6268; /* Color secundario al pasar el ratón */
        }
        .alert {
            margin-top: 20px; /* Espaciado superior para la alerta */
        }
        .total-ventas {
            position: fixed; /* Posición fija para que siempre esté visible */
            bottom: 20px; /* Espaciado desde la parte inferior */
            left: 20px; /* Espaciado desde la izquierda */
            background-color: #ffffff; /* Fondo blanco */
            border: 1px solid #007bff; /* Borde azul */
            border-radius: 5px; /* Bordes redondeados */
            padding: 10px; /* Espaciado interno */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Sombra */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Consultar ventas Generados</h1>
    
    <?php if (empty($archivos)): ?>
        <div class="alert alert-warning text-center">No hay ventas generados.</div>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>PDF</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($archivos as $archivo): ?>
                    <tr>
                        <td><?php echo date('Y-m-d'); ?></td> <!-- Fecha actual -->
                        <td>
                            <a href="<?php echo $directorio . $archivo; ?>" class="btn btn-primary" target="_blank">Descargar <?php echo $archivo; ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <div class="text-center mt-4">
        <a href="consultar.php" class="btn btn-secondary">Volver al Menú</a>
    </div>
</div>

<!-- Mostrar total de ventas en la esquina inferior izquierda -->
<div class="total-ventas">
</div>
</body>
</html>