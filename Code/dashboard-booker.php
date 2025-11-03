<?php
session_start();
require_once 'db.php'; // Make sure this contains your DB connection

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ticket_booker') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'])) {
    $cancelId = (int)$_POST['cancel_booking_id'];

    // Optional: Check user owns this booking
    $stmt = $conn->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cancelId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Mark as cancelled (optional status column), or delete booking and payment
        $conn->query("DELETE FROM payments WHERE booking_id = $cancelId");
        $conn->query("DELETE FROM bookings WHERE id = $cancelId");
    }
    $stmt->close();

    // Optional: Refresh page
    header("Location: dashboard-booker.php");
    exit();
}


// Get total bookings
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM bookings WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$totalBookings = $result->fetch_assoc()['total'] ?? 0;
$stmt->close();

// Get total confirmed ticket count
$stmt = $conn->prepare("SELECT SUM(ticket_count) AS total FROM bookings WHERE user_id = ? AND status = 'confirmed'");
$stmt->bind_param("i", $userId); // ✅ Missing in your code
$stmt->execute();                // ✅ Missing in your code
$result = $stmt->get_result();
$totalTickets = $result->fetch_assoc()['total'] ?? 0;
$stmt->close();


// Get recent bookings with event info
$stmt = $conn->prepare("
  SELECT b.id AS booking_id, b.status, e.name AS event_name, e.event_date
  FROM bookings b
  JOIN events e ON b.event_id = e.id
  WHERE b.user_id = ?
  ORDER BY e.event_date DESC
");


$stmt->bind_param("i", $userId);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ticket Booker Dashboard - Eventify</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat%20Alternates:700|Montserrat%20Alternates:400" rel="stylesheet" />
  <link rel="stylesheet" href="dashboard-booker-styles.css" />
</head>
<body>
  <div class="dashboard-booker-page">
     <header>
    <h1>Eventify</h1>
    <nav>
      <a href="index.php" tabindex="1">Home</a>
      <a href="browse.php" tabindex="2">Browse Events</a>

      <!-- Instead of a static href="dashboard.html", we point to our PHP router -->
      <a href="dashboard-router.php" tabindex="3">Dashboard</a>

      <?php if (isset($_SESSION['user_type'])): ?>
        <!-- If a session exists, show Logout instead of Login -->
        <a href="logout.php" tabindex="4">Logout</a>
      <?php endif; ?>
      
    </nav>
  </header>

    <main>
      <section class="hero" aria-label="Dashboard Overview">
        <h1>Welcome back, <?= htmlspecialchars( $user['name']) ?>!</h1>
        <p>Your events and bookings at a glance.</p>
        <a href="browse.php" class="btn-primary" role="button">Browse Events</a>
      </section>

      <section class="dashboard-cards" aria-label="User summary">
        <article class="card" tabindex="0">
          <h3>Total Bookings</h3>
          <p class="metric" aria-live="polite"><?= $totalBookings ?></p>
          <p class="caption">Upcoming and Past</p>
        </article>
  
        <article class="card" tabindex="0">
          <h3>Total Tickets Booked</h3>
            <p class="metric" aria-live="polite"><?= $totalTickets ?></p>
            <p class="caption">Confirmed Tickets</p>
        </article>
      </section>

      <section class="bookings-section" aria-label="List of recent bookings">
        <h2>Your Bookings</h2>
        <ul class="bookings-list">
          <?php if (empty($bookings)): ?>
            <li>No bookings yet.</li>
          <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
              <li class="booking-item" tabindex="0">
                <div class="booking-detail">
                  <span class="booking-event"><?= htmlspecialchars($booking['event_name']) ?></span>
                  <span class="booking-date"><?= date('F j, Y', strtotime($booking['event_date'])) ?></span>
                </div>
               <span class="booking-status <?= $booking['status'] === 'cancelled' ? 'cancelled' : '' ?>">
                  <?= ucfirst($booking['status']) ?>
                </span>

                <?php if ($booking['status'] === 'confirmed'): ?>
                  <form action="cancel-confirm.php" method="GET" style="display:inline;">
                    <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                    <button type="submit" class="btn-cancel">Show</button>
                  </form>
                <?php endif; ?>


              </li>
            <?php endforeach; ?>

          <?php endif; ?>
        </ul>
      </section>
    </main>
  </div>
     <footer class="site-footer">
      <div class="footer-content">
        <div class="footer-brand">
          <h3>Eventify</h3>
          <p>Bringing people together through unforgettable events.</p>
        </div>

        <div class="footer-links">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="browse.php">Browse Events</a></li>
            <li><a href="dashboard-router.php">Dashboard</a></li>
            <li><a href="login.php">Login</a></li>
          </ul>
        </div>

        <div class="footer-contact">
          <h4>Contact</h4>
          <p>Email: support@eventify.com</p>
          <p>Phone: +91-98765-43210</p>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; 2025 Eventify. All rights reserved.</p>
      </div>
    </footer>

</body>
</html>
