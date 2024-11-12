<?php
session_start();
if(isset($_SESSION["usuario"])){
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>INICIO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
      body {
        background-image: url('https://panafargo.com/wp-content/uploads/2023/11/que-es-una-miscelanea-1-1024x648.jpeg');
        background-size: cover; 
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .container {
        display: flex; /* Usa flexbox para alinear elementos */
        justify-content: space-between; /* Espacio entre los elementos */
        align-items: center; /* Centra los elementos verticalmente */
        color: rgba(255, 255, 255, 0.9); /* Ajusta el color para mejor visibilidad */
        font-family: 'Georgia', serif; /* Elegante */
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        width: 90%; /* Ajusta el ancho del contenedor */
      }

      .header {
        flex: 1; /* Permite que la sección del texto use el espacio disponible */
        text-align: left; /* Alinea el texto a la izquierda */
      }

      h1 {
        font-size: 4em; /* Tamaño de fuente del título */
        margin-bottom: 0.5em;
      }

      .description {
        font-size: 1.5em; /* Tamaño de fuente más pequeño para la descripción */
        max-width: 300px;
      }

      .menu-buttons {
        display: flex;
        flex-direction: column; /* Coloca los botones en columna */
        gap: 1em; /* Espaciado entre los botones */
      }

      .menu-button {
        background-color: rgba(255, 255, 255, 0.9);
        border: 1px solid #ddd; /* Borde sutil */
        border-radius: 10px;
        padding: 10px; /* Espaciado interno */
        width: 120px; /* Ancho de la tarjeta */
        height: 70px; /* Alto de la tarjeta */
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2); /* Sombra para dar profundidad */
        color: #333; /* Color del texto */
        text-decoration: none; /* Sin subrayado */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }

      .menu-button:hover {
        transform: translateY(-5px); /* Efecto de elevación */
        box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.3);
      }
    </style> 
  </head>
  <body>
    <div class="container">
      <div class="header">
        <h1> MEYORKI </h1>
        <p class="description"> Bienvenido a nuestro sistema está diseñado para brindarte una experiencia rápida y organizada, facilitando la búsqueda de todo lo que necesitas en un solo lugar.</p>
      </div>
      <div class="menu-buttons">
        <?php include("menu.php"); ?>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.5.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/v/VC4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
  </body>
</html>
<?php
}else{
    header("location:login.php");
}
?>
