<?php
// Include the database connection
$conn = new mysqli('localhost', 'root', '', 'hp_tunisia_jobs_db');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the 'id' is set in the URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // First, check if the user exists
    $sql_check = "SELECT * FROM users WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Delete the associated candidate record if it exists
        $sql_delete_candidate = "DELETE FROM candidates WHERE id = ?";
        $stmt_delete_candidate = $conn->prepare($sql_delete_candidate);
        $stmt_delete_candidate->bind_param("i", $user_id);
        $stmt_delete_candidate->execute();
        $stmt_delete_candidate->close();

        // Now, delete the user from the users table
        $sql_delete_user = "DELETE FROM users WHERE id = ?";
        $stmt_delete_user = $conn->prepare($sql_delete_user);
        $stmt_delete_user->bind_param("i", $user_id);

        if ($stmt_delete_user->execute()) {
            // Redirect back to the user list after deletion
            header('Location: admin.php');  // Adjust the location as needed
            exit();
        } else {
            echo "Error deleting user.";
        }

        $stmt_delete_user->close();
    } else {
        echo "User not found.";
    }

    $stmt_check->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
