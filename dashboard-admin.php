<?php
session_start();
require_once 'db.php';

// Access control
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    die("Access denied.");
}

$userId = $_SESSION['user_id'] ?? 0;

// Handle event deletion if POST request received
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event_id'])) {
    $deleteId = (int)$_POST['delete_event_id'];

    // Delete related images
    $stmt = $conn->prepare("SELECT image_path FROM event_images WHERE event_id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach ($images as $img) {
        if (file_exists($img['image_path'])) unlink($img['image_path']);
    }
    $stmt->close();

    // Delete related video
    $stmt = $conn->prepare("SELECT video_path FROM events WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $video = $stmt->get_result()->fetch_assoc();
    if ($video && file_exists($video['video_path'])) unlink($video['video_path']);
    $stmt->close();

    // Delete from tables
    $conn->query("DELETE FROM event_images WHERE event_id = $deleteId");
    $conn->query("DELETE FROM bookings WHERE event_id = $deleteId");
    $conn->query("DELETE FROM events WHERE id = $deleteId");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $deleteUserId = (int)$_POST['delete_user_id'];

    // 1. Get all bookings made by the user
    $stmt = $conn->prepare("SELECT id FROM bookings WHERE user_id = ?");
    $stmt->bind_param("i", $deleteUserId);
    $stmt->execute();
    $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // 2. Delete payments related to those bookings
    foreach ($bookings as $booking) {
        $bookingId = (int) $booking['id'];
        $conn->query("DELETE FROM payments WHERE booking_id = $bookingId");
    }

    // 3. Delete bookings
    $conn->query("DELETE FROM bookings WHERE user_id = $deleteUserId");

    // 4. Finally, delete the user
    $conn->query("DELETE FROM users WHERE id = $deleteUserId");
}


// Fetch dashboard metrics
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$totalEvents = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$totalBookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$totalRevenue = $conn->query("SELECT SUM(amount) as total FROM payments WHERE status = 'completed'")->fetch_assoc()['total'] ?? 0;

// Fetch all events
$events = $conn->query("
SELECT e.id, e.name, e.event_date, e.location, u.name AS organizer_name
FROM events e
JOIN users u ON e.user_id = u.id
ORDER BY e.event_date DESC
");

$users = $conn->query("SELECT id, name, email, user_type FROM users ORDER BY id ASC");

$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Eventify</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css?family=Montserrat+Alternates:700|Montserrat+Alternates:400" rel="stylesheet">
  <link rel="stylesheet" href="dashboard-admin-styles.css">
</head>
<body>
  <header>
    <h1>Eventify</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="browse.php">Browse Events</a>
      <a href="dashboard-router.php">Dashboard</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main class="dashboard-admin-page">
    <section class="hero">
      <h1>Welcome, Admin <?= htmlspecialchars($user['name']) ?></h1>
      <p>System overview and analytics.</p>
    </section>

    <section class="dashboard-cards">
      <article class="card"><h3>Total Users</h3><p class="metric"><?= $totalUsers ?></p></article>
      <article class="card"><h3>Total Events</h3><p class="metric"><?= $totalEvents ?></p></article>
      <article class="card"><h3>Total Bookings</h3><p class="metric"><?= $totalBookings ?></p></article>
      <article class="card"><h3>Total Revenue</h3><p class="metric">₹<?= number_format($totalRevenue, 2) ?></p></article>
    </section>

    <section class="event-list">
      <h3>All Events</h3>
      <?php while ($event = $events->fetch_assoc()): ?>
        <div class="event-item">
          <div>
            <h4><?= htmlspecialchars($event['name']) ?></h4>
            <p>
              <?= date('F j, Y', strtotime($event['event_date'])) ?> – <?= htmlspecialchars($event['location']) ?><br>
              <small>Organizer: <strong><?= htmlspecialchars($event['organizer_name']) ?></strong></small>
            </p>
          </div>
          <form method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');">
            <input type="hidden" name="delete_event_id" value="<?= $event['id'] ?>">
            <button type="submit" class="delete-button">Delete</button>
          </form>
        </div>
      <?php endwhile; ?>
    </section>
    <section class="user-list">
      <h3>All Users</h3>
      <?php while ($user = $users->fetch_assoc()): ?>
        <div class="user-item">
          <div>
            <h4>
              <?= htmlspecialchars($user['name']) ?>
              <span class="user-type-badge"><?= $user['user_type'] ?></span>
            </h4>
            <p><?= htmlspecialchars($user['email']) ?></p>
          </div>
          <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
            <input type="hidden" name="delete_user_id" value="<?= $user['id'] ?>">
            <button type="submit" class="delete-button">Delete</button>
          </form>
        </div>
      <?php endwhile; ?>
    </section>

  </main>
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
        <li><a href="login.html">Login</a></li>
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
