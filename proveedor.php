<?php
require_once "conexion.php";

$mensaje = "";
$editar = false;

$id_editar = "";
$nombre_editar = "";
$nit_editar = "";
$telefono_editar = "";
$correo_editar = "";
$direccion_editar = "";

/* GUARDAR */
if (isset($_POST["guardar"])) {

    $nombre = $_POST["nombre"];
    $nit = $_POST["nit"];
    $telefono = $_POST["telefono"];
    $correo = $_POST["correo"];
    $direccion = $_POST["direccion"];

    $stmt = $conexion->prepare(
        "INSERT INTO proveedores
        (nombre, nit, telefono, correo, direccion)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sssss",
        $nombre,
        $nit,
        $telefono,
        $correo,
        $direccion
    );

    if ($stmt->execute()) {
        $mensaje = "Proveedor guardado correctamente.";
    } else {
        $mensaje = "Error al guardar el proveedor.";
    }
}

/* ACTUALIZAR */
if (isset($_POST["actualizar"])) {

    $id = $_POST["id_proveedor"];
    $nombre = $_POST["nombre"];
    $nit = $_POST["nit"];
    $telefono = $_POST["telefono"];
    $correo = $_POST["correo"];
    $direccion = $_POST["direccion"];

    $stmt = $conexion->prepare(
        "UPDATE proveedores SET
        nombre = ?,
        nit = ?,
        telefono = ?,
        correo = ?,
        direccion = ?
        WHERE id_proveedor = ?"
    );

    $stmt->bind_param(
        "sssssi",
        $nombre,
        $nit,
        $telefono,
        $correo,
        $direccion,
        $id
    );

    if ($stmt->execute()) {
        $mensaje = "Proveedor actualizado correctamente.";
    }
}

/* ELIMINAR */
if (isset($_GET["eliminar"])) {

    $id = $_GET["eliminar"];

    $stmt = $conexion->prepare(
        "DELETE FROM proveedores WHERE id_proveedor = ?"
    );

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $mensaje = "Proveedor eliminado correctamente.";
    } else {
        $mensaje = "No se puede eliminar porque tiene productos relacionados.";
    }
}

/* EDITAR */
if (isset($_GET["editar"])) {

    $editar = true;

    $id = $_GET["editar"];

    $stmt = $conexion->prepare(
        "SELECT * FROM proveedores WHERE id_proveedor = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $datos = $stmt->get_result()->fetch_assoc();

    if ($datos) {

        $id_editar = $datos["id_proveedor"];
        $nombre_editar = $datos["nombre"];
        $nit_editar = $datos["nit"];
        $telefono_editar = $datos["telefono"];
        $correo_editar = $datos["correo"];
        $direccion_editar = $datos["direccion"];
    }
}

/* CONSULTAR */
$resultado = $conexion->query(
    "SELECT * FROM proveedores ORDER BY id_proveedor DESC"
);
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Proveedores - INVENTX</title>

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

        <h1>Gestión de Proveedores</h1>

        <p>
            Registra, consulta, actualiza y elimina proveedores.
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
                ? "Actualizar proveedor"
                : "Registrar proveedor";
            ?>
        </h2>


        <form method="POST">

            <?php if ($editar): ?>

                <input
                    type="hidden"
                    name="id_proveedor"
                    value="<?php echo $id_editar; ?>"
                >

            <?php endif; ?>


            <div class="grid-form">

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

                    <label>NIT</label>

                    <input
                        type="text"
                        name="nit"
                        required
                        value="<?php echo htmlspecialchars($nit_editar); ?>"
                    >

                </div>


                <div class="campo">

                    <label>Teléfono</label>

                    <input
                        type="text"
                        name="telefono"
                        value="<?php echo htmlspecialchars($telefono_editar); ?>"
                    >

                </div>


                <div class="campo">

                    <label>Correo</label>

                    <input
                        type="email"
                        name="correo"
                        value="<?php echo htmlspecialchars($correo_editar); ?>"
                    >

                </div>

            </div>


            <div class="campo">

                <label>Dirección</label>

                <input
                    type="text"
                    name="direccion"
                    value="<?php echo htmlspecialchars($direccion_editar); ?>"
                >

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
                        href="proveedor.php"
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
                        Guardar proveedor
                    </button>

                <?php endif; ?>

            </div>

        </form>

    </section>


    <section class="tabla-container">

        <h2>Proveedores registrados</h2>

        <div class="tabla-responsive">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>NIT</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>

                </thead>

                <tbody>

                <?php while ($fila = $resultado->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo $fila["id_proveedor"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($fila["nombre"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($fila["nit"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($fila["telefono"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($fila["correo"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($fila["direccion"]); ?>
                        </td>

                        <td class="acciones">

                            <a
                                href="proveedor.php?editar=<?php echo $fila["id_proveedor"]; ?>"
                                class="btn-editar"
                            >
                                Editar
                            </a>

                            <a
                                href="proveedor.php?eliminar=<?php echo $fila["id_proveedor"]; ?>"
                                class="btn-eliminar"
                                onclick="return confirm('¿Eliminar este proveedor?');"
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