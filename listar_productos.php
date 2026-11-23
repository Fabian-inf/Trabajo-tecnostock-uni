<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require_once 'conexion.php';
$busqueda = trim($_GET['q'] ?? '');

try {
    if ($busqueda !== '') {
        $sql = "SELECT p.*, c.nombre as categoria_nombre
                FROM productos p
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE (p.estado = 1 OR p.estado IS NULL)
                  AND (p.codigo LIKE :q OR p.nombre LIKE :q)
                ORDER BY p.id_producto DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':q' => "%$busqueda%"]);
    } else {
        $sql = "SELECT p.*, c.nombre as categoria_nombre
                FROM productos p
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE (p.estado = 1 OR p.estado IS NULL)
                ORDER BY p.id_producto DESC";
        $stmt = $pdo->query($sql);
    }
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error al cargar los productos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventario - TecnoStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Inventario de Productos</h1>
            <div>
                <a href="index.html" class="btn btn-primary me-2">+ Registrar nuevo producto</a>
                <a href="logout.php" class="btn btn-outline-danger">Cerrar Sesion</a>
            </div>
        </div>

        <form method="get" class="input-group mb-4 shadow-sm">
            <input type="text" name="q" class="form-control" placeholder="Buscar por código o nombre..." value="<?= htmlspecialchars($busqueda) ?>">
            <button class="btn btn-outline-dark" type="submit">Buscar</button>
            <?php if ($busqueda !== ''): ?>
                <a href="listar_productos.php" class="btn btn-outline-secondary">Limpiar</a>
            <?php endif; ?>
        </form>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Categoria</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Stock Min.</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($productos)): ?>
                                <?php foreach ($productos as $prod): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($prod['codigo']) ?></td>
                                        <td><?= htmlspecialchars($prod['nombre']) ?></td>
                                        <td><?= htmlspecialchars($prod['categoria_nombre'] ?? 'Sin categoria') ?></td>
                                        <td>$<?= number_format($prod['precio'], 2) ?></td>
                                        <td>
                                            <span class="badge <?= $prod['stock_actual'] <= $prod['stock_minimo'] ? 'bg-danger' : 'bg-success' ?>">
                                                <?= $prod['stock_actual'] ?>
                                            </span>
                                        </td>
                                        <td><?= $prod['stock_minimo'] ?></td>
                                        <td class="text-center">
                                            <form action="registrar_salida.php" method="post" class="d-inline-flex gap-1 align-items-center mb-1">
                                            <input type="hidden" name="id_producto" value="<?= $prod['id_producto'] ?>">
                                            <input type="number" name="cantidad" value="1" min="1" max="<?= $prod['stock_actual'] ?>" class="form-control form-control-sm" style="width: 60px;" required>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Descontar stock">- Restar</button>
                                            </form>
                                            <a href="form_editar.php?id=<?= $prod['id_producto'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                            <a href="eliminar_producto.php?id=<?= $prod['id_producto'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-secondary py-4">No se encontraron productos registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>