<?php 
// Include the database connection
$pdo = new PDO('mysql:host=localhost;dbname=hp_tunisia_jobs_db', 'root', ''); // Update with your DB credentials

// Fetch images for the carousel
$query = "SELECT image_path FROM images WHERE section_name = 'slideshow' ORDER BY created_at";
$stmt = $pdo->query($query);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
$imageCount = count($images);

// Fetch content for sections (Accueil, About, etc.)
$sectionsQuery = "SELECT section_name, content FROM site_content WHERE section_name IN ('welcome', 'about_us', 'recruitment', 'contact')";
$sectionsStmt = $pdo->query($sectionsQuery);
$sections = $sectionsStmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HP Tunisie - Accueil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* General Body and Layout Styling */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        /* Sticky Navbar */
        .navbar {
            display: flex;
            justify-content: center; 
            align-items: center;
            background-color: #333; /* Adjust navbar background */
            padding: 10px 20px;
            background-color: #2c3e50;

            text-align: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar a {
            color: white;
            margin: 0 20px;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 16px;
            letter-spacing: 1px;
            transition: color 0.3s ease;
        }
        .navbar a:hover {
            color: #3498db;
            border-radius: 5px; 
        }

        /* Carousel Section */
        .carousel {
            width: 100%;
            height: 500px;
            overflow: hidden;
            position: relative;
        }
        .carousel-images {
            display: flex;
            transition: transform 1s ease-in-out;
        }
        .carousel-images img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Navigation Buttons */
        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 0 10px;
            z-index: 10;
        }
        .carousel-nav button {
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px;
            font-size: 20px;
            cursor: pointer;
            border-radius: 50%;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }
        .carousel-nav button:hover {
            opacity: 1;
        }

        /* Sections Styling */
        .section {
            padding: 40px 20px;
            margin: 30px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 85%;
            max-width: 1200px;
        }
        .section h2 {
            text-align: center;
            color: #34495e;
            font-size: 28px;
            margin-bottom: 20px;
        }
        .section p {
            font-size: 18px;
            line-height: 1.6;
            text-align: justify;
        }

        /* Position Descriptions Styling */
        #position-descriptions {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .position-description {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: box-shadow 0.3s ease;
        }
        .position-description:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        .position-description h3 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .position-description p {
            font-size: 16px;
            color: #7f8c8d;
            line-height: 1.5;
        }

        /* Footer Section Styling */
        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 40px 20px;
            text-align: center;
            margin-top: 60px;
        }
        .footer .footer-content {
            max-width: 1000px;
            margin: 0 auto;
        }
        .footer .hp-logo {
            width: 150px;
            margin-bottom: 20px;
        }
        .footer .contact-info {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }
        .footer .contact-info a {
            color: white;
            margin: 0 20px;
            font-size: 24px;
            text-decoration: none;
            transition: opacity 0.3s ease;
        }
        .footer .contact-info a:hover {
            opacity: 0.7;
        }
        .footer p {
            margin-top: 20px;
            font-size: 16px;
            text-align: center;
        }

        /* Button Styling */
        .button {
            background-color: #3498db;
            color: white;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-block;
            margin: 10px 5px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #2980b9;
            transform: translateY(-3px);
        }

        /* Responsive Design for Smaller Screens */
        @media (max-width: 768px) {
            .carousel-images img {
                height: 300px;
            }
            .section {
                padding: 30px 15px;
                width: 95%;
            }
            .footer .contact-info {
                justify-content: space-evenly;
            }
            .footer .contact-info a {
                margin: 0 10px;
            }
            #position-descriptions {
                grid-template-columns: 1fr 1fr;
            }
            .button {
                font-size: 16px;
                padding: 12px 25px;
            }
        }

        .button:active {
            background-color: #1c6691;
            transform: translateY(2px);
        }

        .auth-buttons {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        /* Additional Styling for Buttons Container */
        .auth-buttons a {
            text-align: center;
        }


        .navbar .hp-logo {
            height: 40px; /* Adjust logo height */
            width: auto;  /* Maintain aspect ratio */
            margin-right: 20px; /* Adjust space between logo and links */

        }

    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a><img src="img/HP_logo.png" alt="HP Tunisie Logo" class="hp-logo"></a>
        <a href="#accueil">Accueil</a>
        <a href="#about">À propos de nous</a>
        <a href="#recruitment">Recrutement</a>
        <a href="#contact">Contact</a>
    </div>

    <!-- Carousel Section -->
    <div class="carousel">
        <div class="carousel-images" id="carouselImages">
            <!-- Clone the last image at the start for seamless looping -->
            <?php if (!empty($images)): ?>
                <img src="<?php echo $images[$imageCount - 1]['image_path']; ?>" alt="Cloned Image">
            <?php endif; ?>

            <?php foreach ($images as $image): ?>
                <img src="<?php echo $image['image_path']; ?>" alt="Slideshow Image">
            <?php endforeach; ?>

            <!-- Clone the first image at the end for seamless looping -->
            <?php if (!empty($images)): ?>
                <img src="<?php echo $images[0]['image_path']; ?>" alt="Cloned Image">
            <?php endif; ?>
        </div>

        <!-- Left and Right Navigation Buttons -->
        <div class="carousel-nav">
            <button id="prevBtn">&#10094;</button>
            <button id="nextBtn">&#10095;</button>
        </div>
    </div>

    <!-- Sections (Accueil, About, etc.) -->
    <div class="section" id="accueil">
        <h2>Bienvenue sur notre plateforme</h2>
        <p>
            <?php 
                foreach ($sections as $section) {
                    if ($section['section_name'] == 'welcome') {
                        echo $section['content'];
                    }
                }
            ?>
        </p>
    </div>

    <div class="section" id="about">
        <h2>À propos de nous</h2>
        <p>
            <?php 
                foreach ($sections as $section) {
                    if ($section['section_name'] == 'about_us') {
                        echo $section['content'];
                    }
                }
            ?>
        </p>
    </div>

    <div class="section" id="recruitment">
        <h2>Recrutement</h2>
        <p>
            <?php 
                foreach ($sections as $section) {
                    if ($section['section_name'] == 'recruitment') {
                        echo $section['content'];
                    }
                }
            ?>
        </p>
        
        <div id="position-descriptions">
            <?php
            // Fetch available positions with descriptions
            $conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT * FROM positions";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='position-description'>
                            <h3>{$row['title']}</h3>
                            <p>{$row['description']}</p>
                          </div>";
                }
            }
            $conn->close();
            ?>
        </div>
        <div class="auth-buttons">
            <a href="create_account.php" class="button">Créer un compte</a>
            <a href="login.php" class="button">Se connecter</a>
        </div>
    </div>

    <!-- Footer Section -->
    <div class="footer" id="contact">
        <div class="footer-content">
            <img src="img/HP_logo.png" alt="HP Tunisie Logo" class="hp-logo">
            <div class="contact-info">
                <a href="tel:+21655555555" title="Numéro de Téléphone"><i class="fas fa-phone"></i></a>
                <a href="#" title="Email"><i class="fas fa-envelope"></i></a>
                <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="https://twitter.com/HPTunisie" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
            </div>
            <p>
                <?php 
                    foreach ($sections as $section) {
                        if ($section['section_name'] == 'contact') {
                            echo $section['content'];
                        }
                    }
                ?>
            </p>
            <p>&copy; 2024 HP Tunisie. Tous droits réservés.</p>
        </div>
    </div>

    <script>
        let currentIndex = 1;
        const images = document.querySelectorAll('.carousel-images img');
        const totalImages = images.length - 2; 

        const carouselImagesContainer = document.getElementById('carouselImages');

        setInterval(() => {
            if (currentIndex < totalImages) {
                currentIndex++;
                carouselImagesContainer.style.transition = 'transform 1s ease-in-out';
                carouselImagesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
            } else {
                setTimeout(() => {
                    carouselImagesContainer.style.transition = 'none';
                    carouselImagesContainer.style.transform = `translateX(-100%)`;
                    currentIndex = 1;
                }, 1000);
            }
        }, 3000);

        document.getElementById('nextBtn').addEventListener('click', () => {
            if (currentIndex < totalImages - 1) {
                currentIndex++;
                carouselImagesContainer.style.transition = 'transform 1s ease-in-out';
                carouselImagesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
            }
        });

        document.getElementById('prevBtn').addEventListener('click', () => {
            if (currentIndex > 1) {
                currentIndex--;
                carouselImagesContainer.style.transition = 'transform 1s ease-in-out';
                carouselImagesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
            }
        });
    </script>

</body>
</html>
