<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'], $_SESSION['payment_info'])) {
    die("Unauthorized access or session expired.");
}

$userId = $_SESSION['user_id'];
$payment = $_SESSION['payment_info'];

$eventId = $payment['event_id'];
$quantity = $payment['quantity'];
$total = $payment['total'];

// 1. Validate event and availability
$stmt = $conn->prepare("SELECT tickets_available, tickets_sold FROM events WHERE id = ?");
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

// 2. Create booking
$stmt = $conn->prepare("INSERT INTO bookings (user_id, event_id, ticket_count, booked_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iii", $userId, $eventId, $quantity);
$stmt->execute();
$bookingId = $stmt->insert_id;
$stmt->close();

// 3. Update event's sold tickets
$stmt = $conn->prepare("UPDATE events SET tickets_sold = tickets_sold + ? WHERE id = ?");
$stmt->bind_param("ii", $quantity, $eventId);
$stmt->execute();
$stmt->close();

// 4. Generate mock payment reference
$paymentReference = 'MOCK-' . strtoupper(uniqid());

// 5. Record payment in `payments` table and set status to 'completed'in bookings
$stmt = $conn->prepare("
    INSERT INTO payments (booking_id, user_id, amount, status, payment_method, payment_reference, paid_at)
    VALUES (?, ?, ?, 'completed', 'mock_gateway', ?, NOW())
");
$stmt->bind_param("iids", $bookingId, $userId, $total, $paymentReference);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$stmt->close();


// 6. Clear session payment data
unset($_SESSION['payment_info']);

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // SMTP config
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ashikrojanvaikom@gmail.com'; // Your Gmail
    $mail->Password = 'seec mcwh afrt vayn';   // App password from Gmail
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Recipient
    $mail->setFrom('yourgmail@gmail.com', 'Eventify');
    
    // Fetch user's email from DB (optional)
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $userResult = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $userEmail = $userResult['email'] ?? null;
    if ($userEmail) {
        $mail->addAddress($userEmail);
    } else {
        throw new Exception("Email not found.");
    }

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Booking Confirmed for Event ID #' . $eventId;
    $mail->Body    = "
        <h2>Thank you for booking with Eventify!</h2>
        <p>Your booking (Booking ID: $bookingId) for Event ID <strong>$eventId</strong> has been confirmed.</p>
        <p>Quantity: $quantity ticket(s)</p>
        <p>Total Paid: ₹" . number_format($total, 2) . "</p>
        <p>Reference: $paymentReference</p>
    ";

    $mail->send();
} catch (Exception $e) {
    error_log("Email not sent: " . $mail->ErrorInfo);
    // You may choose to continue silently or show a warning
}

// 7. Redirect to confirmation page
header("Location: payment-success.php?ref=" . urlencode($paymentReference));
exit;
?>
