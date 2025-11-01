<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user by email
    $stmt = $conn->prepare("SELECT id, name, password, user_type FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $hashed_password, $user_type);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['user_type'] = strtolower(trim($user_type));
             $stmt->close(); 
             $conn->close();
            header("Location: dashboard-router.php");
            exit;
        } else {
             $stmt->close(); 
             $conn->close();
            header("Location: login.php?error=invalid");
            exit;
        }

    } else {
        $stmt->close(); 
        $conn->close();
        header("Location: login.php?error=notfound");
        exit;
    }

   
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Eventify</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat%20Alternates:700|Montserrat%20Alternates:400" rel="stylesheet" />
  <link rel="stylesheet" href="login-styles.css" />
</head>
<body>
  <div class="login-page">
    <h1>Login to Eventify</h1>
    <form id="loginForm"  method="POST" autocomplete="off">
      <label for="email">Email address</label>
      <input type="email" id="email" name="email" placeholder="you@example.com" required autocomplete="email" />
      
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password" />

      <button type="submit" class="btn">
        Login <span class="arrow">→</span>
      </button>
    </form>

    <div class="footer-link">
      <a href="signup.html">Don't have an account? Sign up</a>
    </div>
  </div>
  <div id="errorModal" class="modal">
  <div class="modal-content">
    <p id="errorMessage">Error</p>
    <button onclick="closeModal()">OK </button>
  </div>
</div>

<script>
function closeModal() {
  document.getElementById("errorModal").style.display = "none";
}

window.addEventListener("DOMContentLoaded", function () {
  const params = new URLSearchParams(window.location.search);
  const error = params.get("error");
  if (error) {
    const modal = document.getElementById("errorModal");
    const message = document.getElementById("errorMessage");

    if (error === "invalid") {
      message.textContent = "Incorrect password. Please try again.";
    } else if (error === "notfound") {
      message.textContent = "No user found with this email address.";
    } else {
      message.textContent = "Login failed. Please try again.";
    }

    modal.style.display = "flex";
  }
});

window.addEventListener("click", function(e) {
  const modal = document.getElementById("errorModal");
  if (e.target === modal) {
    closeModal();
  }
});

</script>

</body>
</html>
