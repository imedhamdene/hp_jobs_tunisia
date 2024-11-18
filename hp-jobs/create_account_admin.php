<?php
// Start a session
session_start();

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form inputs
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validate input data (basic validation)
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert the data into the database
        $sql = "INSERT INTO users (firstname, lastname, email, password, role) VALUES (?, ?, ?, ?, 'admin')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $firstname, $lastname, $email, $hashed_password);

        if ($stmt->execute()) {
            // Success message
            header('Location: admin.php'); // Redirect to a success page after account creation
            exit();
        } else {
            $error = "Erreur lors de la création du compte. Veuillez réessayer.";
        }

        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>
