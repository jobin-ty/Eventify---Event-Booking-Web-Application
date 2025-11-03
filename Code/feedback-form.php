<?php
session_start();
require_once 'db.php';

$success = false;
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$existing = null;

// Check for existing feedback
$stmt = $conn->prepare("SELECT comment, rating FROM feedback WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$existing = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $comment = $_POST['comment'];
    $rating = $_POST['rating'];

    if ($existing) {
        // Update
        $stmt = $conn->prepare("UPDATE feedback SET comment = ?, rating = ? WHERE user_id = ?");
        $stmt->bind_param("sii", $comment, $rating, $userId);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, comment, rating) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $userId, $comment, $rating);
    }
    $stmt->execute();
    $stmt->close();
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Provide Feedback - Eventify</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="feedback-form-styles.css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat%20Alternates:400,700" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/feda979d6d.js" crossorigin="anonymous"></script>
</head>
<body class="feedback-form-page">
  <h1>We Value Your Feedback</h1>
  <div class="feedback-form-container">
    <form method="POST">
      <label for="comment">Your Feedback</label>
      <textarea id="comment" name="comment" required><?= htmlspecialchars($existing['comment'] ?? '') ?></textarea>

      <label>Rate Us</label>
      <div class="star-rating" id="star-rating">
        <?php
        $currentRating = (int)($existing['rating'] ?? 0);
        for ($i = 1; $i <= 5; $i++) {
            $filled = $i <= $currentRating ? 'selected' : '';
            echo "<span class='star $filled' data-value='$i'>★</span>";
        }
        ?>
      </div>
      <input type="hidden" name="rating" id="rating" value="<?= $currentRating ?>">

      <button type="submit" class="btn">Submit Feedback <i class="fas fa-paper-plane"></i></button>
    </form>
  </div>

  <?php if ($success): ?>
    <div id="success-popup">Thank you! Your feedback has been saved.</div>
    <script>
      setTimeout(() => {
        window.location.href = "index.php";
      }, 500);
    </script>
  <?php endif; ?>

  <script>
    const stars = document.querySelectorAll(".star");
    const ratingInput = document.getElementById("rating");

    stars.forEach((star) => {
      star.addEventListener("click", function () {
        const rating = this.getAttribute("data-value");
        ratingInput.value = rating;

        stars.forEach((s, index) => {
          s.classList.toggle("selected", index < rating);
        });
      });
    });
  </script>
</body>
</html>
