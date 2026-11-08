<?php

require_once "conexion.php";

$mensaje = "";
$editar = false;

$id_editar = "";
$codigo_editar = "";
$nombre_editar = "";
$descripcion_editar = "";
$precio_editar = "";
$stock_editar = "";
$stock_minimo_editar = "";
$categoria_editar = "";
$proveedor_editar = "";


/* =========================
   GUARDAR
========================= */

if (isset($_POST["guardar"])) {

    $codigo = $_POST["codigo"];
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $stock_minimo = $_POST["stock_minimo"];
    $id_categoria = $_POST["id_categoria"];
    $id_proveedor = $_POST["id_proveedor"];

    $stmt = $conexion->prepare(
        "INSERT INTO productos
        (codigo, nombre, descripcion, precio, stock,
         stock_minimo, id_categoria, id_proveedor)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sssdiiii",
        $codigo,
        $nombre,
        $descripcion,
        $precio,
        $stock,
        $stock_minimo,
        $id_categoria,
        $id_proveedor
    );

    if ($stmt->execute()) {
        $mensaje = "Producto guardado correctamente.";
    } else {
        $mensaje = "Error al guardar el producto.";
    }
}


/* =========================
   ACTUALIZAR
========================= */

if (isset($_POST["actualizar"])) {

    $id = $_POST["id_producto"];

    $codigo = $_POST["codigo"];
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $stock_minimo = $_POST["stock_minimo"];
    $id_categoria = $_POST["id_categoria"];
    $id_proveedor = $_POST["id_proveedor"];

    $stmt = $conexion->prepare(
        "UPDATE productos SET
        codigo = ?,
        nombre = ?,
        descripcion = ?,
        precio = ?,
        stock = ?,
        stock_minimo = ?,
        id_categoria = ?,
        id_proveedor = ?
        WHERE id_producto = ?"
    );

    $stmt->bind_param(
        "sssdiiiii",
        $codigo,
        $nombre,
        $descripcion,
        $precio,
        $stock,
        $stock_minimo,
        $id_categoria,
        $id_proveedor,
        $id
    );

    if ($stmt->execute()) {
        $mensaje = "Producto actualizado correctamente.";
    } else {
        $mensaje = "Error al actualizar el producto.";
    }
}


/* =========================
   ELIMINAR
========================= */

if (isset($_GET["eliminar"])) {

    $id = $_GET["eliminar"];

    $stmt = $conexion->prepare(
        "DELETE FROM productos
         WHERE id_producto = ?"
    );

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $mensaje = "Producto eliminado correctamente.";
    } else {
        $mensaje = "Error al eliminar el producto.";
    }
}


/* =========================
   EDITAR
========================= */

if (isset($_GET["editar"])) {

    $editar = true;

    $id = $_GET["editar"];

    $stmt = $conexion->prepare(
        "SELECT * FROM productos
         WHERE id_producto = ?"
    );

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $datos = $stmt->get_result()->fetch_assoc();

    if ($datos) {

        $id_editar = $datos["id_producto"];
        $codigo_editar = $datos["codigo"];
        $nombre_editar = $datos["nombre"];
        $descripcion_editar = $datos["descripcion"];
        $precio_editar = $datos["precio"];
        $stock_editar = $datos["stock"];
        $stock_minimo_editar = $datos["stock_minimo"];
        $categoria_editar = $datos["id_categoria"];
        $proveedor_editar = $datos["id_proveedor"];
    }
}


/* =========================
   CATEGORÍAS
========================= */

$categorias = $conexion->query(
    "SELECT * FROM categorias
     ORDER BY nombre"
);


/* =========================
   PROVEEDORES
========================= */

$proveedores = $conexion->query(
    "SELECT * FROM proveedores
     ORDER BY nombre"
);


/* =========================
   PRODUCTOS
========================= */

$resultado = $conexion->query(
    "SELECT
        p.id_producto,
        p.codigo,
        p.nombre,
        p.descripcion,
        p.precio,
        p.stock,
        p.stock_minimo,
        c.nombre AS categoria,
        pr.nombre AS proveedor

     FROM productos p

     INNER JOIN categorias c
     ON p.id_categoria = c.id_categoria

     INNER JOIN proveedores pr
     ON p.id_proveedor = pr.id_proveedor

     ORDER BY p.id_producto DESC"
);

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Productos - INVENTX</title>

    <link rel="stylesheet" href="css/estilo.css">

</head>

<body>


<header class="navbar">

    <div class="logo">
        INVENT<span>X</span>
    </div>

    <nav>
        <a href="inicio.html">Inicio</a>
    </nav>

</header>


