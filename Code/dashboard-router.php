<?php
ob_start();
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

switch ($_SESSION['user_type']) {
    case 'ticket_booker':
        header("Location: dashboard-booker.php");
        break;

    case 'event_organizer':
        header("Location: dashboard-organizer.php");
        break;

    case 'admin':
        header("Location: dashboard-admin.php");
        break;

    default:
        echo "❌ Unknown user type: " . htmlspecialchars($_SESSION['user_type']);
        session_destroy();
        break;
}
exit;
?>
