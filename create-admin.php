<?php
require_once 'db.php';

// Set admin credentials
$name = 'Adminjb';
$email = 'adminjb@email.com';
$password = 'adminjb';  // Choose a strong password!
$user_type = 'admin';

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if admin already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "Admin user already exists.";
} else {
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $user_type);

    if ($stmt->execute()) {
        echo "✅ Admin created successfully!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}

$stmt->close();
$conn->close();
?>
