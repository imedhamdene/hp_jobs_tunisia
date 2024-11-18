<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the job ID from the URL
$id = intval($_GET['id']);

// Fetch the job details from the database
$sql = "SELECT * FROM positions WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Aucun emploi trouvé.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the new job data from the form
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Update the job details in the database
    $update_sql = "UPDATE positions SET title = ?, description = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('ssi', $title, $description, $id);

    if ($update_stmt->execute()) {
        echo "Emploi mis à jour avec succès.";
        header("Location: admin.php");
        exit;
    } else {
        echo "Erreur lors de la mise à jour de l'emploi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'emploi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        .form-container h1 {
            text-align: center;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h1>Modifier l'emploi</h1>
        <form method="POST" action="">
            <label for="title">Titre de l'emploi:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>

            <label for="description">Description de l'emploi:</label>
            <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($row['description']); ?></textarea>

            <button type="submit">Mettre à jour</button>
        </form>
    </div>

</body>
</html>

<?php
// Close the database connection
$stmt->close();
$conn->close();
?>
