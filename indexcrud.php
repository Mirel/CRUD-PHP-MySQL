<?php
require 'datab.php';  // Incluye la conexión a la base de datos utilizando MySQLi

// Variable para almacenar mensajes de estado, como si la acción se completó correctamente
$message = '';

// Manejo de la solicitud POST para agregar o actualizar un usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Se obtienen los valores enviados en el formulario
    $name = $_POST['name'];
    $email = $_POST['email'];
    $age = $_POST['age'];

    // Acción para agregar un nuevo usuario
    if ($_POST['action'] == 'add') {
        // Preparamos la consulta SQL para insertar un nuevo usuario en la base de datos
        $stmt = $conn->prepare("INSERT INTO users (name, email, age) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $email, $age);  // Los datos son un string, un string y un entero
        // Ejecutamos la consulta y verificamos si fue exitosa
        if ($stmt->execute()) {
            $message = "Usuario agregado correctamente.";  // Mensaje de éxito
        }
        $stmt->close();  // Cerramos la declaración SQL
    } 
    // Acción para actualizar un usuario existente
    elseif ($_POST['action'] == 'update' && isset($_POST['id'])) {
        $id = $_POST['id'];  // ID del usuario que se va a modificar
        // Preparamos la consulta SQL para actualizar un usuario
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, age = ? WHERE id = ?");
        $stmt->bind_param("ssii", $name, $email, $age, $id);  // Se pasan los parámetros: dos strings y dos enteros
        // Ejecutamos la consulta y verificamos si fue exitosa
        if ($stmt->execute()) {
            $message = "Usuario modificado correctamente.";  // Mensaje de éxito
        }
        $stmt->close();  // Cerramos la declaración SQL
    }
    // Redirigir al formulario para agregar un nuevo usuario
    header("Location: indexcrud.php");
    exit();
}

// Acción para eliminar un usuario cuando se pasa el parámetro 'delete_id'
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];  // ID del usuario que se va a eliminar
    // Preparamos la consulta SQL para eliminar el usuario de la base de datos
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);  // Pasamos el ID como parámetro entero
    // Ejecutamos la consulta y verificamos si fue exitosa
    if ($stmt->execute()) {
        $message = "Usuario eliminado correctamente.";  // Mensaje de éxito
    }
    $stmt->close();  // Cerramos la declaración SQL
    // Redirigir al formulario para agregar un nuevo usuario
    header("Location: indexcrud.php");
    exit();
}

// Obtener un usuario por su ID si está presente en la URL para permitir su edición
$user = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];  // Obtenemos el ID del usuario desde la URL
    // Preparamos la consulta SQL para seleccionar el usuario con el ID especificado
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);  // Pasamos el ID como parámetro entero
    $stmt->execute();  // Ejecutamos la consulta
    $result = $stmt->get_result();  // Obtenemos el resultado de la consulta
    $user = $result->fetch_assoc();  // Obtenemos el usuario como un arreglo asociativo
    $stmt->close();  // Cerramos la declaración SQL
}

// Obtener todos los usuarios de la base de datos para mostrarlos en una tabla
$stmt = $conn->query("SELECT * FROM users");
$users = $stmt->fetch_all(MYSQLI_ASSOC);  // Obtenemos todos los usuarios en un arreglo asociativo
$stmt->close();  // Cerramos la declaración SQL
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD con PHP y MySQLi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input[type="text"], input[type="email"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .clear-button {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .clear-button:hover {
            background-color: #e60000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        .message {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        .error {
            text-align: center;
            color: red;
        }

        .actions a {
            margin-right: 10px;
            color: #007BFF;
            text-decoration: none;
        }

        .actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Usuarios</h1>

        <!-- Mostrar mensaje de éxito si alguna acción fue completada -->
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <h2><?= $user ? 'Editar Usuario' : 'Agregar Nuevo Usuario' ?></h2>
        <!-- Formulario para agregar o editar un usuario -->
        <form method="POST">
            <!-- Campo oculto para enviar el ID del usuario cuando se edita -->
            <input type="hidden" name="id" value="<?= $user['id'] ?? '' ?>"> 
            <div class="form-group">
                <input type="text" name="name" placeholder="Nombre" value="<?= $user['name'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Correo" value="<?= $user['email'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <input type="number" name="age" placeholder="Edad" value="<?= $user['age'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <!-- El valor del botón cambia dependiendo si se está agregando o actualizando -->
                <button type="submit" name="action" value="<?= $user ? 'update' : 'add' ?>"><?= $user ? 'Actualizar' : 'Agregar' ?></button>
                <!-- Botón para limpiar los campos del formulario -->
                <button type="reset" class="clear-button">Limpiar</button>
            </div>
        </form>

        <h2>Lista de Usuarios</h2>
        <!-- Tabla que lista todos los usuarios -->
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Edad</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['age']) ?></td>
                    <td class="actions">
                        <!-- Enlaces para editar o eliminar un usuario -->
                        <a href="indexcrud.php?id=<?= $user['id'] ?>">Editar</a>
                        <a href="indexcrud.php?delete_id=<?= $user['id'] ?>" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
