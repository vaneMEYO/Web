<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo de Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
         body {
            background-image: url('https://img.freepik.com/vector-gratis/fondo-borroso-colores-claros_1034-245.jpg');
            background-size: cover; 
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }
        /* Estilos básicos para el menú */
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; /* Centramos el contenido */
            background-color: #f8f9fa; /* Fondo claro para mejorar el contraste */
        }
        h1 {
            margin: 20px 0;
            color: #007bff; /* Color azul para el encabezado */
        }
        .menu { 
            margin: 20px; 
        }
        .menu a { 
            text-decoration: none; 
            color: #000; 
            padding: 15px 25px; 
            margin-right: 10px; 
            border: 1px solid #ddd; 
            background-color: #f4f4f4; 
            border-radius: 5px; 
            font-size: 1.2em; /* Tamaño mediano */
        }
        .menu a:hover { 
            background-color: #ddd; 
        }
        .btn-success { 
            margin-top: 20px; 
            font-size: 1.2em; /* Tamaño mediano */
        }
    </style>
</head>
<body>
    <h1>BIENVENIDO A LA GESTIÓN DE CLIENTES</h1>
    <div class="menu">
        <a href="formulario_cliente.php">Agregar Cliente</a>
        <a href="lista_cliente.php">Lista de Clientes</a>
    </div>
    <!-- Botón para regresar al inicio -->
    <div class="mt-3">
        <a href="inicio.php" class="btn btn-success">Regresar al inicio</a>
    </div>

</body>
</html>
