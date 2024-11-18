<?php
// Start output buffering
ob_start();

// Start session
session_start();

try {
    // Connect to database using PDO
    $pdo = new PDO('mysql:host=localhost;dbname=hp_tunisia_jobs_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch images for the carousel
    $query = "SELECT image_path FROM images WHERE section_name = 'slideshow' ORDER BY created_at";
    $stmt = $pdo->query($query);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $imageCount = count($images);

    // Fetch content for sections (Accueil, About, etc.)
    $sectionsQuery = "SELECT section_name, content FROM site_content WHERE section_name IN ('welcome', 'about_us', 'recruitment', 'contact')";
    $sectionsStmt = $pdo->query($sectionsQuery);
    $sections = $sectionsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle connection error
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Tableau de bord Admin - HP Tunisie</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="sidebar_style.css">

    <script>
        function showSection(sectionId) {
            var sections = document.getElementsByClassName('section');
            for (var i = 0; i < sections.length; i++) {
                sections[i].classList.remove('active');
            }
            document.getElementById(sectionId).classList.add('active');
        }

        window.onload = function() {
            showSection('manage-users-section'); // Default section
        };
    </script>

</head>
<body>
    <div class="sidebar">
        <h2 class="sidebar-title">Tableau de bord Admin</h2>
        <nav class="sidebar-nav">

            <a class="sidebar-link modify-jobs" onclick="showSection('manage-jobs-section')">Gérer les emplois</a>
            <a class="sidebar-link modify-users" onclick="showSection('manage-users-section')">Gérer les utilisateurs</a>
            <div class="sidebar-section modify-index">
                <h4 class="sidebar-subtitle">Ajouter un utilisateur</h4>
                <a class="sidebar-sublink" onclick="showSection('add-admin-section')">Admin</a>
                <a class="sidebar-sublink" onclick="showSection('add-hr-section')">RH</a>
                <a class="sidebar-sublink" onclick="showSection('add-candidate-section')">candidat</a>
            </div>
            <div class="sidebar-section modify-index">
                <h4 class="sidebar-subtitle">Modifier page Acceuil</h4>
                <a class="sidebar-sublink" onclick="showSection('modify-welcome-section')">Bienvenue</a>
                <a class="sidebar-sublink" onclick="showSection('modify-about-section')">Modifier À propos</a>
                <a class="sidebar-sublink" onclick="showSection('modify-recruitment-section')">Recrutement</a>
                <a class="sidebar-sublink" onclick="showSection('modify-contact-section')">Modifier Contact</a>
            </div>
           
            <a href="login.php" class="sidebar-link disconnect-btn">Déconnexion</a>
        </nav>
    </div>


    <div class="main-content">

        <!-- Section to manage users -->
        <div id="manage-users-section" class="section">
            <h1>Gérer les utilisateurs</h1>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch users from the database
                    $conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');
                    $sql = "SELECT id, firstname, lastname, email, role FROM users";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['firstname']} {$row['lastname']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['role']}</td>
                                    <td>
                                        <button onclick=\"window.location.href='modify-user.php?id={$row['id']}'\">Modifier</button>
                                        <button onclick=\"if(confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) window.location.href='delete_user.php?id={$row['id']}';\">Supprimer</button>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Aucun utilisateur trouvé.</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>

            </table>
        </div>

        <!-- Section to manage jobs -->
        <div id="manage-jobs-section" class="section"> 
            <h1>Gérer les emplois</h1>

            <!-- Section to add job category -->
            <div id="add-category" class="add-category-section">
                <h2 class="section-title">Ajouter une catégorie d'emploi</h2>
                <form method="POST" action="add_category.php" class="category-form">
                    <label for="category" class="form-label">Catégorie:</label>
                    <input type="text" id="category" name="category" class="form-input" required>
                    
                    <label for="description" class="form-label">Description:</label>
                    <textarea id="description" name="description" class="form-textarea" required></textarea>
                    
                    <button type="submit" class="submit-button">Ajouter</button>
                </form>
            </div>

            <div class=" jobs-section">
                <table class="jobs-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch jobs from the database
                        $conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');
                        $sql = "SELECT id, title, description FROM positions";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['title']}</td>
                                        <td>{$row['description']}</td>
                                        <td>
                                            <button onclick=\"window.location.href='modify-job.php?id={$row['id']}'\">Modifier</button>
                                            <button onclick=\"if(confirm('Êtes-vous sûr de vouloir supprimer cet emploi ?')) window.location.href='delete_job.php?id={$row['id']}';\">Supprimer</button>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>Aucun emploi trouvé.</td></tr>";
                        }
                        

                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section" id="add-admin-section">
            <h2>Créer un compte administrateur</h2>
            <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
            <form method="POST" action="create_account_admin.php" enctype="multipart/form-data">
                <label for="firstname">Prénom :</label>
                <input type="text" id="firstname" name="firstname" required>

                <label for="lastname">Nom :</label>
                <input type="text" id="lastname" name="lastname" required>

                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Créer un compte</button>
            </form>
        </div>


        <div class="section" id="add-hr-section">
            <h2>Créer un compte RH</h2>
            <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
            <form method="POST" action="create_account_hr.php" enctype="multipart/form-data">
                <label for="firstname">Prénom :</label>
                <input type="text" id="firstname" name="firstname" required>

                <label for="lastname">Nom :</label>
                <input type="text" id="lastname" name="lastname" required>

                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Créer un compte</button>
            </form>
        </div>

        <div class="section" id="add-candidate-section">
            <h2>Créer un compte candidat</h2>
            <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
            <form method="POST" action="create_account_candidate.php" enctype="multipart/form-data">
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
        </div>



        <!-- Section to modify about info -->
        <div id="modify-welcome-section" class="section">
            <h1>Modifier À propos</h1>
            <form method="POST" action="update_welcome.php">
                <label for="welcome">Contenu Actuel:</label>
                <p>
                    <?php 
                        foreach ($sections as $section) {
                            if ($section['section_name'] == 'welcome') {
                                echo $section['content'];
                            }
                        }
                    ?>
                </p>
                <label for="recruitment">Nouveau Contenu:</label>
                <textarea id="welcome" name="welcome" required></textarea>
                <button type="submit">Mettre à jour</button>
            </form>
        </div>


        <!-- Section to modify about info -->
        <div id="modify-about-section" class="section">
            <h1>À propos</h1>
            <form method="POST" action="update_about.php">
                <label for="about">Contenu Actuel:</label>
                <p>
                    <?php 
                        foreach ($sections as $section) {
                            if ($section['section_name'] == 'about_us') {
                                echo $section['content'];
                            }
                        }
                    ?>
                </p>
                <label for="recruitment">Nouveau Contenu:</label>
                <textarea id="about" name="about" required></textarea>
                <button type="submit">Mettre à jour</button>
            </form>
        </div>


        <!-- Section to modify recruitment info -->
        <div id="modify-recruitment-section" class="section">
            <h1>Recrutement Introduction</h1>
            <form method="POST" action="update_recrutment.php">
                <label for="recruitment">Contenu Actuel:</label>
                <p>
                    <?php 
                        foreach ($sections as $section) {
                            if ($section['section_name'] == 'recruitment') {
                                echo $section['content'];
                            }
                        }
                    ?>
                </p>
                <label for="recruitment">Nouveau Contenu:</label>
                <textarea id="recruitment" name="recruitment" required></textarea>
                <button type="submit">Mettre à jour</button>
            </form>
        </div>



        <!-- Section to modify contact info -->
        <div id="modify-contact-section" class="section">
            <h1>Contact</h1>
            <form method="POST" action="update_contact.php">
                <label for="contact">Contenu Actuel::</label>
                <p>
                    <?php 
                        foreach ($sections as $section) {
                            if ($section['section_name'] == 'contact') {
                                echo $section['content'];
                            }
                        }
                    ?>
                </p>
                <label for="recruitment">Nouveau Contenu:</label>
                <textarea id="contact" name="contact" required></textarea>
                <button type="submit">Mettre à jour</button>
            </form>
        </div>



    </div>
</body>
</html>

<?php
// End output buffering
ob_end_flush();
?>
