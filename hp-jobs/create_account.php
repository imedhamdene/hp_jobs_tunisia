<?php
// Start a session
session_start();

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error variable
$error = null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // File upload paths
    $cv_path = null;
    $motivation_letter_path = null;

    // Handle CV upload
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] == UPLOAD_ERR_OK) {
        $cv_path = 'uploads/' . basename($_FILES['cv']['name']);
        move_uploaded_file($_FILES['cv']['tmp_name'], $cv_path);
    }

    // Handle motivation letter upload
    if (isset($_FILES['motivation_letter']) && $_FILES['motivation_letter']['error'] == UPLOAD_ERR_OK) {
        $motivation_letter_path = 'uploads/' . basename($_FILES['motivation_letter']['name']);
        move_uploaded_file($_FILES['motivation_letter']['tmp_name'], $motivation_letter_path);
    }

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if ($checkEmail) {
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            $error = "Cet email est déjà utilisé. Veuillez en choisir un autre.";
        } else {
            // Insert into the users table
            $sql = "INSERT INTO users (firstname, lastname, email, password, role, created_at) VALUES (?, ?, ?, ?, 'candidate', NOW())";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssss", $firstname, $lastname, $email, $password);

                if ($stmt->execute()) {
                    // Get the ID of the newly inserted user
                    $user_id = $conn->insert_id;

                    // Insert into the candidates table
                    $sql = "INSERT INTO candidates (id, phone, address, cv_path, motivation_letter_path, position_id) VALUES (?, ?, ?, ?, ?, 1)";
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("issss", $user_id, $phone, $address, $cv_path, $motivation_letter_path);
                        
                        if ($stmt->execute()) {
                            // Redirect to login or profile page
                            header("Location: login.php");
                            exit;
                        } else {
                            $error = "Erreur lors de la création du profil candidat.";
                        }
                        $stmt->close();
                    } else {
                        $error = "Erreur lors de la préparation de la requête pour la table candidates.";
                    }
                } else {
                    $error = "Erreur lors de la création de l'utilisateur.";
                }
                $stmt->close();
            } else {
                $error = "Erreur lors de la préparation de la requête pour la table users.";
            }
        }
        $checkEmail->close();
    } else {
        $error = "Erreur lors de la préparation de la requête pour vérifier l'email.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte candidat</title>
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    box-sizing: border-box;
}

.form-container {
    width: 100%;
    max-width: 500px;
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    box-sizing: border-box;
    position: relative;
}

h2 {
    margin-bottom: 20px;
    font-size: 1.5rem;
    color: #333;
    text-align: center;
}

form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

label {
    font-size: 0.9rem;
    color: #555;
    font-weight: bold;
}

input,
button {
    width: 100%;
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

input:focus {
    outline: none;
    border-color: #666;
}

button {
    background-color: #333;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #555;
}

.error {
    color: red;
    margin-bottom: 15px;
    font-size: 0.9rem;
}

.signup-link,
.home-link {
    text-align: center;
    margin-top: 15px;
    font-size: 0.9rem;
}

.signup-link a,
.home-link a {
    color: #333;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s ease;
}

.signup-link a:hover,
.home-link a:hover {
    color: #555;
    text-decoration: underline;
}

    </style>
</head>
<body>
    <div class="form-container">
        <h2>Créer un compte candidat</h2>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="firstname">Prénom :</label>
            <input type="text" id="firstname" name="firstname" required>

            <label for="lastname">Nom :</label>
            <input type="text" id="lastname" name="lastname" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <label for="phone">Téléphone :</label>
            <input type="text" id="phone" name="phone" required>

            <label for="address">Adresse :</label>
            <input type="text" id="address" name="address" required>

            <label for="cv">CV :</label>
            <input type="file" id="cv" name="cv" required>

            <label for="motivation_letter">Lettre de Motivation :</label>
            <input type="file" id="motivation_letter" name="motivation_letter" required>

            <button type="submit">Créer un compte</button>
        </form>
        <div class="signup-link">
            <p>Vous avez déjà un compte ? 
            <br>
            <a href="login.php">Se connecter</a></p>
        </div>
        <div class="home-link">
            <p><a href="index.php">Retour à l'accueil</a></p>
        </div>
    </div>
</body>
</html>
