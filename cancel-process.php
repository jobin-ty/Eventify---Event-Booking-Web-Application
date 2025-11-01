<?php
session_start();
require_once 'db.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

if (!isset($_SESSION['user_id'], $_POST['booking_id'])) {
    die("Unauthorized access.");
}

$bookingId = (int) $_POST['booking_id'];
$userId = $_SESSION['user_id'];

// Fetch booking + payment info
$stmt = $conn->prepare("
  SELECT b.ticket_count, b.event_id, e.name AS event_name, e.price, u.email
  FROM bookings b
  JOIN events e ON b.event_id = e.id
  JOIN users u ON b.user_id = u.id
  WHERE b.id = ? AND b.user_id = ?
");
$stmt->bind_param("ii", $bookingId, $userId);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) die("Booking not found.");

// Update booking status
$conn->query("UPDATE bookings SET status = 'cancelled' WHERE id = $bookingId");

// Refund logic (optional record insert)

// Update tickets_sold
$conn->query("UPDATE events SET tickets_sold = tickets_sold - {$data['ticket_count']} WHERE id = {$data['event_id']}");

// Send email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = ''; // Your Gmail
    $mail->Password = ''; 
    $mail->SMTPSecure = '';
    $mail->Port = 587;

    $mail->setFrom('', 'Eventify');
    $mail->addAddress($data['email']);
    $mail->isHTML(true);
    $mail->Subject = 'Your Booking Has Been Cancelled';
    $mail->Body = "
        <h2>Booking Cancelled</h2>
        <p>Your booking for <strong>{$data['event_name']}</strong> has been successfully cancelled.</p>
        <p>You will receive a refund of ₹" . number_format($data['ticket_count'] * $data['price'], 2) . "</p>
    ";
    $mail->send();
} catch (Exception $e) {
    error_log("Cancellation email failed: " . $mail->ErrorInfo);
}

header("Location: dashboard-router.php?cancel=success");
exit;

