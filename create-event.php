<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userId = $_SESSION['user_id'];

    // Safely access POST data
    $name = $_POST['eventname'] ?? '';
    $desc = $_POST['event_description'] ?? '';
    $category = $_POST['category'] ?? 'General';
    $location = $_POST['location'] ?? '';
    $date = $_POST['event_date'] ?? '';
    $tickets = $_POST['tickets_available'] ?? 0;
    $price = $_POST['event_price'] ?? 0;
    $locationDesc = $_POST['location_description'] ?? '';

    // Upload video if present
    $videoPath = "";
    
    $maxFileSize = 20 * 1024 * 1024; // 10MB in bytes

    if (!empty($_FILES['video']['name'])) {
        if ($_FILES['video']['size'] > $maxFileSize) {
            die("Video file is too large. Maximum size allowed is 20MB.");
        }

        $videoName = uniqid() . "_" . basename($_FILES['video']['name']);
        $videoPath = "uploads/videos/" . $videoName;
        move_uploaded_file($_FILES['video']['tmp_name'], $videoPath);
    }

    // Insert event
    $stmt = $conn->prepare("INSERT INTO events (user_id, name, description, location, location_description, event_date, tickets_available, price, video_path, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssidss", $userId, $name, $desc, $location, $locationDesc, $date, $tickets, $price, $videoPath, $category);
    $stmt->execute();
    $eventId = $stmt->insert_id;
    $stmt->close();

    // Upload images
    for ($i = 1; $i <= 3; $i++) {
        $field = "image$i";
        
        if (!empty($_FILES[$field]['name'])) {
          if ($_FILES[$field]['size'] > $maxFileSize) {
            die("Image file $field is too large. Max 10MB allowed.");
          }

            $imageName = uniqid() . "_" . basename($_FILES[$field]['name']);
            $imagePath = "uploads/images/" . $imageName;
            move_uploaded_file($_FILES[$field]['tmp_name'], $imagePath);

            $stmtImg = $conn->prepare("INSERT INTO event_images (event_id, image_path) VALUES (?, ?)");
            $stmtImg->bind_param("is", $eventId, $imagePath);
            $stmtImg->execute();
            $stmtImg->close();
        }
    }

    
    // Set a flag to show the success popup
    $showSuccess = true;
  }
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Event</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat%20Alternates:700|Montserrat%20Alternates:400" rel="stylesheet" />
    <link rel="stylesheet" href="create-event-styles.css" />
</head>
<body>
  <?php if (!empty($showSuccess)): ?>
    <div id="success-popup">
        Event created successfully!
    </div>
    <script>
    setTimeout(function() {
        window.location.href = 'dashboard-organizer.php';
    }, 2000);
    </script>
  <?php endif; ?>
  <div class="create-event-page">
    <h1>Create Event</h1>
    <form method="POST" enctype="multipart/form-data">
      <label for="eventname">Event Name</label>
      <input type="text" id="eventname" name="eventname" placeholder="Enter Event Title" required autocomplete="name" />

      <label for="event_description">Description</label>
      <textarea id="event_description" name="event_description" placeholder="Description" required></textarea>

      <label for="category">Category</label>
      <select id="category" name="category" required>
        <option value="">-- Select Category --</option>
        <option value="Music">Music</option>
        <option value="Art">Art</option>
        <option value="Tech">Tech</option>
        <option value="Sports">Sports</option>
        <option value="Education">Education</option>
        <option value="Health">Health</option>
        <option value="Food">Food</option>
        <option value="Business">Business</option>
        <option value="General">General</option>
      </select>


      <label for="image">Add Images</label>
      <input type="file" id="image" name="image1" placeholder="Add Image" accept="image/*" required />
      <input type="file" id="image" name="image2" accept="image/*" />
      <input type="file" id="image" name="image3" accept="image/*" />

      <label>Video (optional)</label>
      <input type="file" name="video" accept="video/*" />

      <label for="location">Location</label>
      <select id="location" name="location" required>
          <option value="">-- Select Location --</option>
          <option value="Thiruvananthapuram">Thiruvananthapuram</option>
          <option value="Kollam">Kollam</option>
          <option value="Alappuzha">Alappuzha</option>
          <option value="Pathanamthitta">Pathanamthitta</option>
          <option value="Kottayam">Kottayam</option>
          <option value="Idukki">Idukki</option>
          <option value="Ernakulam">Ernakulam</option>
          <option value="Thrissur">Thrissur</option>
          <option value="Palakkad">Palakkad</option>
          <option value="Malappuram">Malappuram</option>
          <option value="Kozhikode">Kozhikode</option>
          <option value="Wayanad">Wayanad</option>
          <option value="Kannur">Kannur</option>
          <option value="Kasaragod">Kasaragod</option>
      </select>

      <label for="location_description">Location Description</label>
      <textarea id="location_description" name="location_description" placeholder="Description" required></textarea>

      <label for="event_date">Event Date</label>
<input type="date" id="event_date" name="event_date" required min="" />

<script>
  // Set today's date as the minimum
  const today = new Date().toISOString().split("T")[0];
  document.getElementById("event_date").setAttribute("min", today);
</script>


      <label for="tickets_available">Tickets Available</label>
      <input type="range" id="tickets_available" name="tickets_available" min="0" max="10000" step="10" value="0" oninput="ticketsOutput.value = this.value" />
      <output id="ticketsOutput">0</output>

      <label for="event_price">Event Price (₹)</label>
      <input type="range" id="event_price" name="event_price" min="0" max="15000" step="10" value="0" oninput="priceOutput.value = '$' + this.value" />
      <output id="priceOutput">₹0</output>

      
      <button type="submit" class="btn">
        Create <span class="arrow">→</span>
      </button>
    </form>
  </div>
  <script>
    document.querySelector("form").addEventListener("submit", function (e) {
      const maxSize = 20 * 1024 * 1024;
      const files = [...document.querySelectorAll('input[type="file"]')];
      for (const fileInput of files) {
        if (fileInput.files[0] && fileInput.files[0].size > maxSize) {
          alert(`"${fileInput.files[0].name}" is too large. Max size is 20MB.`);
          e.preventDefault();
          return;
        }
      }
    });
  </script>

</body>
</html>

