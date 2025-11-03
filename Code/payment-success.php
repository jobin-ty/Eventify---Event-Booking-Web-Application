<?php
session_start();

$ref = $_GET['ref'] ?? null;
if (!$ref) {
    die("Invalid access. No payment reference found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Payment Successful | Eventify</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css?family=Montserrat+Alternates:700|Montserrat+Alternates:400" rel="stylesheet" />
  <style>
    :root {
      --primary: #2B3A8C;
      --success: #2e7d32;
      --bg: #f9f9fb;
      --text: #161d18;
      --white: #ffffff;
    }
    body {
      font-family: 'Montserrat Alternates', sans-serif;
      background: var(--bg);
      color: var(--text);
      margin: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      min-height: 100vh;
      padding: 0 2rem 2rem 2rem;
    }
    header {
      background: var(--primary);
      width: 100%;
      padding: 1rem 2rem;
      color: var(--white);
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 0 0 2rem 2rem;
    }
    header h1 {
      font-size: 1.75rem;
      font-weight: 900;
    }
    nav a {
      color: var(--white);
      text-decoration: none;
      margin-left: 1.5rem;
      font-weight: 600;
    }
    .success-container {
      background: var(--white);
      padding: 3rem 2rem;
      max-width: 500px;
      margin-top: 4rem;
      text-align: center;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
      border-radius: 12px;
    }
    .success-container h2 {
      color: var(--success);
      margin-bottom: 1rem;
    }
    .success-container p {
      margin: 0.75rem 0;
    }
    .payment-ref {
      font-weight: bold;
      font-size: 1.1rem;
      color: var(--primary);
    }
    .actions {
      margin-top: 2rem;
    }
    .actions a {
      text-decoration: none;
      color: var(--white);
      background: var(--primary);
      padding: 0.75rem 1.5rem;
      border-radius: 30px;
      margin: 0 0.5rem;
      display: inline-block;
      font-weight: 700;
      transition: background 0.3s ease;
    }
    .actions a:hover {
      background: #1c2d6b;
    }
  </style>
</head>
<body>
  <header>
    <h1>Eventify</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="browse.php">Browse Events</a>
      <a href="dashboard-router.php">Dashboard</a>
    </nav>
  </header>

  <div class="success-container">
    <h2>🎉 Payment Successful!</h2>
    <p>Thank you for booking with <strong>Eventify</strong>.</p>
    <p>Your payment reference:</p>
    <p class="payment-ref"><?= htmlspecialchars($ref) ?></p>

    <div class="actions">
      <a href="browse.php">Browse More Events</a>
      <a href="dashboard-router.php">Go to Dashboard</a>
    </div>
  </div>
</body>
</html>
