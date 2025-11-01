<?php
include 'db.php'; // Your DB connection
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Match field names from your HTML form
    $name = $_POST['fullname']; // Matches name="fullname"
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_type = $_POST['accountType']; // Matches name="accountType"

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $user_type);

if ($stmt->execute()) {
        // Optionally store user info in session
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['name'] = $name;
        $_SESSION['user_type'] = strtolower(trim($user_type));


        // Redirect to router page
        header("Location: dashboard-router.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
