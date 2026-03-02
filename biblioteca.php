<?php
// 1. Conexión a la base de datos
$host = "127.0.0.1:3307";
$user = "root";
$pass = "";
$db   = "biblioteca";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$mensaje = "";
$update_mode = false;
$id = ""; $titulo = ""; $autor = ""; $anio = ""; $genero = "";

// 2. CREATE / UPDATE (Procesar formulario vía POST)
if (isset($_POST['guardar'])) {
    $titulo = $_POST['titulo'];
    $autor  = $_POST['autor'];
    $anio   = $_POST['anio'];
    $genero = $_POST['genero'];

    // Validaciones
    if (!empty($titulo) && !empty($autor) && !empty($anio) && !empty($genero)) {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Lógica de UPDATE
            $id = $_POST['id'];
            $sql = "UPDATE libros SET titulo='$titulo', autor='$autor', anio='$anio', genero='$genero' WHERE id=$id";
            $mensaje = "¡Libro actualizado con éxito!";
        } else {
            // Lógica de CREATE
            $sql = "INSERT INTO libros (titulo, autor, anio, genero) VALUES ('$titulo', '$autor', '$anio', '$genero')";
            $mensaje = "¡Libro registrado con éxito!"; 
        }
        $conn->query($sql);
        // Limpiar variables después de guardar
        $titulo = ""; $autor = ""; $anio = ""; $genero = "";
    } else {
        $mensaje = "Error: Todos los campos son obligatorios.";
    }
}

// 3. DELETE (Procesar vía GET) 
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM libros WHERE id=$id");
    $mensaje = "Registro eliminado."; 
}

// 4. Cargar datos para EDITAR (Procesar vía GET) [cite: 23, 27]
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $update_mode = true;
    $result = $conn->query("SELECT * FROM libros WHERE id=$id");
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $titulo = $row['titulo'];
        $autor  = $row['autor'];
        $anio   = $row['anio'];
        $genero = $row['genero'];
    }
}

// 5. READ (Obtener registros para la tabla) 
$libros = $conn->query("SELECT * FROM libros");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Biblioteca</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .mensaje { padding: 10px; margin-bottom: 10px; background: #e7f3fe; color: #31708f; border: 1px solid #bcdff1; }
    </style>
</head>
<body>

    <h2>Sistema de Libros</h2>

    <?php if ($mensaje): ?>
        <div class="mensaje"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="text" name="titulo" placeholder="Título" value="<?php echo $titulo; ?>" required>
        <input type="text" name="autor" placeholder="Autor" value="<?php echo $autor; ?>" required>
        <input type="number" name="anio" placeholder="Año" value="<?php echo $anio; ?>" required>
        <input type="text" name="genero" placeholder="Género" value="<?php echo $genero; ?>" required>
        
        <button type="submit" name="guardar">
            <?php echo $update_mode ? "Actualizar" : "Guardar"; ?>
        </button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Año</th>
                <th>Género</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $libros->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['titulo']; ?></td>
                <td><?php echo $row['autor']; ?></td>
                <td><?php echo $row['anio']; ?></td>
                <td><?php echo $row['genero']; ?></td>
                <td>
                    <a href="biblioteca.php?edit=<?php echo $row['id']; ?>">Editar</a> | 
                    <a href="biblioteca.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('¿Eliminar?')">Borrar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>