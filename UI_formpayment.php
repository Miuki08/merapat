<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login_register.php");
    exit();
}

// Cek apakah role user adalah customer
if ($_SESSION['role'] != 'customer') {
    // Jika bukan customer, redirect ke dashboard yang sesuai
    if ($_SESSION['role'] == 'manager' || $_SESSION['role'] == 'officer') {
        header("Location: dasboard.php");
    } else {
        // Role tidak dikenali, redirect ke halaman login
        header("Location: user_login_register.php");
    }
    exit();
}

$name = $_SESSION['name'];
$user_id = $_SESSION['user_id']; 
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

if ($booking_id === 0) {
    header("Location: UI_scadule.php");
    exit();
}

// Include class booking dan meeting
require_once 'booking.php';
require_once 'meeting_admin.php';

// Ambil data booking
$booking = new booking();
$booking_data = $booking->getBookingById($booking_id);

// Pastikan booking ini milik user yang login
if (!$booking_data || $booking_data['user_id'] != $user_id) {
    header("Location: UI_scadule.php");
    exit();
}

// Ambil data ruangan
$meet = new Meet();
$room = $meet->getRoomByID($booking_data['room_id']);

// Format tanggal dan waktu
$start_time = date('d M Y H:i', strtotime($booking_data['start_time']));
$end_time = date('H:i', strtotime($booking_data['end_time']));
$booking_period = $start_time . ' - ' . $end_time;

// Hitung DP (misalnya 30% dari total harga)
$dp_percentage = 0.3; // 30%
$dp_amount = $booking_data['total_price'] * $dp_percentage;

// QR code images for each payment method
$qr_codes = [
    'DANA' => 'qrcode_dana.png',
    'BCA' => 'qrcode_bca.png',
    'GOPAY' => 'qrcode_gopay.png',
    'MANDIRI' => 'qrcode_mandiri.png'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="logoweb.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>MERAPAT | PAYMENT</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        header {
            width: 100%;
            background: linear-gradient(135deg, #6a1b9a, #9c27b0);
            color: white;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(106,27,154,0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        header .header-left button {
            background: none;
            border: none;
            color: white;
            font-size: 1.5em;
            cursor: pointer;
        }

        header .header-left h1 {
            margin: 0;
            font-size: 1.5em;
        }

        section {
            display: flex;
            justify-content: space-between;
            padding: 30px;
            gap: 30px;
        }

        section .information {
            flex: 1;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }

        section .information img {
            width: 100%;
            max-width: 300px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        section .information h2 {
            font-size: 1.5em;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        section .information p {
            font-size: 1em;
            color: #7f8c8d;
            margin: 10px 0;
        }

        section .form {
            flex: 1;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }

        section .form p {
            font-size: 1em;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        section .form .field {
            margin-bottom: 20px;
        }

        section .form .field label {
            display: block;
            font-size: 1em;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        section .form .field input,
        section .form .field select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
        }

        section .form button {
            width: 100%;
            padding: 10px;
            background-color: #9c27b0;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        section .form button:hover {
            background-color: #6a1b9a;
        }
        
        .dp-amount {
            font-size: 1.2em;
            font-weight: bold;
            color: #27ae60;
            margin-top: 10px;
        }
        
        .qr-code-container {
            text-align: center;
            margin-top: 20px;
            display: none;
        }
        
        .qr-code-container img {
            max-width: 200px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            background: white;
        }
        
        .qr-code-container p {
            margin-top: 10px;
            font-size: 0.9em;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <button onclick="window.history.back()"><i class="fa-solid fa-arrow-left"></i></button>
            <h1>Membayar Booking Room</h1>
        </div>
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($name); ?>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
            </ul>
        </div>
    </header>
    <section>
        <div class="information">
            <img src="rm1.jpg" alt="ruangan yang kamu pesan">
            <h2>Detail Pemesanan</h2>
            <p>Nama Pemesan: <?php echo htmlspecialchars($name); ?></p>
            <p>Ruangan Yang Dipesan: <?php echo htmlspecialchars($room['room_name']); ?></p>
            <p>Lokasi: <?php echo htmlspecialchars($room['location']); ?></p>
            <p>Tanggal dan Waktu: <?php echo $booking_period; ?></p>
            <p>Durasi: <?php 
                $duration = (strtotime($booking_data['end_time']) - strtotime($booking_data['start_time'])) / 3600;
                echo ceil($duration) . ' jam';
            ?></p>
            <p>Total Harga: Rp. <?php echo number_format($booking_data['total_price'], 0, ',', '.'); ?></p>
            <div class="dp-amount">
                DP Yang Harus Dibayar: Rp. <?php echo number_format($dp_amount, 0, ',', '.'); ?>
            </div>
        </div>
        <div class="form">
            <form action="payment_action.php?action=add" method="post" enctype="multipart/form-data">
                <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                <input type="hidden" name="amount" value="<?php echo $dp_amount; ?>">
                
                <p>Download file Perjanjian <a href="README.MD" download="">klik disini</a> pastikan isi semua data yang ada di dalamnya</p>
                <p>Jika terdapat surat resmi lampirkan sebagai halaman setelahnya</p>
                <div class="field">
                    <label for="user_file">Upload File Perjanjian disini</label>
                    <input type="file" name="user_file" id="user_file" required>
                </div>
                <div class="field">
                    <label for="payment_method">Pilih Metode Pembayaran</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="DANA">DANA</option>
                        <option value="BCA">BCA</option>
                        <option value="GOPAY">GOPAY</option>
                        <option value="MANDIRI">MANDIRI</option>
                    </select>
                </div>
                
                <!-- QR Code Container -->
                <div id="qrCodeContainer" class="qr-code-container">
                    <img id="qrCodeImage" src="" alt="QR Code">
                    <p id="qrCodeInstruction">Scan QR code untuk melakukan pembayaran</p>
                </div>
                
                <button type="submit">Bayar</button>
            </form>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            const paymentMethod = this.value;
            const qrCodeContainer = document.getElementById('qrCodeContainer');
            const qrCodeImage = document.getElementById('qrCodeImage');
            
            if (paymentMethod) {
                // Show the QR code container
                qrCodeContainer.style.display = 'block';
                
                // Set the appropriate QR code image based on payment method
                switch(paymentMethod) {
                    case 'DANA':
                        qrCodeImage.src = 'dana.jpg';
                        break;
                    case 'BCA':
                        qrCodeImage.src = 'dana.jpg';
                        break;
                    case 'GOPAY':
                        qrCodeImage.src = 'dana.jpg';
                        break;
                    case 'MANDIRI':
                        qrCodeImage.src = 'dana.jpg';
                        break;
                    default:
                        qrCodeContainer.style.display = 'none';
                }
            } else {
                // Hide the QR code container if no method is selected
                qrCodeContainer.style.display = 'none';
            }
        });
    </script>
</body>
</html>