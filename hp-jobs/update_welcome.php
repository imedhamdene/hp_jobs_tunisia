<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the new content from the form
    $newContent = $_POST['welcome'];

    // Update the 'welcome' section in the site_content table
    $sql = "UPDATE site_content SET content = ? WHERE section_name = 'welcome'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $newContent);

    if ($stmt->execute()) {
        echo "La section 'À propos' a été mise à jour avec succès.";
        // Redirect to a page or display a success message
        header("Location: admin.php");
        exit;
    } else {
        echo "Erreur lors de la mise à jour de la section: " . $conn->error;
    }
}

// Close the database connection
$stmt->close();
$conn->close();
?>
