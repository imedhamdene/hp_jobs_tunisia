<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the ID from the URL
$id = intval($_GET['id']);

// Prepare and execute the delete query
$sql = "DELETE FROM positions WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo "L'emploi a été supprimé avec succès.";
    // Redirect back to the job management page after deletion (optional)
    header("Location: admin.php");
    exit;
} else {
    echo "Erreur lors de la suppression de l'emploi: " . $conn->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
