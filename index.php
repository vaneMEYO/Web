<?php
require_once('conexion.php');

// Si el formulario de creación ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    // Inserción del nuevo producto
    $sql = "INSERT INTO productos (nombre, precio) VALUES (?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('sd', $nombre, $precio);
    $stmt->execute();
}

// Si el formulario de eliminación ha sido enviado
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar' && isset($_GET['id_producto'])) {
    $id_producto = intval($_GET['id_producto']);
    $sql = "DELETE FROM productos WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $id_producto);
    $stmt->execute();
}

// Obtener todos los productos de la base de datos
$sql = "SELECT * FROM productos";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Productos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h1 class="my-4">Listado de Productos</h1>

    <!-- Formulario para crear un nuevo producto -->
    <form action="index.php" method="post">
        <input type="hidden" name="accion" value="crear">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Producto</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio</label>
            <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-primary">Crear Producto</button>
    </form>

    <!-- Listado de productos -->
    <table class="table mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($producto = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $producto['id']; ?></td>
                    <td><?php echo $producto['nombre']; ?></td>
                    <td><?php echo $producto['precio']; ?></td>
                    <td>
                        <!-- Botón para abrir el modal de edición -->
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modal<?php echo $producto['id']; ?>">
                            Editar
                        </button>

                        <!-- Enlace para eliminar -->
                        <a href="index.php?accion=eliminar&id_producto=<?php echo $producto['id']; ?>" class="btn btn-danger">Eliminar</a>

                        <!-- Modal para editar -->
                        <div class="modal fade" id="modal<?php echo $producto['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Actualizar Producto</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <form action="actualizar.php" method="post">
                                    <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" name="nombre" value="<?php echo $producto['nombre']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="precio" class="form-label">Precio</label>
                                        <input type="number" class="form-control" name="precio" value="<?php echo $producto['precio']; ?>" step="0.01" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                </form>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                              </div>
                            </div>
                          </div>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
