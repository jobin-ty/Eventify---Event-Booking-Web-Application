<?php
session_start();
require_once 'db.php';

$today = date('Y-m-d');

// Handle filtering
$locationFilter = $_GET['location'] ?? '';
$categoryFilter = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'date';

$query = "
  SELECT 
    e.id, e.name, e.description, e.event_date, e.location, e.category,
    (SELECT image_path FROM event_images WHERE event_id = e.id LIMIT 1) AS image_path
  FROM events e
  WHERE e.event_date >= ?
";

$params = [$today];
$types = "s";

if (!empty($locationFilter)) {
    $query .= " AND e.location = ?";
    $params[] = $locationFilter;
    $types .= "s";
}

if (!empty($categoryFilter)) {
    $query .= " AND e.category = ?";
    $params[] = $categoryFilter;
    $types .= "s";
}

switch ($sort) {
  case 'price_asc':
    $query .= " ORDER BY e.price ASC";
    break;
  case 'price_desc':
    $query .= " ORDER BY e.price DESC";
    break;
  default:
    $query .= " ORDER BY e.event_date ASC";
}

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Browse Events - Eventify</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat+Alternates:700|Montserrat+Alternates:400" rel="stylesheet" />
  <link rel="stylesheet" href="browse-styles.css"/>
</head>
<body>
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

  <main class="browse-page">
    <aside class="filter-menu" aria-label="Event Filters">
      <h3>Filter Events</h3>

      <form method="GET" action="browse.php">
        <section class="filter-section">
          <h4>Location</h4>
          <select name="location">
            <option value="">All</option>
            <?php
              $locations = [
                "Thiruvananthapuram", "Kollam", "Alappuzha", "Pathanamthitta", "Kottayam",
                "Idukki", "Ernakulam", "Thrissur", "Palakkad", "Malappuram",
                "Kozhikode", "Wayanad", "Kannur", "Kasaragod"
              ];
              foreach ($locations as $loc) {
                $selected = ($locationFilter === $loc) ? "selected" : "";
                echo "<option value=\"$loc\" $selected>$loc</option>";
              }
            ?>
          </select>
        </section>

        <section class="filter-section">
          <h4>Sort By</h4>
            <select name="sort">
              <option value="date" <?= $sort === 'date' ? 'selected' : '' ?>>Date</option>
              <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price (Low to High)</option>
              <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price (High to Low)</option>
            </select>
        </section>

        <section class="filter-section">
          <h4>Category</h4>
            <select name="category" id="category">
              <option value="">All</option>
              <?php
                $categories = ["Music", "Art", "Tech", "Sports", "Education", "Health", "Food", "Miscellaneous"];
                foreach ($categories as $cat) {
                  $selected = ($categoryFilter === $cat) ? "selected" : "";
                  echo "<option value=\"$cat\" $selected>$cat</option>";
                }
              ?>
            </select>
        </section>

        <button class="apply-button" type="submit">Apply Filters</button>
      </form>
    </aside>

    <section class="events-section">
      <h2>Browse Events</h2>
      <ul class="events-list" aria-label="List of events">
        <?php while ($event = $result->fetch_assoc()): ?>
          <?php
            $bgImage = $event['image_path'] ? htmlspecialchars($event['image_path']) : 'assets/default.jpg';
            if (!str_starts_with($bgImage, 'uploads')) {
              $bgImage = 'uploads/images/' . $bgImage;
            }
          ?>
          <li class="event-card" tabindex="0" style="background-image: url('<?= $bgImage ?>')">
            <div class="overlay"></div>
            <div class="event-content">
              <h3 class="event-title"><?= htmlspecialchars($event['name']) ?></h3>
              <div class="event-date"><?= date('F j, Y', strtotime($event['event_date'])) ?></div>
              <p class="event-description"><?= htmlspecialchars(substr($event['description'], 0, 100)) ?>...</p>
              <a href="book-events.php?event_id=<?= $event['id'] ?>">
                <span class="btn-book">
                Book Now <span class="arrow">→</span>
                </span>
              </a>

            </div>
          </li>
        <?php endwhile; ?>
      </ul>
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
