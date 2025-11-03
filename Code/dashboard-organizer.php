<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'event_organizer') {
    header("Location: login.php");
    exit();
}

$organizerId = $_SESSION['user_id'] ?? 0;

// Fetch total events created
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM events WHERE user_id = ?");
$stmt->bind_param("i", $organizerId);
$stmt->execute();
$result = $stmt->get_result();
$totalEvents = $result->fetch_assoc()['total'] ?? 0;
$stmt->close();

// Fetch total tickets sold
$stmt = $conn->prepare("SELECT SUM(tickets_sold) AS total_sold FROM events WHERE user_id = ?");
$stmt->bind_param("i", $organizerId);
$stmt->execute();
$result = $stmt->get_result();
$totalTickets = $result->fetch_assoc()['total_sold'] ?? 0;
$stmt->close();

// Fetch recent events, sorted by last modified
// Fetch all events for this organizer
$stmt = $conn->prepare("SELECT id, name, event_date, last_modified, tickets_sold, tickets_available FROM events WHERE user_id = ? ORDER BY event_date DESC");
$stmt->bind_param("i", $organizerId);
$stmt->execute();
$allEvents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $organizerId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();
$stmt->close();

$upcomingEvents = [];
$pastEvents = [];

$today = date('Y-m-d');
foreach ($allEvents as $event) {
    if ($event['event_date'] >= $today) {
        $upcomingEvents[] = $event;
    } else {
        $pastEvents[] = $event;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Organizer Dashboard - Eventify</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat+Alternates:700|Montserrat+Alternates:400" rel="stylesheet" />
  <link rel="stylesheet" href="dashboard-organizer-styles.css"/>
</head>
<body>
<div class="dashboard-organizer-page">
  <header>
    <h1>Eventify</h1>
    <nav>
      <a href="index.php" tabindex="1">Home</a>
      <a href="browse.php" tabindex="2">Browse Events</a>
      <a href="dashboard-router.php" tabindex="3">Dashboard</a>
      <?php if (isset($_SESSION['user_type'])): ?>
        <a href="logout.php" tabindex="4">Logout</a>
      <?php else: ?>
        <a href="login.php" tabindex="4">Login</a>
      <?php endif; ?>
    </nav>
  </header>

  <main>
    <section class="hero" aria-label="Dashboard Overview">
      <h1>Welcome back,<?= htmlspecialchars( $user['name']?? 'Organizer')?>!</h1>
      <p>Manage your events and view stats here.</p>
      <a href="create-event.php" class="btn-primary" role="button">Create Event</a>
    </section>

    <section class="dashboard-cards" aria-label="Organizer summary stats">
      <article class="card" tabindex="0">
        <h3>Total Events Created</h3>
        <p class="metric"><?= $totalEvents ?></p>
        <p class="caption">Live and Past Events</p>
      </article>
      <article class="card" tabindex="0">
        <h3>Total Tickets Sold</h3>
        <p class="metric"><?= $totalTickets ?? 0 ?></p>
        <p class="caption">All events combined</p>
      </article>
    </section>

    <section class="events-section" aria-label="Upcoming Events">
      <h2>Upcoming Events</h2>
      <ul class="events-list">
        <?php if (empty($upcomingEvents)): ?>
          <li>No upcoming events.</li>
        <?php else: ?>
          <?php foreach ($upcomingEvents as $event): ?>
            <li class="event-item" tabindex="0">
              <div class="event-detail">
                <span class="event-title"><?= htmlspecialchars($event['name']) ?></span>
                <span class="event-date"><?= date('F j, Y', strtotime($event['event_date'])) ?></span>
                <span class="event-tickets">Tickets Booked: <?= (int)$event['tickets_sold'] ?> / <?= (int)$event['tickets_available'] ?></span>
              </div>
              <button class="edit-btn" onclick="window.location.href='edit-event.php?id=<?= $event['id'] ?>'">Manage</button>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </section>

    <section class="events-section" aria-label="Past Events">
      <h2>Past Events</h2>
      <ul class="events-list">
        <?php if (empty($pastEvents)): ?>
          <li>No past events.</li>
        <?php else: ?>
          <?php foreach ($pastEvents as $event): ?>
            <li class="event-item" tabindex="0">
              <div class="event-detail">
                <span class="event-title"><?= htmlspecialchars($event['name']) ?></span>
                <span class="event-date"><?= date('F j, Y', strtotime($event['event_date'])) ?></span>
                <span class="event-tickets">Tickets Booked: <?= (int)$event['tickets_sold'] ?> / <?= (int)$event['tickets_available'] ?></span>
              </div>
              <!-- No Manage button for past events (optional) -->
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
