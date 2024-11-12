<?php
// incluir el archivo de conexión
require 'conexion.php';

// Obtener la lista de clientes
$sql = "SELECT * FROM clientes";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Clientes</title>
    <!-- Agregar Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Estilo para el fondo de la página */
        body {
            background-image: url('https://img.freepik.com/vector-gratis/fondo-borroso-colores-claros_1034-245.jpg');
            background-size: cover; 
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }
        body {
            background-color: #f8f9fa; /* Fondo claro */
            font-family: Arial, sans-serif;
        }
        h1 {
            color: #007bff; /* Color azul para el encabezado */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Lista de Clientes</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?php echo $cliente['id']; ?></td>
                    <td><?php echo $cliente['nombre']; ?></td>
                    <td><?php echo $cliente['email']; ?></td>
                    <td><?php echo $cliente['telefono']; ?></td>
                    <td><?php echo $cliente['direccion']; ?></td>
                    <td>
                        <!-- Botón de actualizar -->
                        <button class="btn btn-warning" data-toggle="modal" data-target="#updateModal<?php echo $cliente['id']; ?>">
                            Actualizar
                        </button>
                        <!-- Botón de eliminar -->
                        <button class="btn btn-danger" onclick="confirmDelete(<?php echo $cliente['id']; ?>)">
                            Eliminar
                        </button>
                    </td>
                </tr>

                <!-- Modal para actualizar -->
                <div class="modal fade" id="updateModal<?php echo $cliente['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updateModalLabel">Actualizar Cliente</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="actualizar_cliente.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
                                    <div class="form-group">
                                        <label for="nombre">Nombre:</label>
                                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo $cliente['nombre']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email:</label>
                                        <input type="email" id="email" name="email" class="form-control" value="<?php echo $cliente['email']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="telefono">Teléfono:</label>
                                        <input type="text" id="telefono" name="telefono" class="form-control" value="<?php echo $cliente['telefono']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="direccion">Dirección:</label>
                                        <textarea id="direccion" name="direccion" class="form-control"><?php echo $cliente['direccion']; ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Botón para regresar al inicio -->
        <div class="mt-3">
            <a href="cliente.php" class="btn btn-success">Regresar al inicio</a>
        </div>
    </div>

    <!-- Confirmación de eliminación -->
    <script>
        function confirmDelete(id) {
            if (confirm("¿Estás seguro de que deseas eliminar este cliente?")) {
                window.location.href = 'eliminar_cliente.php?id=' + id;
            }
        }
    </script>

    <!-- Agregar Bootstrap JS y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
