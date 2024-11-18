<?php
session_start();

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get email and password from form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL query to fetch user
    $sql = "SELECT id, firstname, lastname, password, role FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Store user information in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname'] = $user['lastname'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on user role
                if ($user['role'] === 'admin') {
                    header('Location: admin.php');
                }elseif ($user['role'] === 'hr') {
                    header('Location: hr-dashboard.php');
                } elseif ($user['role'] === 'candidate') {
                    header('Location: candidate-dashboard.php');
                } else {
                    // If the role is unknown, log out
                    echo "Unknown role. Please contact support.";
                    session_destroy();
                }
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No user found with that email address.";
        }

        // Close statement
        $stmt->close();
    } else {
        $error = "Error preparing SQL statement: " . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HP Tunisie</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .login-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container label {
            margin-bottom: 5px;
        }

        .login-container input {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container button {
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #555;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }

        .signup-link, .home-link {
            text-align: center;
            margin-top: 20px;
        }

        .signup-link a, .home-link a {
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }

        .signup-link a:hover, .home-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>

        <!-- Display error message -->
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Login form -->
        <form method="POST" action="login.php">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <!-- Signup link -->
        <div class="signup-link">
            <p>Pas encore de compte ? 
            <br>
            <a href="create_account.php">Créez un compte</a></p>
        </div>
        <div class="home-link">
            <p><a href="index.php">Retour à l'accueil</a></p>
        </div>
    </div>
</body>
</html>
