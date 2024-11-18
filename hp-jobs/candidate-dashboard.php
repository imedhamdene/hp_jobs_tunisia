
<?php
// Start output buffering to avoid issues with headers
ob_start();

// Start session
session_start();

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users u
        JOIN candidates c ON u.id = c.id
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Candidat - HP Tunisie</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #333;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            display: block;
            text-align: center;
            cursor: pointer;
        }

        .sidebar a:hover {
            background-color: #555;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .section {
            display: none;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .section.active {
            display: block;
        }

        h1 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .disconnect-btn {
            background-color: #ff4d4d;
            margin-top: auto;
        }

        .disconnect-btn:hover {
            background-color: #ff6666;
        }

        button {
            background-color: #333;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
        }

        /************************ */
        .description-block {
            background-color: #ddd;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .description-block h3 {
            margin-top: 0;
        }
        /************************************************** */
        #profile-section {
        max-width: 800px;
        margin: auto;
        padding: 20px;
        border-radius: 8px;
        background: #ccc;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        #profile-section h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .form-group {
            width: 100%;
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input[type="text"],
        .form-group input[type="email"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-group input[type="file"] {
            display: block;
            margin-top: 5px;
        }

        .file-path {
            display: block;
            font-size: 0.9em;
            color: #555;
            margin-top: 5px;
        }

        .submit-button {
            display: block;
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background: #333;
            color: #fff;
            font-size: 1em;
            cursor: pointer;
            margin-top: 20px;
        }

        .submit-button:hover {
            background: #555;
        }

        
    </style>


    <script>
        function showSection(sectionId) {
            var sections = document.getElementsByClassName('section');
            for (var i = 0; i < sections.length; i++) {
                sections[i].classList.remove('active');
            }
            document.getElementById(sectionId).classList.add('active');
        }

        window.onload = function() {
            showSection('profile-section');
            var urlParams = new URLSearchParams(window.location.search);
            var message = urlParams.get('message');
            if (message) {
                alert(message);
            }
        };
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Tableau de bord Candidat</h2>
        <a onclick="showSection('profile-section')">Profil</a>
        <a onclick="showSection('apply-section')">Postuler</a>
        <a onclick="showSection('meetings-section')">Entretiens</a>
        <a href="login.php" class="disconnect-btn">Déconnexion</a>
    </div>
    <div class="main-content">
        <div id="profile-section" class="section">
            <h1>Mon Profil</h1>
            <?php
            // Connect to database
            $conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch user data
            $sql = "SELECT * FROM users u
                    JOIN candidates c ON u.id = c.id
                    WHERE u.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                echo "<div class='profile-info'>
                        <img src='" . htmlspecialchars($user["profile_picture"]) . "' alt='Photo de profil' class='profile-picture'>
                        <form method='POST' action='update_profile.php' enctype='multipart/form-data'>
                            <div class='form-group'>
                                <label for='firstname'>Prénom:</label>
                                <input type='text' id='firstname' name='firstname' value='" . htmlspecialchars($user["firstname"]) . "' required>
                            </div>
                            <div class='form-group'>
                                <label for='lastname'>Nom:</label>
                                <input type='text' id='lastname' name='lastname' value='" . htmlspecialchars($user["lastname"]) . "' required>
                            </div>
                            <div class='form-group'>
                                <label for='email'>Email:</label>
                                <input type='email' id='email' name='email' value='" . htmlspecialchars($user["email"]) . "' required>
                            </div>
                            <div class='form-group'>
                                <label for='phone'>Téléphone:</label>
                                <input type='text' id='phone' name='phone' value='" . htmlspecialchars($user["phone"]) . "'>
                            </div>
                            <div class='form-group'>
                                <label for='address'>Adresse:</label>
                                <input type='text' id='address' name='address' value='" . htmlspecialchars($user["address"]) . "'>
                            </div>
                            <div class='form-group'>
                                <label for='profile_picture'>Photo de Profil:</label>
                                <input type='file' id='profile_picture' name='profile_picture'>
                                <span class='file-path'>Chemin actuel: " . htmlspecialchars($user["profile_picture"]) . "</span>
                            </div>
                            <div class='form-group'>
                                <label for='cv_path'>CV:</label>
                                <input type='file' id='cv_path' name='cv_path'>
                                <span class='file-path'>Chemin actuel: " . htmlspecialchars($user["cv_path"]) . "</span>
                            </div>
                            <div class='form-group'>
                                <label for='motivation_letter_path'>Lettre de Motivation:</label>
                                <input type='file' id='motivation_letter_path' name='motivation_letter_path'>
                                <span class='file-path'>Chemin actuel: " . htmlspecialchars($user["motivation_letter_path"]) . "</span>
                            </div>
                            <button type='submit' class='submit-button'>Mettre à jour</button>
                        </form>
                      </div>";
            } else {
                echo "<p>Aucun profil trouvé.</p>";
            }

            $conn->close();
            ?>
        </div>

        <div id="apply-section" class="section">
            <h1>Postuler</h1>

            <?php
            // Connect to database
            $conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Get the user ID from session
            $user_id = $_SESSION['user_id'];

            // Fetch the current position ID for the user
            $current_position_sql = "SELECT position_id FROM candidates WHERE id = ?";
            $stmt = $conn->prepare($current_position_sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $current_position_result = $stmt->get_result();
            $current_position = $current_position_result->fetch_assoc();
            $current_position_id = $current_position ? $current_position['position_id'] : null;

            // Fetch the current position title if exists
            if ($current_position_id) {
                $position_sql = "SELECT title FROM positions WHERE id = ?";
                $stmt = $conn->prepare($position_sql);
                $stmt->bind_param("i", $current_position_id);
                $stmt->execute();
                $position_result = $stmt->get_result();
                $position = $position_result->fetch_assoc();
                $current_position_title = $position ? $position['title'] : 'Aucun poste';
            } else {
                $current_position_title = 'Aucun poste';
            }

            // Flag to indicate successful submission
            $success = false;

            // Handle form submission
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply'])) {
                // Get position ID from the form
                $position_id = $_POST['position'];

                // Prepare and bind
                $stmt = $conn->prepare("UPDATE candidates SET position_id = ? WHERE id = ?");
                if ($stmt === false) {
                    die("Prepare failed: " . $conn->error);
                }

                $stmt->bind_param("ii", $position_id, $user_id);

                // Execute the query
                if ($stmt->execute()) {
                    $success = true;
                    // Update the current position after successful application
                    $current_position_id = $position_id;

                    $position_sql = "SELECT title FROM positions WHERE id = ?";
                    $stmt = $conn->prepare($position_sql);
                    $stmt->bind_param("i", $current_position_id);
                    $stmt->execute();
                    $position_result = $stmt->get_result();
                    $position = $position_result->fetch_assoc();
                    $current_position_title = $position ? $position['title'] : 'Aucun poste';
                } else {
                    echo "<p>Error: " . $stmt->error . "</p>";
                }

                // Close statement
                $stmt->close();
            }

            // Close database connection
            $conn->close();
            ?>

            <!-- Display current position -->
            <p>Vous postulez actuellement pour: <strong><?php echo htmlspecialchars($current_position_title); ?></strong></p>

            <form method="POST" action="">
                <label for="position">Poste:</label>
                <select id="position" name="position" required>
                    <?php
                    // Fetch available positions
                    $conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');
                    $sql = "SELECT id, title FROM positions";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row["id"] . "'>" . $row["title"] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Aucun poste disponible</option>";
                    }

                    $conn->close();
                    ?>
                </select>
                <button type="submit" name="apply">Postuler</button>
            </form>

            <?php
            if ($success) {
                echo "<p>Vous avez postulé avec succès pour le poste de " . htmlspecialchars($current_position_title) . ".</p>";
            }
            ?>
            <h3>Offres d'emploi disponibles :</h3>
            <div id="position-descriptions">
                <?php
                // Fetch available positions with descriptions
                $conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT title, description FROM positions";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='description-block'>";
                        echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
                        echo "<p>" . htmlspecialchars($row["description"]) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Aucune offre d'emploi disponible.</p>";
                }

                $conn->close();
                ?>
            </div>
        </div>

        <div id="meetings-section" class="section">
            <h1>Mes Entretiens</h1>
            <table>
                <thead>
                    <tr>
                        <th>Poste</th>
                        <th>Date</th>
                        <th>linguistique</th>
                        <th>technique</th>
                        <th>managériale</th>
                        <th>transversale</th>
                        <th>Commentaires RH
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Connect to database
                    $conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Fetch candidate meetings

                    $sql = "SELECT m.*, p.title, 
                                m.linguistic_rating, m.technical_rating, 
                                m.managerial_rating, m.transversal_rating, 
                                m.comment
                            FROM meetings m
                            JOIN positions p ON m.candidate_id = p.id
                            WHERE m.candidate_id = ?";

                    // Prepare the statement
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['title']) . "</td>
                                    <td>" . htmlspecialchars($row['meeting_time']) . "</td>
                                    <td>" . htmlspecialchars($row['linguistic_rating']) . "</td>
                                    <td>" . htmlspecialchars($row['technical_rating']) . "</td>
                                    <td>" . htmlspecialchars($row['managerial_rating']) . "</td>
                                    <td>" . htmlspecialchars($row['transversal_rating']) . "</td>
                                    <td>" . htmlspecialchars($row['comment']) . "</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>Aucun entretien trouvé.</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
