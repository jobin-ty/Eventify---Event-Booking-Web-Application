<?php
session_start();
require_once 'db.php';

if (!isset($_POST['event_id'], $_POST['ticket_quantity'])) {
    die("Missing data.");
}

$eventId = (int) $_POST['event_id'];
$quantity = (int) $_POST['ticket_quantity'];

if ($quantity <= 0) {
    die("Invalid ticket quantity.");
}

// Fetch event info
$stmt = $conn->prepare("SELECT price, tickets_available, tickets_sold FROM events WHERE id = ?");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    die("Event not found.");
}

$available = $event['tickets_available'] - $event['tickets_sold'];

if ($quantity > $available) {
    die("Not enough tickets available.");
}

// Save booking data in session before payment
$_SESSION['payment_info'] = [
    'event_id' => $eventId,
    'quantity' => $quantity,
    'total' => $quantity * $event['price']
];

// ✅ Redirect to mock payment page
header("Location: mock-payment-gateway.php");
exit;
?>