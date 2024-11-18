<?php
session_start();
$user_id = $_SESSION['user_id'];

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch existing file paths
$sql = "SELECT profile_picture, cv_path, motivation_letter_path FROM candidates WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$existing_files = $result->fetch_assoc();

$profile_picture = $existing_files['profile_picture'];
$cv_path = $existing_files['cv_path'];
$motivation_letter_path = $existing_files['motivation_letter_path'];

// Check if new files are uploaded
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $profile_picture = 'uploads/' . basename($_FILES['profile_picture']['name']);
    move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
}

if (isset($_FILES['cv_path']) && $_FILES['cv_path']['error'] === UPLOAD_ERR_OK) {
    $cv_path = 'uploads/' . basename($_FILES['cv_path']['name']);
    move_uploaded_file($_FILES['cv_path']['tmp_name'], $cv_path);
}

if (isset($_FILES['motivation_letter_path']) && $_FILES['motivation_letter_path']['error'] === UPLOAD_ERR_OK) {
    $motivation_letter_path = 'uploads/' . basename($_FILES['motivation_letter_path']['name']);
    move_uploaded_file($_FILES['motivation_letter_path']['tmp_name'], $motivation_letter_path);
}

// Update user profile in the database
$sql = "UPDATE users u
        JOIN candidates c ON u.id = c.id
        SET u.firstname = ?, u.lastname = ?, u.email = ?, c.phone = ?, c.address = ?, 
            c.profile_picture = ?, c.cv_path = ?, c.motivation_letter_path = ?
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssssssi",
    $_POST['firstname'],
    $_POST['lastname'],
    $_POST['email'],
    $_POST['phone'],
    $_POST['address'],
    $profile_picture,
    $cv_path,
    $motivation_letter_path,
    $user_id
);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "<script>alert('Profile updated successfully!'); window.location.href='candidate-dashboard.php';</script>";
} else {
    echo "<script>alert('No changes were made.'); window.location.href='candidate-dashboard.php';</script>";
}

$stmt->close();
$conn->close();
?>
