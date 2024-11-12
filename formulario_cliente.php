<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Cliente</title>
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
        .button-group {
            display: flex; /* Para alinear los botones en línea */
            justify-content: space-between; /* Espacio entre botones */
            margin-top: 20px; /* Separar los botones del formulario */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Agregar Cliente</h1>
        <form action="formulario_cliente.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" class="form-control">
            </div>
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <textarea id="direccion" name="direccion" class="form-control"></textarea>
            </div>
            <div class="button-group">
                <button type="submit" class="btn btn-primary">Agregar Cliente</button>
                <!-- Botón para regresar al inicio -->
                <a href="cliente.php" class="btn btn-success">Regresar al inicio</a>
            </div>
        </form>

        <?php
        // incluir el archivo de conexión
        require 'conexion.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Obtener datos del formulario
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];

            try {
                // Preparar la consulta SQL
                $sql = "INSERT INTO clientes (nombre, email, telefono, direccion) VALUES (:nombre, :email, :telefono, :direccion)";
                $stmt = $conexion->prepare($sql);
                
                // Vincular los parámetros
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':direccion', $direccion);
                
                // Ejecutar la consulta
                $stmt->execute();

                echo "<div class='container mt-3 alert alert-success'>Cliente agregado exitosamente.</div>";
            } catch (PDOException $e) {
                echo "<div class='container mt-3 alert alert-danger'>Error: " . $e->getMessage() . "</div>";
            }
        }
        ?>
    </div>
    <!-- Agregar Bootstrap JS y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
