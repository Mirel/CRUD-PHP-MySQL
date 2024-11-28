# Query: 
# ContextLines: 1

<?php 
require 'db.php'; 
 
// Obtener usuario por ID 
$id = $_GET['id']; 
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?"); //sentencia sql para modificar la bd
$stmt->execute([$id]); 
$user = $stmt->fetch(PDO::FETCH_ASSOC); 
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $name = $_POST['name']; 
    $email = $_POST['email']; 
    $age = $_POST['age']; 
 
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, age = ? WHERE id = ?"); 
    $stmt->execute([$name, $email, $age, $id]); 
    header("Location: indexcrud.php"); 
} 
?> 

 