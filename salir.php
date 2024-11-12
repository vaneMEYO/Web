//<?php
// Iniciar o reanudar la sesión
session_start();

// Destruir la sesión
session_destroy();

// Redireccionar a index.php
header('Location: index.php');
exit();
?>//
<body>
    <form action="salir.php" method="post">
        <button type="submit" class="btn btn-danger">Salir</button>
    </form>
</body>