<?php
// Start session (if needed)
session_start();

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form values
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']); // New field for description

    // Insert into the positions table
    $sql = "INSERT INTO positions (title, description, created_at) VALUES ('$category', '$description', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "Nouvelle catégorie ajoutée avec succès.";
    } else {
        echo "Erreur: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="0; URL=admin.php"> <!-- Redirects to index page after 0 seconds -->
    <title>Ajout de catégorie</title>
</head>
<body>
    <p>Redirection vers la page principale...</p>
</body>
</html>
