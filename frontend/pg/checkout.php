<?php
include('../config/constants.php');

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
$amount = isset($_GET['amount']) ? (float)$_GET['amount'] : 0;

if (!$order_id || $amount <= 0) {
    echo "Invalid Order or Amount";
    exit;
}

// Generate the UPI URI
// Use the configured UPI ID from constants or fallback to a dummy if empty/not set
$upi_id = defined('RECEIVE_UPI_ID') && !empty(RECEIVE_UPI_ID) ? RECEIVE_UPI_ID : "pasar-kita@okicici";
$merchant_name = defined('RECEIVE_UPI_NAME') && !empty(RECEIVE_UPI_NAME) ? urlencode(RECEIVE_UPI_NAME) : urlencode("Pasar-Kita Food Orders");
$currency = "INR";
$upi_uri = "upi://pay?pa={$upi_id}&pn={$merchant_name}&am={$amount}&cu={$currency}";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Secure Payment Gateway | Pasar-kita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f6fb;
            font-family: 'Nunito', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .pg-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 450px;
            padding: 30px;
            text-align: center;
        }
        .pg-header {
            margin-bottom: 24px;
        }
        .pg-header h2 {
            font-weight: 800;
            color: #0e1f4d;
            margin-bottom: 5px;
        }
        .pg-header p {
            color: #6c757d;
            font-size: 0.95rem;
            margin: 0;
        }
        .amount-display {
            font-size: 2.2rem;
            font-weight: 800;
            color: #f2a208;
            margin: 15px 0;
        }
        .qr-wrapper {
            background: #fff;
            border: 2px dashed #e1e7f2;
            border-radius: 16px;
            padding: 20px;
            display: inline-block;
            margin: 10px 0 20px;
        }
        .qr-wrapper img {
            max-width: 100%;
            height: auto;
        }
        .apps-supported {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 25px;
            align-items: center;
        }
        .apps-supported img {
            height: 25px;
            object-fit: contain;
        }
        .apps-supported span {
            font-weight: 700;
            color: #0e1f4d;
            font-size: 0.9rem;
        }
        .utr-form {
            text-align: left;
            background: #f9fbff;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e1e7f2;
        }
        .utr-form label {
            font-weight: 700;
            color: #0e1f4d;
            margin-bottom: 8px;
            display: block;
        }
        .utr-form input {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            width: 100%;
            margin-bottom: 15px;
        }
        .btn-pay {
            background: #f2a208;
            color: #fff;
            font-weight: 800;
            border-radius: 8px;
            padding: 12px;
            width: 100%;
            border: none;
            transition: 0.3s;
        }
        .btn-pay:hover {
            background: #e69500;
        }
    </style>
</head>
<body>

<div class="pg-container">
    <div class="pg-header">
        <h2>Secure Payment</h2>
        <p>Scan the QR code to pay using any UPI app</p>
    </div>

    <div class="amount-display">
        ₹<?php echo number_format($amount, 2); ?>
    </div>

    <div class="qr-wrapper" id="qrcode">
        <!-- QR code will be generated here -->
    </div>

    <div class="apps-supported">
        <span>Supports:</span>
        <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/UPI-Logo.png" alt="UPI">
        <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/Google_Pay_Logo.svg" alt="GPay" style="height:20px;">
        <img src="https://upload.wikimedia.org/wikipedia/commons/7/71/PhonePe_Logo.svg" alt="PhonePe">
    </div>

    <form class="utr-form" action="process.php" method="POST">
        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
        <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount); ?>">
        
        <label for="utr">Enter UTR / Transaction ID</label>
        <input type="text" id="utr" name="utr" placeholder="e.g. 312456789012" required>
        <small style="display:block; margin-top:-10px; margin-bottom:15px; color:#6c757d; font-size:0.8rem;">
            After scanning and paying, please enter your 12-digit UTR here to confirm.
            <br><a href="#" data-bs-toggle="modal" data-bs-target="#howItWorksModal" style="color: #f2a208; text-decoration: none; font-weight: bold; margin-top: 5px; display: inline-block;">How does this work?</a>
        </small>
        
        <button type="submit" class="btn-pay">Confirm Payment</button>
    </form>
</div>

<!-- How it Works Modal -->
<div class="modal fade" id="howItWorksModal" tabindex="-1" aria-labelledby="howItWorksModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="howItWorksModalLabel" style="color: #0e1f4d; font-weight: 800;">How to Pay</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="font-family: 'Nunito', sans-serif;">
        <p><strong>Step 1:</strong> Open your favorite UPI app (GPay, PhonePe, Paytm, etc.).</p>
        <p><strong>Step 2:</strong> Scan the secure QR code shown on the screen.</p>
        <p><strong>Step 3:</strong> The exact amount will be automatically filled in. Enter your UPI PIN to transfer the money securely via the official NPCI banking network.</p>
        <p><strong>Step 4:</strong> After the payment succeeds, your app will give you a <strong>12-digit UTR</strong> (Transaction ID). Enter that number back on this screen to instantly confirm your order!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Got it!</button>
      </div>
    </div>
  </div>
</div>

<!-- Include Bootstrap JS and qrcode.js library -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // Generate QR Code dynamically
    var upiUri = "<?php echo $upi_uri; ?>";
    var qrcode = new QRCode(document.getElementById("qrcode"), {
        text: upiUri,
        width: 200,
        height: 200,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
</script>
</body>
</html>
