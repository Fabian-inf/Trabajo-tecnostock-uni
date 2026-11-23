<?php
require_once 'conexion.php';

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch();

    if (!$producto) {
        exit('Producto no encontrado.');
    }
} catch (PDOException $e) {
    exit('Error al buscar el producto.');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar producto - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container py-5">
        <div class="card shadow-sm mx-auto" style="max-width: 760px;">
            <div class="card-body p-4">
                <h1 class="h3 mb-3">Editar producto</h1>
                <form action="actualizar_producto.php" method="post">
                    <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="codigo" class="form-label">Código</label>
                            <input type="text" class="form-control" id="codigo" name="codigo" value="<?= htmlspecialchars($producto['codigo']) ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria_id" name="categoria_id" required>
                                <option value="1" <?= $producto['id_categoria'] == 1 ? 'selected' : '' ?>>Accesorios</option>
                                <option value="2" <?= $producto['id_categoria'] == 2 ? 'selected' : '' ?>>Almacenamiento</option>
                                <option value="3" <?= $producto['id_categoria'] == 3 ? 'selected' : '' ?>>Conectividad</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="precio" name="precio" step="0.01" value="<?= $producto['precio'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="stock_actual" class="form-label">Stock actual</label>
                            <input type="number" class="form-control" id="stock_actual" name="stock_actual" value="<?= $producto['stock_actual'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="stock_minimo" class="form-label">Stock mínimo</label>
                            <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" value="<?= $producto['stock_minimo'] ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-success">Guardar cambios</button>
                            <a href="listar_productos.php" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>