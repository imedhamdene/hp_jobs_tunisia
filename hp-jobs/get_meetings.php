<?php
// get_meetings.php
header('Content-Type: application/json');

if (isset($_GET['candidate_id'])) {
    $candidate_id = $_GET['candidate_id'];

    // Database connection
    $pdo = new PDO("mysql:host=localhost;dbname=hp_tunisia_jobs_db", "root", "");

    $stmt = $pdo->prepare("SELECT * FROM meetings WHERE candidate_id = :candidate_id");
    $stmt->execute([':candidate_id' => $candidate_id]);

    $meetings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($meetings);
} else {
    echo json_encode([]);
}
