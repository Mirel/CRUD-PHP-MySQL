<?php
$servername = "localhost";
$username = "root";  // MAMP usa "root" como nombre de usuario por defecto
$password = "root";  // MAMP usa "root" como contraseña por defecto
$dbname = "crud";  // Reemplaza con el nombre de tu base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
