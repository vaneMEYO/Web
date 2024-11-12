<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $nuevaPassword = $_POST['password'];

    // Buscar el usuario con el token
    $consulta = $conexion->prepare("SELECT id FROM usuarios WHERE reset_token = :token AND token_expiration > NOW()");
    $consulta->bindParam(':token', $token);
    $consulta->execute();

    if ($consulta->rowCount() > 0) {
        // Hashear la nueva contraseña
        $passwordHashed = password_hash($nuevaPassword, PASSWORD_BCRYPT);

        // Actualizar la contraseña en la base de datos
        $actualizar = $conexion->prepare("UPDATE usuarios SET upass = :password, reset_token = NULL, token_expiration = NULL WHERE reset_token = :token");
        $actualizar->bindParam(':password', $passwordHashed);
        $actualizar->bindParam(':token', $token);
        $actualizar->execute();

        $mensaje = "Tu contraseña ha sido actualizada exitosamente.";
    } else {
        $mensaje = "El enlace de restablecimiento es inválido o ha expirado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablecer contraseña</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Restablecer contraseña</div>
                    <div class="card-body">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-info">
                                <?php echo $mensaje; ?>
                            </div>
                        <?php endif; ?>
                        <form method="post">
                            <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
                            <div class="form-group">
                                <label for="password">Nueva contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Restablecer contraseña</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
