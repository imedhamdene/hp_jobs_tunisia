<?php
// Include database connection
$conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

// Check for connection errors
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

// Initialize variables
$error = "";
$success = "";

// Check if `id` is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de l'utilisateur manquant.");
}

$user_id = intval($_GET['id']);

// Fetch user details
$sql = "SELECT id, firstname, lastname, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Utilisateur non trouvé.");
}

$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Validate inputs
    if (empty($firstname) || empty($lastname) || empty($email) || empty($role)) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        // Check if the email is unique for other users
        $check_email_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt_check_email = $conn->prepare($check_email_sql);
        $stmt_check_email->bind_param("si", $email, $user_id);
        $stmt_check_email->execute();
        $result_check_email = $stmt_check_email->get_result();

        if ($result_check_email->num_rows > 0) {
            $error = "Cet email est déjà utilisé par un autre utilisateur.";
        } else {
            // Update the user in the database
            $update_sql = "UPDATE users SET firstname = ?, lastname = ?, email = ?, role = ? WHERE id = ?";
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("ssssi", $firstname, $lastname, $email, $role, $user_id);

            if ($stmt_update->execute()) {
                $success = "Les informations de l'utilisateur ont été mises à jour avec succès.";
                // Refresh the user data
                $user['firstname'] = $firstname;
                $user['lastname'] = $lastname;
                $user['email'] = $email;
                $user['role'] = $role;
            } else {
                $error = "Erreur lors de la mise à jour des informations de l'utilisateur.";
            }

            $stmt_update->close();
        }

        $stmt_check_email->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Utilisateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }
        .form-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }
        .form-container .error {
            color: red;
            margin-bottom: 15px;
        }
        .form-container .success {
            color: green;
            margin-bottom: 15px;
        }
        .form-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-container input, .form-container select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-container button {
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-container .submit-btn {
            background-color: red; /* Change to black or grey if preferred */
        }
        .form-container .submit-btn:hover {
            background-color: darkred;
        }
        .form-container .return-btn {
            background-color: grey; /* Change to black or red if preferred */
            margin-top: 10px;
        }
        .form-container .return-btn:hover {
            background-color: darkgrey;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Modifier l'utilisateur</h2>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>
        <form method="POST">
            <label for="firstname">Prénom :</label>
            <input type="text" id="firstname" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" required>

            <label for="lastname">Nom :</label>
            <input type="text" id="lastname" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="role">Rôle :</label>
            <select id="role" name="role" required>
                <option value="candidate" <?= $user['role'] === 'candidate' ? 'selected' : '' ?>>Candidat</option>
                <option value="hr" <?= $user['role'] === 'hr' ? 'selected' : '' ?>>RH</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
            </select>

            <button type="submit" class="submit-btn">Enregistrer</button>
        </form>

        <!-- Return button -->
        <button class="return-btn" onclick="window.location.href='admin.php'">Retour à l'administration</button>
    </div>
</body>
</html>
