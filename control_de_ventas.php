<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Ventas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: black;
            color: white;
        }

        .container-options {
            display: flex;
            justify-content: space-between;
            width: 50%;
            max-width: 600px;
        }

        .option-button {
            flex: 1;
            text-align: center;
            padding: 20px;
            margin: 0 10px;
            background-color: black;
            color: white;
            font-size: 1.5em;
            border: 2px solid #ff0000;
            border-radius: 8px;
            transition: box-shadow 0.3s ease;
        }

        .option-button:hover {
            box-shadow: 0 0 10px 5px rgba(255, 0, 0, 0.8);
            text-decoration: none;
            color: white;
        }

        .btn-volver-menu {
            position: fixed;
            top: 20px;
            left: 20px;
            font-size: 1.2em;
            color: white;
            background-color: black;
            border: 2px solid #ff0000;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
        }

        .btn-volver-menu:hover {
            box-shadow: 0 0 10px 5px rgba(255, 0, 0, 0.8);
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Botón de Volver al Menú -->
    <a href="consultar.php" class="btn-volver-menu">Volver al Menú</a>

    <!-- Contenedor de opciones -->
    <div class="container-options">
        <a href="venta_diaria.php" class="option-button">Venta Diaria</a>
        <a href="total_del_mes.php" class="option-button">Total del Mes</a>
    </div>

</body>
</html>
