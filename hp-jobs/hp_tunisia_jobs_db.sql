-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2024 at 12:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hp_tunisia_jobs_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `cv_path` varchar(255) DEFAULT NULL,
  `motivation_letter_path` varchar(255) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `section_name`, `image_path`, `created_at`) VALUES
(1, 'slideshow', 'img/img1.webp', '2024-11-05 01:21:01'),
(2, 'slideshow', 'img/img2.webp', '2024-11-05 01:21:01'),
(3, 'slideshow', 'img/img3.png', '2024-11-05 01:21:01'),
(4, 'slideshow', 'img/img4.png', '2024-11-17 11:09:42'),
(5, 'slideshow', 'img/img5.png', '2024-11-18 23:07:36'),
(6, 'slideshow', 'img/img6.png', '2024-11-18 23:07:54');

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `candidate_id` int(11) NOT NULL,
  `hr_user_id` int(11) NOT NULL,
  `meeting_time` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `linguistic_rating` int(11) DEFAULT NULL CHECK (`linguistic_rating` between 0 and 10),
  `technical_rating` int(11) DEFAULT NULL CHECK (`technical_rating` between 0 and 10),
  `managerial_rating` int(11) DEFAULT NULL CHECK (`managerial_rating` between 0 and 10),
  `transversal_rating` int(11) DEFAULT NULL CHECK (`transversal_rating` between 0 and 10),
  `nature` enum('en ligne','en personne') NOT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(2000) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `title`, `description`, `created_at`) VALUES
(1, 'Candidature Spontanée', 'La Candidature Spontanée chez HP Tunisie offre l’opportunité aux talents passionnés de rejoindre notre équipe, même si aucun poste spécifique n\'est actuellement ouvert. En tant que candidat spontané, vous pouvez soumettre votre profil pour être pris en compte pour toute opportunité future qui correspond à vos compétences et à vos aspirations professionnelles. HP Tunisie valorise l\'initiative et l\'innovation, et nous sommes toujours à la recherche de personnes talentueuses pour renforcer nos équipes dans différents domaines. Cette candidature permet de constituer un vivier de talents et d’explorer des opportunités dès qu’elles se présentent.', '2024-11-09 21:40:21'),
(2, 'Ingénieur Logiciel', 'L’Ingénieur Logiciel chez HP Tunisie est responsable de la conception, du développement et de l’optimisation des applications logicielles innovantes destinées à améliorer les solutions technologiques de l\'entreprise. Vous travaillerez en étroite collaboration avec les équipes de développement, d’architecture et de gestion de produits pour créer des logiciels robustes, performants et évolutifs. Ce poste nécessite une expertise technique approfondie en programmation, une bonne maîtrise des méthodologies agiles et la capacité à résoudre des problèmes complexes. L\'Ingénieur Logiciel contribuera activement à l\'évolution des produits et services de HP Tunisie, en s\'assurant que les solutions répondent aux exigences de qualité et de performance.', '2024-11-09 21:40:21'),
(3, 'Chef de Projet', 'En tant que Chef de Projet chez HP Tunisie, vous superviserez la planification, l\'exécution et la livraison de projets complexes au sein de l\'organisation. Vous travaillerez en étroite collaboration avec les parties prenantes pour définir les objectifs du projet, allouer les ressources, gérer les délais et assurer le succès des projets. Ce poste nécessite d\'excellentes compétences en leadership, organisation et communication, ainsi qu\'une expérience en méthodologies de gestion de projet.', '2024-11-09 21:40:21'),
(4, 'Coordinateur Marketing', 'Le Coordinateur Marketing chez HP Tunisie est chargé de soutenir l\'équipe marketing dans l\'exécution des campagnes, la gestion des réseaux sociaux et l\'analyse des tendances du marché. Ce poste implique la création de contenu, la coordination d\'événements et la collaboration avec des partenaires externes pour promouvoir les produits et services de HP Tunisie. La créativité, l\'attention aux détails et une passion pour le marketing sont des atouts clés pour ce poste.', '2024-11-09 21:40:21'),
(5, 'Spécialiste en Ressources Humaines', 'Le Spécialiste en Ressources Humaines chez HP Tunisie joue un rôle vital dans la gestion des relations avec les employés, le recrutement et la conformité RH. Vous serez responsable de l\'acquisition de talents, de l\'intégration, de la formation et des programmes de développement des employés. Ce poste nécessite de solides compétences interpersonnelles, une connaissance des pratiques RH et la capacité de gérer des informations sensibles avec confidentialité.', '2024-11-09 21:40:21'),
(6, 'Administrateur Réseau', 'En tant qu\'Administrateur Réseau chez HP Tunisie, vous serez responsable de maintenir l\'intégrité et la sécurité de notre infrastructure réseau. Cela inclut la configuration et le support du matériel réseau, la surveillance des performances du réseau et la mise en œuvre des protocoles de sécurité. Ce poste nécessite une solide formation technique en réseaux, des compétences en résolution de problèmes et la capacité de travailler dans un environnement dynamique.', '2024-11-09 21:40:21'),
(7, 'Analyste de Données', 'L\'Analyste de Données chez HP Tunisie est responsable de la collecte, de l\'analyse et de l\'interprétation des données pour soutenir la prise de décision stratégique. Vous collaborerez avec divers départements pour identifier les besoins en données, créer des rapports et fournir des insights qui orientent la stratégie. Ce poste nécessite la maîtrise des outils d\'analyse de données, une attention particulière aux détails et la capacité de traduire les données en recommandations actionnables.', '2024-11-09 21:40:21'),
(8, 'Candidature Spontanée', 'Chez HP Tunisie, nous sommes toujours à la recherche de talents passionnés et innovants pour rejoindre notre équipe. Si vous ne trouvez pas de poste correspondant à vos compétences parmi nos offres actuelles, nous vous invitons à soumettre une candidature spontanée. Votre profil sera conservé dans notre base de données, et nous vous contacterons si une opportunité correspondant à vos qualifications se présente. Montrez-nous ce que vous pouvez apporter à notre entreprise, et ensemble, nous pourrions construire l\'avenir de la technologie.\r\n', '2024-11-09 21:40:21');

