<?php
include 'conexion.php';

$exitoMensaje = $errorMensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['unombre'];
    $password = $_POST['upass'];
    $tipo = isset($_POST['utipo']) ? $_POST['utipo'] : 'user';

    $sqlVerificarUsuario = "SELECT unombre FROM usuarios WHERE unombre = :usuario";
    $stmtVerificarUsuario = $conexion->prepare($sqlVerificarUsuario);
    $stmtVerificarUsuario->bindParam(':usuario', $usuario, PDO::PARAM_STR);
    $stmtVerificarUsuario->execute();

    if ($stmtVerificarUsuario->rowCount() > 0) {
        $errorMensaje = "El nombre de usuario ya está en uso. Por favor, elija otro.";
    } else {
        $passwordHashed = password_hash($password, PASSWORD_BCRYPT);
        $sqlInsertarUsuario = "INSERT INTO usuarios (unombre, upass, utipo) VALUES (:usuario, :password, :tipo)";
        $stmtInsertarUsuario = $conexion->prepare($sqlInsertarUsuario);
        $stmtInsertarUsuario->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmtInsertarUsuario->bindParam(':password', $passwordHashed, PDO::PARAM_STR);
        $stmtInsertarUsuario->bindParam(':tipo', $tipo, PDO::PARAM_STR);

        if ($stmtInsertarUsuario->execute()) {
            $exitoMensaje = "Registro exitoso. Ahora puedes iniciar sesión.";
        } else {
            $errorMensaje = "Error al registrar el usuario. Por favor, inténtalo de nuevo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('https://img.freepik.com/vector-premium/estilo-fondo-futurista_23-2148503794.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            width: 100%;
            max-width: 400px;
            border: 2px solid red;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(255, 0, 0, 0.5);
            background: rgba(255, 255, 255, 0.85);
        }

        .card-header, .card-body {
            text-align: center;
        }

        .card-body form .btn-primary {
            width: 100%;
        }

        .card-body p {
            margin-top: 10px;
        }

        .card-body a {
            color: blue;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">Registro</div>
        <div class="card-body">
            <?php if ($exitoMensaje): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $exitoMensaje; ?>
                </div>
            <?php endif; ?>
            <?php if ($errorMensaje): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errorMensaje; ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label for="unombre">Nombre de Usuario:</label>
                    <input type="text" class="form-control" id="unombre" name="unombre" required>
                </div>
                <div class="form-group">
                    <label for="upass">Contraseña:</label>
                    <input type="password" class="form-control" id="upass" name="upass" required>
                </div>
                <button type="submit" class="btn btn-primary">Registrar</button>
                <p><a href="login.php">¿Ya tienes una cuenta? Inicia sesión aquí</a></p>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$conexion = null;
?>
