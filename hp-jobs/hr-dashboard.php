<?php 
session_start(); // Start the session

// Check if the HR user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Get the HR user ID from the session
$hr_user_id = $_SESSION['user_id'];

// Database connection
$host = 'localhost';
$db = 'hp_tunisia_jobs_db';
$user = 'root'; // Replace with your DB username
$pass = ''; // Replace with your DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT firstname, lastname FROM users WHERE id = :hr_user_id AND role = 'hr'");
    $stmt->execute([':hr_user_id' => $hr_user_id]);
    $hr_user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Default sorting criteria
    $sort_by = 'lastname'; // Default sort by last name
    if (isset($_GET['sort_by']) && in_array($_GET['sort_by'], ['firstname', 'lastname'])) {
        $sort_by = $_GET['sort_by'];
    }

    // Base query with sorting
    $query = "SELECT u.id, u.firstname, u.lastname, u.email, c.phone, c.address, c.profile_picture, c.cv_path, c.motivation_letter_path, p.title AS position_title
              FROM users u
              JOIN candidates c ON u.id = c.id
              LEFT JOIN positions p ON c.position_id = p.id
              WHERE u.role = 'candidate'";

    // Apply filtering by position if selected
    // Apply filtering by position if selected
    $params = [];
    if (isset($_GET['position_id']) && $_GET['position_id'] !== '') {
        $position_id = $_GET['position_id'];
        $query .= " AND c.position_id = :position_id";
        $params[':position_id'] = $position_id;
    }

    // Apply search filter if provided
    if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
        $search_query = '%' . $_GET['search_query'] . '%';
        $query .= " AND (u.firstname LIKE :search_query OR u.lastname LIKE :search_query)";
        $params[':search_query'] = $search_query;
    }

    $query .= " ORDER BY u.$sort_by ASC"; // Apply sorting
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error connecting to database: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>HR Dashboard</title>
    <link rel="stylesheet" href="rh_style_.css">
    <link rel="stylesheet" href="sidebar_style.css">

    <script>
        function showModal(candidate) {
            document.getElementById('modal').style.display = 'block';
            document.getElementById('candidate_name').innerText = candidate.firstname + ' ' + candidate.lastname;
            document.getElementById('candidate_phone').innerText = 'Phone: ' + candidate.phone;
            document.getElementById('candidate_address').innerText = 'Address: ' + candidate.address;
            document.getElementById('candidate_id').value = candidate.id;
            document.getElementById('candidate_id_rating').value = candidate.id; // Add this line

                // Display profile picture
            document.getElementById('candidate_profile_picture').src = candidate.profile_picture;
            document.getElementById('candidate_cv_link').href = candidate.cv_path;
            document.getElementById('candidate_motivation_letter_link').href = candidate.motivation_letter_path;
            // Fetch the meetings of the selected candidate
            fetchMeetings(candidate.id);
        }


        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        function fetchMeetings(candidateId) {
            // Fetch meetings for the selected candidate
            fetch('get_meetings.php?candidate_id=' + candidateId)
                .then(response => response.json())
                .then(data => {
                    const meetingSelect = document.getElementById('meeting_select');
                    meetingSelect.innerHTML = '<option value="">Select a meeting</option>';
                    data.forEach(meeting => {
                        const option = document.createElement('option');
                        option.value = meeting.meeting_time + '_' + meeting.hr_user_id; // Combination of meeting_time and hr_user_id
                        option.text = 'Meeting at ' + meeting.meeting_time + ' (with HR ' + meeting.hr_user_id + ')';
                        meetingSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching meetings:', error));
        }

    </script>
</head>
<body>    
    <div class="sidebar">
        <h2>Tableau de bord RH</h2>
        <nav class="sidebar-nav">
            <div class="search-bar">
                <form method="GET" action="hr-dashboard.php">
                    <label for="search-query">Rechercher par nom :</label>
                    <input type="text" id="search-query" name="search_query" placeholder="Nom du candidat...">
                    <button type="submit">Rechercher</button>
                </form>
            </div>
            <div class="filters">
                <form method="GET" action="hr-dashboard.php">
                    <label for="position-filter">Filtrer par poste :</label>
                    <select id="position-filter" name="position_id">
                        <option value="">Tous les postes</option>
                        <?php
                        $conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');
                        $sql = "SELECT id, title FROM positions";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
                            }
                        }
                        $conn->close();
                        ?>
                    </select>

                    <label for="sort-by">Trier par :</label>
                    <select id="sort-by" name="sort_by">
                        <option value="lastname" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'lastname' ? 'selected' : ''; ?>>Nom</option>
                        <option value="firstname" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'firstname' ? 'selected' : ''; ?>>Prénom</option>
                    </select>

                    <button type="submit">Appliquer</button>
                </form>
            </div>
            <a href="login.php" class="logout">Déconnexion</a>
        </nav>
    </div>
        
    <div class="main-content">
        <h1>Bienvenue, <?php echo $hr_user['firstname'] . ' ' . $hr_user['lastname']; ?></h1>
        <div id="candidates-section">
            <h2>Candidats :</h2>
            <?php if ($candidates): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Adresse</th>
                            <th>Poste</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($candidates as $candidate): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($candidate['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($candidate['firstname']); ?></td>
                                <td><?php echo htmlspecialchars($candidate['email']); ?></td>
                                <td><?php echo htmlspecialchars($candidate['phone']); ?></td>
                                <td><?php echo htmlspecialchars($candidate['address']); ?></td>
                                <td><?php echo htmlspecialchars($candidate['position_title'] ?? 'N/A'); ?></td>
                                <td>
                                    <button onclick='showModal(<?php echo json_encode($candidate); ?>)'>Voir Détails</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No candidates found.</p>
            <?php endif; ?>
        </div>
        <!-- Modal -->
        <div id="modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Candidate Information</h2>
                <p id="candidate_name"></p>
                <p id="candidate_phone"></p>
                <p id="candidate_address"></p>

                <!-- Display Profile Picture -->
                <img id="candidate_profile_picture" src="" alt="Profile Picture" style="max-width: 200px; display: block; margin-bottom: 15px;">

                <!-- Display CV and Motivation Letter Links -->
                <p><a id="candidate_cv_link" href="" target="_blank">Download CV</a></p>
                <p><a id="candidate_motivation_letter_link" href="" target="_blank">Download Motivation Letter</a></p>

                <!-- Schedule a Meeting Form -->
                <h2>Schedule a Meeting</h2>
                <form method="post" action="">
                    <input type="hidden" name="candidate_id" id="candidate_id" value="">
                    
                    <label for="meeting_time">Meeting Time:</label>
                    <input type="datetime-local" name="meeting_time" id="meeting_time" required>
                    
                    <label for="nature">Nature of Meeting:</label>
                    <select name="nature" id="nature" required>
                        <option value="en ligne">En ligne</option>
                        <option value="en personne">En personne</option>
                    </select>

                    <label for="comment">Comment:</label>
                    <textarea name="comment" id="comment"></textarea>

                    <button type="submit" name="schedule_meeting" class="form-button">Schedule Meeting</button>
                </form>

                <!-- Rating Section -->
                <h2>Rate the Meeting</h2>
                <form method="post" action="">
                    <input type="hidden" name="candidate_id" id="candidate_id_rating" value="">
                    
                    <label for="meeting_select">Select a Meeting:</label>
                    <select name="meeting_id" id="meeting_select" required>
                        <!-- Options will be populated by JavaScript -->
                    </select>

                    <label for="linguistic_rating">Linguistic Rating:</label>
                    <input type="number" name="linguistic_rating" min="0" max="10" required>

                    <label for="technical_rating">Technical Rating:</label>
                    <input type="number" name="technical_rating" min="0" max="10" required>

                    <label for="managerial_rating">Managerial Rating:</label>
                    <input type="number" name="managerial_rating" min="0" max="10" required>

                    <label for="transversal_rating">Transversal Rating:</label>
                    <input type="number" name="transversal_rating" min="0" max="10" required>

                    <button type="submit" name="rate_meeting" class="form-button">Submit Ratings</button>
                </form>
            </div>
        </div>
    </div>

    <?php
    if (isset($_POST['schedule_meeting'])) {
        $candidate_id = $_POST['candidate_id'];
        $meeting_time = $_POST['meeting_time'];
        $nature = $_POST['nature'];
        $comment = $_POST['comment'];

        try {
            $stmt = $pdo->prepare("INSERT INTO meetings (candidate_id, hr_user_id, meeting_time, nature, comment) 
                                   VALUES (:candidate_id, :hr_user_id, :meeting_time, :nature, :comment)");
            $stmt->execute([
                ':candidate_id' => $candidate_id,
                ':hr_user_id' => $hr_user_id,
                ':meeting_time' => $meeting_time,
                ':nature' => $nature,
                ':comment' => $comment
            ]);

            echo "<p>Meeting successfully scheduled with candidate ID $candidate_id.</p>";
        } catch (PDOException $e) {
            echo "<p>Error scheduling meeting: " . $e->getMessage() . "</p>";
        }
    }

    if (isset($_POST['rate_meeting'])) {
        // Retrieve the candidate_id from the form
        $candidate_id = $_POST['candidate_id'];
        // Check if meeting_id is provided
        if (isset($_POST['meeting_id'])) {
            // Extract meeting_time and hr_user_id from the meeting_id
            list($meeting_time, $hr_user_id) = explode('_', $_POST['meeting_id']);
            
            // Retrieve the ratings from the form
            $linguistic_rating = $_POST['linguistic_rating'];
            $technical_rating = $_POST['technical_rating'];
            $managerial_rating = $_POST['managerial_rating'];
            $transversal_rating = $_POST['transversal_rating'];
    
            // Debugging: Log the values to check if they are being passed correctly
            echo "Candidate ID: $candidate_id <br>";
            echo "Meeting Time: $meeting_time <br>";
            echo "HR User ID: $hr_user_id <br>";
            echo "Linguistic Rating: $linguistic_rating <br>";
            echo "Technical Rating: $technical_rating <br>";
            echo "Managerial Rating: $managerial_rating <br>";
            echo "Transversal Rating: $transversal_rating <br>";
    
            try {
                // Prepare the UPDATE query to update the meeting's ratings
                $stmt = $pdo->prepare("UPDATE meetings 
                                       SET linguistic_rating = :linguistic_rating, 
                                           technical_rating = :technical_rating, 
                                           managerial_rating = :managerial_rating, 
                                           transversal_rating = :transversal_rating
                                       WHERE candidate_id = :candidate_id 
                                       AND hr_user_id = :hr_user_id
                                       AND meeting_time = :meeting_time");
    
                // Execute the query
                $stmt->execute([
                    ':linguistic_rating' => $linguistic_rating,
                    ':technical_rating' => $technical_rating,
                    ':managerial_rating' => $managerial_rating,
                    ':transversal_rating' => $transversal_rating,
                    ':candidate_id' => $candidate_id,
                    ':hr_user_id' => $hr_user_id,
                    ':meeting_time' => $meeting_time
                ]);
    
                // Check if the update was successful
                if ($stmt->rowCount() > 0) {
                    echo "<p>Meeting rated successfully.</p>";
                } else {
                    echo "<p>No changes were made. Please check if the meeting exists in the database.</p>";
                }
            } catch (PDOException $e) {
                echo "<p>Error rating meeting: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p>No meeting selected for rating.</p>";
        }
    }
    
    ?>
</body>

</html>
