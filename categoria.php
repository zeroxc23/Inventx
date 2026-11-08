<?php
require_once "conexion.php";

$mensaje = "";
$editar = false;

$id_editar = "";
$nombre_editar = "";
$descripcion_editar = "";

/* GUARDAR */
if (isset($_POST["guardar"])) {

    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];

    $stmt = $conexion->prepare(
        "INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)"
    );

    $stmt->bind_param("ss", $nombre, $descripcion);

    if ($stmt->execute()) {
        $mensaje = "Categoría guardada correctamente.";
    } else {
        $mensaje = "Error al guardar la categoría.";
    }
}

/* ACTUALIZAR */
if (isset($_POST["actualizar"])) {

    $id = $_POST["id_categoria"];
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];

    $stmt = $conexion->prepare(
        "UPDATE categorias
         SET nombre = ?, descripcion = ?
         WHERE id_categoria = ?"
    );

    $stmt->bind_param("ssi", $nombre, $descripcion, $id);

    if ($stmt->execute()) {
        $mensaje = "Categoría actualizada correctamente.";
    }
}

/* ELIMINAR */
if (isset($_GET["eliminar"])) {

    $id = $_GET["eliminar"];

    $stmt = $conexion->prepare(
        "DELETE FROM categorias WHERE id_categoria = ?"
    );

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $mensaje = "Categoría eliminada correctamente.";
    } else {
        $mensaje = "No se puede eliminar porque tiene productos relacionados.";
    }
}

/* EDITAR */
if (isset($_GET["editar"])) {

    $editar = true;

    $id = $_GET["editar"];

    $stmt = $conexion->prepare(
        "SELECT * FROM categorias WHERE id_categoria = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $datos = $stmt->get_result()->fetch_assoc();

    if ($datos) {
        $id_editar = $datos["id_categoria"];
        $nombre_editar = $datos["nombre"];
        $descripcion_editar = $datos["descripcion"];
    }
}

/* CONSULTAR */
$resultado = $conexion->query(
    "SELECT * FROM categorias ORDER BY id_categoria DESC"
);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Categorías - INVENTX</title>

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

        <p class="subtitulo">INVENTX</p>

        <h1>Gestión de Categorías</h1>

        <p>
            Registra, consulta, actualiza y elimina categorías.
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
                ? "Actualizar categoría"
                : "Registrar categoría";
            ?>
        </h2>

        <form method="POST">

            <?php if ($editar): ?>

                <input
                    type="hidden"
                    name="id_categoria"
                    value="<?php echo $id_editar; ?>"
                >

            <?php endif; ?>


            <div class="campo">

                <label>Nombre</label>

                <input
                    type="text"
                    name="nombre"
                    required
                    value="<?php echo htmlspecialchars($nombre_editar); ?>"
                >

            </div>


            <div class="campo">

                <label>Descripción</label>

                <textarea name="descripcion"><?php
                    echo htmlspecialchars($descripcion_editar);
                ?></textarea>

            </div>


            <div class="botones-formulario">

                <?php if ($editar): ?>

                    <button
                        type="submit"
                        name="actualizar"
                        class="btn actualizar"
                    >
                        Actualizar
                    </button>

                    <a
                        href="categoria.php"
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
                        Guardar categoría
                    </button>

                <?php endif; ?>

            </div>

        </form>

    </section>


    <section class="tabla-container">

        <h2>Categorías registradas</h2>

        <div class="tabla-responsive">

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                <?php while ($fila = $resultado->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo $fila["id_categoria"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($fila["nombre"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($fila["descripcion"]); ?>
                        </td>

                        <td class="acciones">

                            <a
                                href="categoria.php?editar=<?php echo $fila["id_categoria"]; ?>"
                                class="btn-editar"
                            >
                                Editar
                            </a>

                            <a
                                href="categoria.php?eliminar=<?php echo $fila["id_categoria"]; ?>"
                                class="btn-eliminar"
                                onclick="return confirm('¿Eliminar esta categoría?');"
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