<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'], $_GET['booking_id'])) {
    die("Unauthorized access.");
}

$bookingId = (int) $_GET['booking_id'];
$userId = $_SESSION['user_id'];

// Validate ownership and fetch booking
$stmt = $conn->prepare("
  SELECT b.id, b.ticket_count, b.status, e.name AS event_name, e.price, e.event_date
  FROM bookings b
  JOIN events e ON b.event_id = e.id
  WHERE b.id = ? AND b.user_id = ?
");
$stmt->bind_param("ii", $bookingId, $userId);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking || $booking['status'] !== 'confirmed') {
    die("Invalid or already canceled booking.");
}

$refund = $booking['ticket_count'] * $booking['price'];
?>

<!DOCTYPE html>
<html>
<head><title>Confirm Cancellation</title>
<link href="https://fonts.googleapis.com/css?family=Montserrat+Alternates:700|Montserrat+Alternates:400" rel="stylesheet" />
<style>
  body {
    font-family: 'Montserrat Alternates', sans-serif;
    background-color: #faf9f8;
    color: #161d18;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    margin: 0;
    padding: 2rem;
  }

  .cancel-container {
    background: #f5f5f5;
    border-radius: 12px;
    padding: 2.5rem 2rem;
    max-width: 500px;
    width: 100%;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  }

  h2 {
    font-weight: 700;
    color: #2B3A8C;
    margin-bottom: 1.2rem;
    text-align: center;
  }

  p {
    font-size: 1.05rem;
    margin: 0.6rem 0;
    color: #333;
  }

  strong {
    color: #2B3A8C;
  }

  form {
    margin-top: 1.8rem;
    display: flex;
    justify-content: center;
  }

  button {
    background-color:rgb(154, 32, 32);
    color: #fff;
    font-weight: 700;
    padding: 0.85rem 2rem;
    border: none;
    border-radius: 40px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  button:hover {
    background-color:rgb(95, 18, 18);
    transform: scale(1.03);
  }
</style>
</head>
<body>
<div class="cancel-container">
  <h2>Booking details</h2>
  <p>Event: <strong><?= htmlspecialchars($booking['event_name']) ?></strong></p>
  <p>Event Date: <?= date('F j, Y', strtotime($booking['event_date'])) ?></p>
  <p>Tickets: <?= $booking['ticket_count'] ?></p>
  <p>Refund Amount: ₹<?= number_format($refund, 2) ?></p>

  <form method="POST" action="cancel-process.php">
    <input type="hidden" name="booking_id" value="<?= $bookingId ?>">
    <button type="submit">Cancel Booking</button>
  </form>
</div>

</body>
</html>
