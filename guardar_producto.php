<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $categoria_id = intval($_POST['categoria_id'] ?? 0);
    $precio = floatval($_POST['precio'] ?? 0);
    $stock_actual = intval($_POST['stock_actual'] ?? 0);
    $stock_minimo = intval($_POST['stock_minimo'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($codigo !== '' && $nombre !== '' && $categoria_id > 0 && $precio >= 0) {
        try {
            $sql = "INSERT INTO productos (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, id_categoria) 
                    VALUES (:codigo, :nombre, :descripcion, :precio, :stock_actual, :stock_minimo, :categoria_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':codigo' => $codigo,
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':precio' => $precio,
                ':stock_actual' => $stock_actual,
                ':stock_minimo' => $stock_minimo,
                ':categoria_id' => $categoria_id
            ]);

            $producto_id = $pdo->lastInsertId();
            $sql_mov = "INSERT INTO movimientos (fecha_hora, tipo, cantidad, observacion, id_producto, id_usuario) 
                        VALUES (NOW(), 'Entrada', :cantidad, 'Stock inicial por registro', :id_producto, :id_usuario)";
            $stmt_mov = $pdo->prepare($sql_mov);
            $stmt_mov->execute([
                ':cantidad' => $stock_actual,
                ':id_producto' => $producto_id,
                ':id_usuario' => $_SESSION['usuario_id'] ?? 1
            ]);

            echo "Producto guardado con éxito! <a href='index.html'>Volver</a>";
        } catch (PDOException $e) {
            echo "Error al guardar el producto: " . $e->getMessage();
        }
    } else {
        echo "Por favor complete todos los campos obligatorios.";
    }
} else {
    header('Location: index.html');
    exit();
}
?>