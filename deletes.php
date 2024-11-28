
<?php
require 'datab.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");//sentencia SQL identificar y deletar
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: indexcrud.php");
    exit();
}
?>
