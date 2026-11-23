<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id_producto'] ?? 0);
    $codigo = trim($_POST['codigo'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $categoria_id = intval($_POST['categoria_id'] ?? 0);
    $precio = floatval($_POST['precio'] ?? 0);
    $stock_actual = intval($_POST['stock_actual'] ?? 0);
    $stock_minimo = intval($_POST['stock_minimo'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($id > 0 && $codigo !== '' && $nombre !== '') {
        try {
            $sql = "UPDATE productos SET codigo = :codigo, nombre = :nombre, descripcion = :descripcion,
                    precio = :precio, stock_actual = :stock_actual, stock_minimo = :stock_minimo, id_categoria = :categoria_id
                    WHERE id_producto = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':codigo' => $codigo,
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':precio' => $precio,
                ':stock_actual' => $stock_actual,
                ':stock_minimo' => $stock_minimo,
                ':categoria_id' => $categoria_id,
                ':id' => $id
            ]);

            $sql_mov = "INSERT INTO movimientos (fecha_hora, tipo, cantidad, observacion, id_producto, id_usuario) 
                        VALUES (NOW(), 'Ajuste', :cantidad, 'Actualización de stock desde edición', :id_producto, :id_usuario)";
            $stmt_mov = $pdo->prepare($sql_mov);
            $stmt_mov->execute([
                ':cantidad' => $stock_actual,
                ':id_producto' => $id,
                ':id_usuario' => $_SESSION['usuario_id'] ?? 1
            ]);

            echo "Producto actualizado con éxito! <a href='listar_productos.php'>Volver al inventario</a>";
        } catch (PDOException $e) {
            echo "Error al actualizar el producto: " . $e->getMessage();
        }
    } else {
        echo "Por favor complete los campos obligatorios.";
    }
} else {
    header('Location: listar_productos.php');
    exit();
}
?>