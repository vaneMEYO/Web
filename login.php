<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
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
        <div class="card-header">Login</div>
        <div class="card-body">
            <?php
            session_start();
            require('conexion.php');

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $us = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
                $pas = filter_input(INPUT_POST, 'contrasena', FILTER_SANITIZE_STRING);

                $consulta = $conexion->prepare("SELECT id, unombre, upass, utipo FROM usuarios WHERE unombre = :unombre");
                $consulta->bindParam(':unombre', $us);
                $consulta->execute();

                if ($consulta->rowCount() > 0) {
                    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);
                    if (password_verify($pas, $usuario['upass'])) {
                        $_SESSION['usuario'] = $usuario;
                        header('Location: inicio.php');
                        exit();
                    } else {
                        echo '<div class="alert alert-danger" role="alert">¡Contraseña incorrecta!</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger" role="alert">¡Usuario no encontrado!</div>';
                }
            }
            ?>
            <form method="post">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" required>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                </div>
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                <p><a href="olvide_password.php">¿Olvidaste tu contraseña?</a> | <a href="registrarse.php">Registrar</a></p>
            </form>
        </div>
    </div>
</body>
</html>
