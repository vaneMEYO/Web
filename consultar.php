<?php
session_start();
if (isset($_SESSION["usuario"])) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEYORKI - Lista de Productos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="estilos/style.css">
    <style>
        body {
            background-image: url('https://img.freepik.com/vector-gratis/fondo-borroso-colores-claros_1034-245.jpg');
            background-size: cover; 
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        .table {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #007bff;
            color: #ffffff;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
        }
        h1 {
            color: #007bff;
            font-family: 'Georgia', serif;
            font-size: 2em;
            margin: 0;
        }
        .btn-nuevo-producto {
            background-color: #6f42c1;
            color: #ffffff;
            border: none;
        }
        .btn-nuevo-producto:hover {
            background-color: #5a379d;
        }
    </style>
</head>
<body>
<?php include("menu.php"); ?>

<div class="container mt-4">
    <div class="header-container">
        <h1>Lista de Productos</h1>
        <a href="crear.php" class="btn btn-nuevo-producto">Nuevo Producto</a>
    </div>
    
    <div class="table-responsive">
        <table class="table caption-top">
            <thead>
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Proveedor</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Descripción</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once('conexion.php');
                $SQL = 'SELECT pro_id, pre_p, can_p, pro_p, nom_p, des_p, tel_p FROM producto';
                $stmt = $conexion->prepare($SQL);
                $stmt->execute();
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                foreach ($rows as $row) {
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['pro_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['pre_p']); ?></td>
                        <td><?php echo htmlspecialchars($row['can_p']); ?></td>
                        <td><?php echo htmlspecialchars($row['pro_p']); ?></td>
                        <td><?php echo htmlspecialchars($row['nom_p']); ?></td>
                        <td><?php echo htmlspecialchars($row['des_p']); ?></td>
                        <td><?php echo htmlspecialchars($row['tel_p']); ?></td>
                        <td>
                            <button class="btn btn-danger" onclick="Eliminar(<?php echo htmlspecialchars($row['pro_id']); ?>)">Eliminar</button>
                            <button class="btn btn-warning" onclick="openUpdateModal(<?php echo htmlspecialchars($row['pro_id']); ?>, '<?php echo htmlspecialchars($row['nom_p']); ?>', '<?php echo htmlspecialchars($row['pre_p']); ?>', '<?php echo htmlspecialchars($row['can_p']); ?>', '<?php echo htmlspecialchars($row['pro_p']); ?>', '<?php echo htmlspecialchars($row['tel_p']); ?>', '<?php echo htmlspecialchars($row['des_p']); ?>')">Actualizar</button>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para actualizar producto -->
<div class="modal fade" id="updateProductModal" tabindex="-1" role="dialog" aria-labelledby="updateProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateProductModalLabel">Actualizar Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateProductForm">
                    <input type="hidden" id="update_pro_id" name="pro_id">
                    <div class="form-group">
                        <label for="update_nom_p">Nombre del Producto</label>
                        <input type="text" class="form-control" id="update_nom_p" name="nom_p" required>
                    </div>
                    <div class="form-group">
                        <label for="update_pre_p">Precio</label>
                        <input type="text" class="form-control" id="update_pre_p" name="pre_p" required>
                    </div>
                    <div class="form-group">
                        <label for="update_can_p">Cantidad</label>
                        <input type="text" class="form-control" id="update_can_p" name="can_p" required>
                    </div>
                    <div class="form-group">
                        <label for="update_pro_p">Proveedor</label>
                        <input type="text" class="form-control" id="update_pro_p" name="pro_p" required>
                    </div>
                    <div class="form-group">
                        <label for="update_tel_p">Teléfono del Proveedor</label>
                        <input type="text" class="form-control" id="update_tel_p" name="tel_p" required>
                    </div>
                    <div class="form-group">
                        <label for="update_des_p">Descripción</label>
                        <textarea class="form-control" id="update_des_p" name="des_p"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="updateProduct()">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function Eliminar(productId) {
    if (confirm("¿Estás seguro de que deseas eliminar este producto?")) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'eliminar.php';
        
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id_producto';
        input.value = productId;
        form.appendChild(input);

        document.body.appendChild(form);
        form.submit();
    }
}

function openUpdateModal(id, nombre, precio, cantidad, proveedor, telefono, descripcion) {
    document.getElementById('update_pro_id').value = id;
    document.getElementById('update_nom_p').value = nombre;
    document.getElementById('update_pre_p').value = precio;
    document.getElementById('update_can_p').value = cantidad;
    document.getElementById('update_pro_p').value = proveedor;
    document.getElementById('update_tel_p').value = telefono;
    document.getElementById('update_des_p').value = descripcion;
    $('#updateProductModal').modal('show');
}

function updateProduct() {
    var formData = $('#updateProductForm').serialize();
    $.ajax({
        url: 'actualizar_producto.php',
        type: 'POST',
        data: formData,
        success: function(response) {
            $('#updateProductModal').modal('hide');
            location.reload(); 
        },
        error: function() {
            alert('Ocurrió un error al actualizar el producto.');
        }
    });
}
</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
} else {
    header("location:index.php");
}
?>
