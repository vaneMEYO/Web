<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el nombre de usuario y las contraseñas
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
    $nueva_contrasena = filter_input(INPUT_POST, 'nueva_contrasena', FILTER_SANITIZE_STRING);
    $confirmar_contrasena = filter_input(INPUT_POST, 'confirmar_contrasena', FILTER_SANITIZE_STRING);

    // Verificar si las contraseñas coinciden
    if ($nueva_contrasena === $confirmar_contrasena) {
        // Verificar si el nombre de usuario existe en la base de datos
        $consulta = $conexion->prepare("SELECT id, unombre FROM usuarios WHERE unombre = :usuario");
        $consulta->bindParam(':usuario', $usuario);
        $consulta->execute();

        if ($consulta->rowCount() > 0) {
            // Encriptar la nueva contraseña
            $nueva_contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

            // Actualizar la contraseña en la base de datos
            $sqlUpdate = $conexion->prepare("UPDATE usuarios SET upass = :nueva_contrasena WHERE unombre = :usuario");
            $sqlUpdate->bindParam(':nueva_contrasena', $nueva_contrasena_hash);
            $sqlUpdate->bindParam(':usuario', $usuario);
            $sqlUpdate->execute();

            // Redirigir automáticamente al inicio de sesión después de cambiar la contraseña
            header('Location: login.php');
            exit();
        } else {
            $mensaje = "No se encontró una cuenta con ese nombre de usuario.";
        }
    } else {
        $mensaje = "Las contraseñas no coinciden. Por favor, intenta nuevamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cambiar contraseña</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('https://img.freepik.com/vector-premium/estilo-fondo-futurista_23-2148503794.jpg'); /* Cambia esto por la ruta de tu imagen */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .card {
            border: 2px solid #87CEEB; /* Sombreado azul cielo */
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(135, 206, 235, 0.7); /* Sombra azul cielo */
            background: rgba(255, 255, 255, 0.85);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Cambiar contraseña</div>
                    <div class="card-body">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-info">
                                <?php echo $mensaje; ?>
                            </div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="form-group">
                                <label for="usuario">Nombre de usuario</label>
                                <input type="text" class="form-control" id="usuario" name="usuario" required>
                            </div>
                            <div class="form-group">
                                <label for="nueva_contrasena">Nueva contraseña</label>
                                <input type="password" class="form-control" id="nueva_contrasena" name="nueva_contrasena" required>
                            </div>
                            <div class="form-group">
                                <label for="confirmar_contrasena">Confirmar nueva contraseña</label>
                                <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
                                <a href="login.php" class="btn btn-warning">Volver a Inicio</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conexion = null;
?>
