<?php
session_start();

if (!isset($_SESSION['payment_info'])) {
    die("Session expired or invalid access.");
}

$payment = $_SESSION['payment_info'];
$eventId = $payment['event_id'];
$quantity = $payment['quantity'];
$total = number_format($payment['total'], 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Mock Payment Gateway</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css?family=Montserrat+Alternates:700|Montserrat+Alternates:400" rel="stylesheet" />
  <style>
    body {
      font-family: 'Montserrat Alternates', sans-serif;
      background: #f4f4f4;
      padding: 2rem;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .payment-box {
      background: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 500px;
    }

    h2 {
      text-align: center;
      color: #2B3A8C;
      margin-bottom: 1.5rem;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    label {
      font-weight: 600;
      display: block;
      margin-bottom: 0.3rem;
    }

    input {
      width: 100%;
      padding: 0.7rem;
      font-size: 1rem;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .btn {
      background: #2B3A8C;
      color: white;
      border: none;
      padding: 0.8rem 1.5rem;
      width: 100%;
      font-size: 1rem;
      font-weight: 700;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 1rem;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background: #1d265d;
    }

    .summary {
      background: #f1f1f1;
      padding: 1rem;
      border-radius: 6px;
      margin-bottom: 1.5rem;
      text-align: center;
      font-weight: 600;
    }

    .secure-note {
      font-size: 0.85rem;
      color: #555;
      margin-top: 1rem;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="payment-box">
    <h2>Enter Card Details</h2>

    <div class="summary">
      Booking for <?= $quantity ?> ticket<?= $quantity > 1 ? 's' : '' ?> <br>
      <strong>Total: ₹<?= $total ?></strong>
    </div>

    <form action="mock-payment.php" method="POST">
      <div class="form-group">
        <label for="card_number">Card Number</label>
        <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required />
      </div>

      <div class="form-group">
        <label for="expiry">Expiry Date</label>
        <input type="text" id="expiry" name="expiry" placeholder="MM/YY" required />
      </div>

      <div class="form-group">
        <label for="cvv">CVV</label>
        <input type="password" id="cvv" name="cvv" placeholder="123" required />
      </div>

      <button type="submit" class="btn">Pay ₹<?= $total ?></button>
    </form>

    <p class="secure-note">This is a mock gateway. No real transaction will occur.</p>
  </div>
  <script>
const cardInput = document.getElementById("card_number");
const expiryInput = document.getElementById("expiry");
const cvvInput = document.getElementById("cvv");
const form = document.querySelector("form");

// Format card number as "1234 5678 9012 3456"
cardInput.addEventListener("input", (e) => {
  let value = e.target.value.replace(/\D/g, "").substring(0, 16);
  let formatted = value.match(/.{1,4}/g);
  e.target.value = formatted ? formatted.join(" ") : "";
});

// Format expiry as "MM/YY"
expiryInput.addEventListener("input", (e) => {
  let value = e.target.value.replace(/\D/g, "").substring(0, 4);
  if (value.length >= 3) {
    e.target.value = value.substring(0, 2) + "/" + value.substring(2);
  } else {
    e.target.value = value;
  }
});

// Limit CVV to 3 digits only
cvvInput.addEventListener("input", (e) => {
  e.target.value = e.target.value.replace(/\D/g, "").substring(0, 3);
});

// Validate expiry date on submit
form.addEventListener("submit", (e) => {
  const expiry = expiryInput.value.trim();
  const match = expiry.match(/^(\d{2})\/(\d{2})$/);

  if (!match) {
    alert("Invalid expiry date format. Use MM/YY.");
    e.preventDefault();
    return;
  }

  const inputMonth = parseInt(match[1], 10);
  const inputYear = parseInt(match[2], 10) + 2000;

  if (inputMonth < 1 || inputMonth > 12) {
    alert("Expiry month must be between 01 and 12.");
    e.preventDefault();
    return;
  }

  const now = new Date();
  const currentMonth = now.getMonth() + 1;
  const currentYear = now.getFullYear();

  if (inputYear < currentYear || (inputYear === currentYear && inputMonth < currentMonth)) {
    alert("Card expiry date is in the past.");
    e.preventDefault();
  }
});
</script>

</body>
</html>
