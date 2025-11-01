<?php
session_start();
require_once 'db.php';

$eventId = $_GET['event_id'] ?? null;
if (!$eventId || !is_numeric($eventId)) {
    die("Invalid event.");
}

// Fetch event details
$stmt = $conn->prepare("SELECT name, description, location, location_description, event_date, tickets_available, tickets_sold, price, video_path FROM events WHERE id = ?");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    die("Event not found.");
}

// Fetch event images
$stmt = $conn->prepare("SELECT image_path FROM event_images WHERE event_id = ?");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$availableTickets = $event['tickets_available'] - $event['tickets_sold'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($event['name']) ?> - Book Tickets | Eventify</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css?family=Montserrat+Alternates:700|Montserrat+Alternates:400" rel="stylesheet" />
  <link rel="stylesheet" href="book-event-styles.css" />
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
        <a href="login.html" tabindex="4">Login</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="book-event-page">
    <div class="event-container">
      <!-- Media Section -->
      <div class="media-column">
        <div class="slideshow-container">
          <?php foreach ($images as $i => $img): ?>
            <img class="slide<?= $i === 0 ? ' active' : '' ?>" src="<?= htmlspecialchars($img['image_path']) ?>" alt="Event image <?= $i + 1 ?>">
          <?php endforeach; ?>

          <?php if (!empty($event['video_path'])): ?>
            <video class="slide<?= empty($images) ? ' active' : '' ?>" controls>
              <source src="<?= htmlspecialchars($event['video_path']) ?>" type="video/mp4">
              Your browser does not support the video tag.
            </video>
          <?php endif; ?>

        </div>

        <?php if (count($images) + (!empty($event['video_path']) ? 1 : 0) > 1): ?>
          <button class="prev" onclick="plusSlides(-1)">❮</button>
          <button class="next" onclick="plusSlides(1)">❯</button>
        <?php endif; ?>
      </div>

      <!-- Event Info -->
      <div class="details-column">
        <h2><?= htmlspecialchars($event['name']) ?></h2>
        <p class="event-date"><?= date('F j, Y', strtotime($event['event_date'])) ?></p>
        <p class="event-location"><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
        <p class="location-description"><?= htmlspecialchars($event['location_description']) ?></p>
        <p class="event-description"><?= nl2br(htmlspecialchars($event['description'])) ?></p>

        <p class="ticket-price">
          <strong>Price:</strong> ₹<?= number_format($event['price'], 2) ?>
        </p>

        <form action="book-confirm.php" method="POST">
          <div class="ticket-form">
          <input type="hidden" name="event_id" value="<?= $eventId ?>">

          <label for="ticket_quantity">Number of Tickets:</label>
          <input type="number" id="ticket_quantity" name="ticket_quantity"
                value="1" min="1" max="<?= $availableTickets ?>"
                oninput="updateTotal()" required>

          <p class="total-price" id="totalDisplay">
            <strong>Total:</strong> ₹<?= number_format($event['price'], 2) ?>
          </p>

          <button type="submit" class="btn-book"
                  <?= $availableTickets <= 0 ? 'disabled' : '' ?>>
            <?= $availableTickets <= 0 ? 'Sold Out' : 'Book Tickets' ?>
          </button>
          </div>
        </form>

        <script>
          const ticketInput = document.getElementById('ticket_quantity');
          const totalDisplay = document.getElementById('totalDisplay');
          const pricePerTicket = <?= json_encode($event['price']) ?>;

          function updateTotal() {
            const quantity = parseInt(ticketInput.value) || 1;
            const total = quantity * pricePerTicket;
            totalDisplay.innerHTML = `<strong>Total:</strong> ₹${total.toFixed(2)}`;
          }
        </script>


        </div>
      </div>
    </div>
  </main>

  <script>
    let slideIndex = 0;
    const slides = document.querySelectorAll(".slide");

    function showSlide(n) {
      slides.forEach((slide, i) => {
        slide.style.display = i === n ? "block" : "none";
        slide.classList.toggle("active", i === n);
      });
    }

    function plusSlides(n) {
      slideIndex = (slideIndex + n + slides.length) % slides.length;
      showSlide(slideIndex);
    }

    window.onload = () => {
      if (slides.length > 0) showSlide(slideIndex);
    };
  </script>
</body>
</html>
