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
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    // Handle file uploads (CV and Motivation Letter)
    $cv_path = '';
    $motivation_letter_path = '';

    if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
        $cv_path = 'uploads/' . basename($_FILES['cv']['name']);
        move_uploaded_file($_FILES['cv']['tmp_name'], $cv_path);
    }

    if (isset($_FILES['motivation_letter']) && $_FILES['motivation_letter']['error'] == 0) {
        $motivation_letter_path = 'uploads/' . basename($_FILES['motivation_letter']['name']);
        move_uploaded_file($_FILES['motivation_letter']['tmp_name'], $motivation_letter_path);
    }
    
    // Validate input data (basic validation)
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($phone) || empty($address) || empty($cv_path) || empty($motivation_letter_path)) {
        $error = "Tous les champs sont obligatoires, y compris les fichiers.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert the user into the 'users' table with the role 'candidate'
        $sql = "INSERT INTO users (firstname, lastname, email, password, role) VALUES (?, ?, ?, ?, 'candidate')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $firstname, $lastname, $email, $hashed_password);

        if ($stmt->execute()) {
            // Get the last inserted user ID (to insert into candidates table)
            $user_id = $stmt->insert_id;
            
            // Insert the candidate-specific data into the 'candidates' table
            $sql_candidate = "INSERT INTO candidates (id, phone, address, profile_picture, cv_path, motivation_letter_path, position_id) 
                            VALUES (?, ?, ?, NULL, ?, ?, NULL)";
            $stmt_candidate = $conn->prepare($sql_candidate);
            $stmt_candidate->bind_param("issss", $user_id, $phone, $address, $cv_path, $motivation_letter_path );
            
            if ($stmt_candidate->execute()) {
                // Success message
                header('Location: admin.php'); // Redirect to a success page after account creation
                exit();
            } else {
                $error = "Erreur lors de l'ajout des informations du candidat. Veuillez réessayer.";
            }
        } else {
            $error = "Erreur lors de la création du compte. Veuillez réessayer.";
        }

        $stmt->close();
        $stmt_candidate->close();
    }
}

// Close the database connection
$conn->close();
?>
