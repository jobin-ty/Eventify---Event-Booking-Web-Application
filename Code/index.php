<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Event Booking Platform</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat%20Alternates:700|Montserrat%20Alternates:400" rel="stylesheet" />
  <link rel="stylesheet" href="index-styles.css" />
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <script src="https://kit.fontawesome.com/feda979d6d.js" crossorigin="anonymous"></script>
</head>
<body>
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
      <?php else: ?>
        <a href="login.php" tabindex="4">Login</a>
      <?php endif; ?>
      
    </nav>
  </header>

  <main class="hero" aria-label="Hero Section">
    <h2 class="hero-title">Discover and Book Amazing Events Near You</h2>
    <p class="hero-subtitle">
      Join thousands of event enthusiasts and organizers using Eventify to browse, book, and share unforgettable experiences.
    </p>
    <div class="btn-group">
      <button class="btn" onclick="window.location.href='browse.php'" tabindex="6">
        Browse Events <span class="arrow">→</span>
      </button>
      <!-- If the user is already logged in, skip pointing them to login -->
      <?php if (!isset($_SESSION['user_type'])): ?>
        <button class="btn" onclick="window.location.href='login.php'" tabindex="7">
          Login <span class="arrow">→</span>
        </button>
      <?php else: ?>
        <button class="btn" onclick="window.location.href='dashboard-router.php'" tabindex="7">
          Go to Dashboard <span class="arrow">→</span>
        </button>
      <?php endif; ?>
    </div>
  </main>

    <!-- About Us Section -->
  <section class="about-section" aria-label="About Eventify">
    <h2>About Eventify</h2>
    <p class="about-description">
      Eventify is your all-in-one platform for discovering, booking, and organizing events with ease. 
      Whether you're a passionate event-goer or a creative organizer, our goal is to connect communities 
      through shared experiences. Built with simplicity and performance in mind, we empower thousands 
      to create unforgettable moments.
    </p>
  </section>

  <?php
    require_once 'db.php';
    $feedbacks = [];
    $stmt = $conn->prepare("
      SELECT f.comment, f.rating, u.name, u.user_type
      FROM feedback f
      JOIN users u ON f.user_id = u.id
      ORDER BY f.id DESC
      LIMIT 6
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $feedbacks = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    ?>

  <section class="feedback-section" aria-label="User Feedback">
    <h2>What Our Users Say</h2>
      <div class="feedback-scroll-wrapper">
        <div class="feedback-scroll">
          <?php foreach ($feedbacks as $fb): ?>
            <div class="feedback-card">
              <p class="comment">“<?= htmlspecialchars($fb['comment']) ?>”</p>
              <p class="user-info"><strong><?= htmlspecialchars($fb['name']) ?> – <?= ucfirst($fb['user_type']) ?></strong></p>
              <p class="rating"><?= str_repeat("⭐", (int)$fb['rating']) ?></p>
            </div>
          <?php endforeach; ?>

          <!-- Repeat once for smooth scrolling -->
          <?php foreach ($feedbacks as $fb): ?>
            <div class="feedback-card">
              <p class="comment">“<?= htmlspecialchars($fb['comment']) ?>”</p>
              <p class="user-info"><strong><?= htmlspecialchars($fb['name']) ?> – <?= ucfirst($fb['user_type']) ?></strong></p>
              <p class="rating"><?= str_repeat("⭐", (int)$fb['rating']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
       <?php if (!isset($_SESSION['user_type'])): ?>
        <a href="login.php" class="btn-feedback" aria-label="Provide Feedback">
          Provide Feedback <i class="fa-solid fa-bullhorn"></i></a>
        </a>
      <?php else: ?>
        <a href="feedback-form.php" class="btn-feedback" aria-label="Provide Feedback">
          Provide Feedback <i class="fa-solid fa-bullhorn"></i></a>
      <?php endif; ?>
  </section>

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
      <?php if (isset($_SESSION['user_type'])): ?>
        <a href="feedback-form.php">Provide Feedback <i class="fa-solid fa-bullhorn"></i></a>
      <?php else: ?>
        <a href="login.php">Provide Feedback <i class="fa-solid fa-bullhorn"></i></a>
      <?php endif; ?>
    </div>
  </div>

  <div class="footer-bottom">
    <p>&copy; 2025 Eventify. All rights reserved.</p>
  </div>
</footer>

<script>
  const scrollContainer = document.getElementById('feedback-scroll');

  // Clone the feedback content to allow seamless looping
  const clone = scrollContainer.cloneNode(true);
  scrollContainer.parentElement.appendChild(clone);

  let scrollSpeed = 0.5;

  function autoScroll() {
    scrollContainer.scrollLeft += scrollSpeed;

    // If we've scrolled past the first set of cards, reset back to start
    if (scrollContainer.scrollLeft >= scrollContainer.scrollWidth) {
      scrollContainer.scrollLeft = 0;
    }

    requestAnimationFrame(autoScroll);
  }

  window.addEventListener("load", autoScroll);
</script>


</body>
</html>
