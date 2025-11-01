<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'event_organizer') {
    die("Access denied.");
}

$userId = $_SESSION['user_id'];
$eventId = $_GET['id'] ?? null;

if (!$eventId) {
    die("Invalid event.");
}


// Handle delete request
if (isset($_POST['delete_event'])) {
    // Step 1: Fetch and delete image files
    $stmt = $conn->prepare("SELECT image_path FROM event_images WHERE event_id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $images = $stmt->get_result();
    while ($img = $images->fetch_assoc()) {
        if (file_exists($img['image_path'])) {
            unlink($img['image_path']);
        }
    }
    $stmt->close();

    // Step 2: Delete video file (if exists)
    $stmt = $conn->prepare("SELECT video_path FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $stmt->bind_result($videoPath);
    $stmt->fetch();
    $stmt->close();
    if (!empty($videoPath) && file_exists($videoPath)) {
        unlink($videoPath);
    }

    // Step 3: Delete image entries
    $stmt = $conn->prepare("DELETE FROM event_images WHERE event_id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $stmt->close();

    // Step 4: Delete the event itself
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $eventId, $userId);
    $stmt->execute();
    $stmt->close();

    echo <<<HTML
    <div id="success-popup"> Event deleted successfully.</div>
    <style>
        #success-popup {
            position: fixed;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color:rgb(255, 255, 255);
            color:rgb(14, 18, 83);
            padding: 20px 30px;
            border: 2px solid rgb(0, 0, 0);
            border-radius: 10px;
            font-family: sans-serif;
            font-weight: bold;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            animation: fadeOut 2s ease-in-out forwards;
            animation-delay: 1s;
        }
        @keyframes fadeOut {
            to { opacity: 0; }
        }
    </style>
    <script>
        setTimeout(function() {
            window.location.href = 'dashboard-organizer.php';
        }, 2000);
    </script>
    HTML;
    $conn->close();
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desc = $_POST['event_description'] ?? '';
    $location = $_POST['location'] ?? '';
    $locationDesc = $_POST['location_description'] ?? '';
    $date = $_POST['event_date'] ?? '';
    $tickets = $_POST['tickets_available'] ?? 0;
    $price = $_POST['event_price'] ?? 0;

    $stmt = $conn->prepare("UPDATE events SET description = ?, location = ?, location_description = ?, event_date = ?, tickets_available = ?, price = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssssiiii", $desc, $location, $locationDesc, $date, $tickets, $price, $eventId, $userId);
    $stmt->execute();
    $stmt->close();

    echo <<<HTML
        <div id="success-popup"> Event updated successfully!</div>
        <script>
            setTimeout(function() {
                window.location.href = 'dashboard-organizer.php';
            }, 2000);
        </script>
    HTML;

    $conn->close();
    exit;
}

// Fetch event for prefill
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $eventId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    die("Event not found or you do not have permission.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Event</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat+Alternates:700|Montserrat+Alternates:400" rel="stylesheet" />
  <link rel="stylesheet" href="create-event-styles.css"/>
</head>
<body>
<div class="create-event-page">
    <h1>Edit Event</h1>
    <form method="POST" autocomplete="off">
        <label>Event Name (Not Editable)</label>
        <input type="text" value="<?= htmlspecialchars($event['name']) ?>" disabled />

        <label for="event_description">Description</label>
        <textarea name="event_description" required><?= htmlspecialchars($event['description']) ?></textarea>

        <label for="location">Location</label>
        <select name="location" required>
            <?php
            $locations = ["Thiruvananthapuram", "Kollam", "Alappuzha", "Pathanamthitta", "Kottayam", "Idukki", "Ernakulam", "Thrissur", "Palakkad", "Malappuram", "Kozhikode", "Wayanad", "Kannur", "Kasaragod"];
            foreach ($locations as $loc) {
                $selected = $event['location'] === $loc ? 'selected' : '';
                echo "<option value=\"$loc\" $selected>$loc</option>";
            }
            ?>
        </select>

        <label for="location_description">Location Description</label>
      <textarea id="location_description" name="location_description" placeholder="Description"><?= htmlspecialchars($event['location_description']) ?></textarea>


        <label for="event_date">Event Date</label>
        <input type="date" name="event_date" value="<?= $event['event_date'] ?>" required min="<?= date('Y-m-d') ?>" />

        <label for="tickets_available">Tickets Available</label>
        <input type="range" name="tickets_available" min="0" max="500" step="10" value="<?= $event['tickets_available'] ?>" oninput="ticketsOutput.value = this.value" />
        <output id="ticketsOutput"><?= $event['tickets_available'] ?></output>

        <label for="event_price">Event Price (₹)</label>
        <input type="range" name="event_price" min="0" max="15000" step="10" value="<?= $event['price'] ?>" oninput="priceOutput.value = '₹' + this.value" />
        <output id="priceOutput">₹<?= $event['price'] ?></output>

        <button type="submit" class="btn">Update <span class="arrow">→</span></button>
        <button type="submit" name="delete_event" class="btn" style="background-color: crimson;"> Delete Event </button>

    </form>

    <script>
    document.querySelector("form").addEventListener("submit", function(e) {
    if (e.submitter.name === "delete_event") {
        const confirmDelete = confirm("Are you sure you want to delete this event permanently?");
        if (!confirmDelete) {
            e.preventDefault();
        }
    }
});
</script>

</div>
</body>
</html>