-- --------------------------------------------------------

--
-- Table structure for table `site_content`
--

CREATE TABLE `site_content` (
  `id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_content`
--

INSERT INTO `site_content` (`id`, `section_name`, `content`, `created_at`, `updated_at`) VALUES
(1, 'welcome', 'Bienvenue chez HP Tunisie, où l’innovation et l\'excellence se rencontrent pour offrir des opportunités exceptionnelles. Nous sommes un leader mondial dans le domaine de la technologie, et nous nous engageons à créer un environnement de travail où les talents peuvent s\'épanouir. Rejoignez-nous pour faire partie d\'une équipe passionnée qui transforme l\'avenir de la technologie!', '2024-11-05 01:21:01', '2024-11-18 17:13:11'),
(2, 'about_us', 'HP Tunisie est un acteur majeur dans l’industrie technologique, offrant des solutions de pointe qui propulsent les entreprises et les consommateurs vers un avenir numérique. Notre mission est de fournir des produits innovants tout en créant un environnement de travail inclusif et dynamique pour nos employés. Nous croyons fermement en la formation continue, l\'innovation et l\'engagement envers la durabilité, offrant ainsi à nos employés des opportunités de développement professionnel exceptionnelles. Venez faire partie de notre équipe et contribuer à une vision ambitieuse et à un avenir prometteur!', '2024-11-05 01:21:01', '2024-11-18 17:15:02'),
(3, 'recruitment', 'Nous sommes toujours à la recherche de talents passionnés et motivés pour rejoindre notre équipe. Chez HP Tunisie, nous offrons un environnement de travail dynamique et stimulant où vos idées sont valorisées et où vous pouvez développer vos compétences à travers des projets innovants. Explorez les postes disponibles ci-dessous et rejoignez-nous pour participer à la transformation numérique.', '2024-11-05 01:21:01', '2024-11-17 12:30:26'),
(4, 'contact', 'Nous serions ravis d\'avoir de vos nouvelles! Si vous avez des questions ou souhaitez plus d\'informations sur les opportunités de carrière chez HP Tunisie, n\'hésitez pas à nous contacter  😊', '2024-11-05 01:21:01', '2024-11-18 19:24:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `role` enum('candidate','hr','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `password`, `email`, `role`, `created_at`) VALUES
(2, 'Imed', 'Hamdene', '$2y$10$GRaVKYUyklDdvFZFULlUZ.OJzHBbdDzLjMD026BEZ2yvyDCe6kD5i', 'imed.hamdene@gmail.com', 'hr', '2024-11-09 21:05:03'),
(10, 'Imed', 'HAMDENE', '$2y$10$mToxWMum64thMrGvaW3FsuYsHB6V.f1N2KGQ1iX4mSQm7Fcpd72p.', 'imed@gmail.com', 'admin', '2024-11-17 15:14:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `position_id` (`position_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`candidate_id`,`hr_user_id`,`meeting_time`),
  ADD KEY `hr_user_id` (`hr_user_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_content`
--
ALTER TABLE `site_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `site_content`
--
ALTER TABLE `site_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidates_ibfk_2` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`);

--
-- Constraints for table `meetings`
--
ALTER TABLE `meetings`
  ADD CONSTRAINT `meetings_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meetings_ibfk_2` FOREIGN KEY (`hr_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