<main class="crud-container">


    <section class="titulo-crud">

        <p class="subtitulo">
            INVENTX
        </p>

        <h1>
            Gestión de Productos
        </h1>

        <p>
            Registra y administra los productos del inventario.
        </p>

    </section>


    <?php if ($mensaje != ""): ?>

        <div class="mensaje exito">
            <?php echo $mensaje; ?>
        </div>

    <?php endif; ?>


    <section class="formulario">

        <h2>

            <?php

            echo $editar
                ? "Actualizar producto"
                : "Registrar producto";

            ?>

        </h2>


        <form method="POST">


            <?php if ($editar): ?>

                <input
                    type="hidden"
                    name="id_producto"
                    value="<?php echo $id_editar; ?>"
                >

            <?php endif; ?>


            <div class="grid-form">


                <div class="campo">

                    <label>
                        Código
                    </label>

                    <input
                        type="text"
                        name="codigo"
                        required
                        value="<?php echo htmlspecialchars($codigo_editar); ?>"
                    >

                </div>


                <div class="campo">

                    <label>
                        Nombre
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        required
                        value="<?php echo htmlspecialchars($nombre_editar); ?>"
                    >

                </div>


                <div class="campo">

                    <label>
                        Precio
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        name="precio"
                        required
                        value="<?php echo $precio_editar; ?>"
                    >

                </div>


                <div class="campo">

                    <label>
                        Stock
                    </label>

                    <input
                        type="number"
                        name="stock"
                        min="0"
                        required
                        value="<?php echo $stock_editar; ?>"
                    >

                </div>


                <div class="campo">

                    <label>
                        Stock mínimo
                    </label>

                    <input
                        type="number"
                        name="stock_minimo"
                        min="0"
                        required
                        value="<?php echo $stock_minimo_editar; ?>"
                    >

                </div>


                <div class="campo">

                    <label>
                        Categoría
                    </label>

                    <select
                        name="id_categoria"
                        required
                    >

                        <option value="">
                            Seleccionar categoría
                        </option>

                        <?php while ($categoria = $categorias->fetch_assoc()): ?>

                            <option
                                value="<?php echo $categoria["id_categoria"]; ?>"
                                <?php
                                if (
                                    $categoria_editar ==
                                    $categoria["id_categoria"]
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >

                                <?php echo htmlspecialchars($categoria["nombre"]); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <div class="campo">

                    <label>
                        Proveedor
                    </label>

                    <select
                        name="id_proveedor"
                        required
                    >

                        <option value="">
                            Seleccionar proveedor
                        </option>

                        <?php while ($proveedor = $proveedores->fetch_assoc()): ?>

                            <option
                                value="<?php echo $proveedor["id_proveedor"]; ?>"
                                <?php
                                if (
                                    $proveedor_editar ==
                                    $proveedor["id_proveedor"]
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >

                                <?php echo htmlspecialchars($proveedor["nombre"]); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

            </div>


            <div class="campo">

                <label>
                    Descripción
                </label>

                <textarea
                    name="descripcion"
                ><?php echo htmlspecialchars($descripcion_editar); ?></textarea>

            </div>


            <div class="botones-formulario">


                <?php if ($editar): ?>

                    <button
                        type="submit"
                        name="actualizar"
                        class="btn actualizar"
                    >
                        Actualizar producto
                    </button>


                    <a
                        href="producto.php"
                        class="btn cancelar"
                    >
                        Cancelar
                    </a>


                <?php else: ?>

                    <button
                        type="submit"
                        name="guardar"
                        class="btn guardar"
                    >
                        Guardar producto
                    </button>

                <?php endif; ?>


            </div>

        </form>

    </section>


    <section class="tabla-container">

        <h2>
            Productos registrados
        </h2>


        <div class="tabla-responsive">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Stock mínimo</th>
                        <th>Categoría</th>
                        <th>Proveedor</th>
                        <th>Acciones</th>

                    </tr>

                </thead>


                <tbody>


                <?php while ($fila = $resultado->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo $fila["id_producto"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($fila["codigo"]); ?>
                        </td>

                        <td>

                            <strong>
                                <?php echo htmlspecialchars($fila["nombre"]); ?>
                            </strong>

                            <br>

                            <small>
                                <?php echo htmlspecialchars($fila["descripcion"]); ?>
                            </small>

                        </td>

                        <td>
                            $<?php echo number_format(
                                $fila["precio"],
                                2
                            ); ?>
                        </td>

                        <td>
                            <?php echo $fila["stock"]; ?>
                        </td>

                        <td>
                            <?php echo $fila["stock_minimo"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($fila["categoria"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($fila["proveedor"]); ?>
                        </td>

                        <td class="acciones">

                            <a
                                href="producto.php?editar=<?php echo $fila["id_producto"]; ?>"
                                class="btn-editar"
                            >
                                Editar
                            </a>


                            <a
                                href="producto.php?eliminar=<?php echo $fila["id_producto"]; ?>"
                                class="btn-eliminar"
                                onclick="return confirm('¿Eliminar este producto?');"
                            >
                                Eliminar
                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>


                </tbody>

            </table>

        </div>

    </section>

</main>


<footer>
    © 2026 INVENTX - Sistema de Inventario
</footer>


</body>

</html>