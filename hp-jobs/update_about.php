<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the new content from the form
    $content = $_POST['about'];

    // Update the 'about_us' section in the database
    $sql = "UPDATE site_content SET content = ? WHERE section_name = 'about_us'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $content);

    if ($stmt->execute()) {
        echo "Section 'À propos' mise à jour avec succès.";
        header("Location: admin.php"); // Redirect to a management page if needed
        exit;
    } else {
        echo "Erreur lors de la mise à jour: " . $conn->error;
    }
}

// Close connection
$stmt->close();
$conn->close();
?>
