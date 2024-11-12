<?php
session_start();
if (isset($_SESSION["usuario"])) {
    // Archivo de conexión
    require('conexion.php');

    // Verificar si se ha enviado el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recuperar los datos del formulario y desinfectar
        $nom_p = filter_input(INPUT_POST, 'nom_p', FILTER_SANITIZE_STRING);
        $des_p = filter_input(INPUT_POST, 'des_p', FILTER_SANITIZE_STRING);
        $can_p = filter_input(INPUT_POST, 'can_p', FILTER_SANITIZE_NUMBER_INT);
        $pro_p = filter_input(INPUT_POST, 'pro_p', FILTER_SANITIZE_STRING);
        $tel_p = filter_input(INPUT_POST, 'tel_p', FILTER_SANITIZE_STRING);
        $pre_p = filter_input(INPUT_POST, 'pre_p', FILTER_SANITIZE_STRING);

        // Consulta para insertar el producto
        $consulta = $conexion->prepare(
            "INSERT INTO producto (nom_p, des_p, can_p, pro_p, tel_p, pre_p) VALUES (:nom_p, :des_p, :can_p, :pro_p, :tel_p, :pre_p)"
        );

        $consulta->bindParam(':nom_p', $nom_p);
        $consulta->bindParam(':des_p', $des_p);
        $consulta->bindParam(':can_p', $can_p);
        $consulta->bindParam(':pro_p', $pro_p);
        $consulta->bindParam(':tel_p', $tel_p);
        $consulta->bindParam(':pre_p', $pre_p);

        if ($consulta->execute()) {
            // Redirigir a consultar.php después de agregar el producto
            header('Location: consultar.php');
            exit(); // Asegurarse de que el script se detiene después de redirigir
        } else {
            // Manejar errores
            $error = 'Error al agregar el producto.';
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('https://img.freepik.com/vector-gratis/fondo-borroso-colores-claros_1034-245.jpg');
            background-size: cover; 
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9); /* Fondo blanco con transparencia */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: #ffffff;
            border-bottom: none;
            border-radius: 10px 10px 0 0;
            padding: 1rem;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
</head>
<body>
    <?php include("menu.php"); ?> 

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Agregar Producto</div>
                    <div class="card-body">
                        <form method="POST" action="crear.php">
                            <div class="form-group">
                                <label for="nom_p">Nombre:</label>
                                <input type="text" id="nom_p" name="nom_p" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="des_p">Descripción:</label>
                                <input type="text" id="des_p" name="des_p" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="can_p">Cantidad:</label>
                                <input type="number" id="can_p" name="can_p" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="pro_p">Proveedor:</label>
                                <input type="text" id="pro_p" name="pro_p" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="tel_p">Teléfono:</label>
                                <input type="text" id="tel_p" name="tel_p" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="pre_p">Precio:</label>
                                <input type="text" id="pre_p" name="pre_p" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Guardar</button>
                        </form>

                        <!-- Mensaje de error -->
                        <?php if (isset($error)): ?>
                            <div id="error-message" class="alert alert-danger mt-3" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts de Bootstrap y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
} else {
    header("location:index.php");
}
?>
