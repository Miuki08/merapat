<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login_register.php");
    exit();
}

if ($_SESSION['role'] != 'customer') {
    if ($_SESSION['role'] == 'manager' || $_SESSION['role'] == 'officer') {
        header("Location: dasboard.php");
    } else {
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

require_once 'booking.php';
require_once 'meeting_admin.php';

$booking = new booking();
$booking_data = $booking->getBookingById($booking_id);

if (!$booking_data || $booking_data['user_id'] != $user_id) {
    header("Location: UI_scadule.php");
    exit();
}

$meet = new Meet();
$room = $meet->getRoomByID($booking_data['room_id']);

$start_time = date('d M Y H:i', strtotime($booking_data['start_time']));
$end_time = date('H:i', strtotime($booking_data['end_time']));
$booking_period = $start_time . ' - ' . $end_time;

$dp_percentage = 0.3;
$dp_amount = $booking_data['total_price'] * $dp_percentage;

$qr_codes = [
    'DANA' => 'dana.jpg',
    'BCA' => 'dana.jpg',
    'GOPAY' => 'dana.jpg',
    'MANDIRI' => 'dana.jpg'
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
    <link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Cinzel+Decorative:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>MERAPAT | PAYMENT</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F1E9E9;
            color: #4A002A;
            margin-top: 70px;
        }

        /* Header Styles */
        header {
            width: 100%;
            background: linear-gradient(135deg, #4A002A, #6a1b9a);
            color: white;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(74, 0, 42, 0.2);
            position: fixed;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-left button {
            background: none;
            border: none;
            color: white;
            font-size: 1.5em;
            cursor: pointer;
        }

        .header-left h1 {
            margin: 0;
            font-size: 1.5em;
            font-family: 'Cinzel Decorative', serif;
        }

        /* Main Content */
        section {
            display: flex;
            justify-content: space-between;
            padding: 30px;
            gap: 30px;
        }

        /* Information Card */
        .information {
            flex: 1;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 25px;
            text-align: center;
            border: 1px solid #F1E9E9;
        }

        .information:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            border-color: #C2AE6D;
        }

        .information img {
            width: 100%;
            max-width: 300px;
            border-radius: 15px;
            margin-bottom: 20px;
            border: 3px solid #C2AE6D;
        }

        .information h2 {
            font-size: 1.5em;
            color: #4A002A;
            margin-bottom: 15px;
            font-family: 'Cinzel Decorative', serif;
        }

        .information p {
            font-size: 1em;
            color: #4B4B4B;
            margin: 10px 0;
        }

        .dp-amount {
            font-size: 1.2em;
            font-weight: bold;
            color: #4A002A;
            margin-top: 10px;
            padding: 10px;
            background-color: #F1E9E9;
            border-radius: 8px;
            border: 1px dashed #C2AE6D;
        }

        /* Form Styles */
        .form {
            flex: 1;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 25px;
            border: 1px solid #F1E9E9;
        }

        .form:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            border-color: #C2AE6D;
        }

        .form p {
            font-size: 1em;
            color: #4A002A;
            margin-bottom: 15px;
        }

        .form a {
            color: #6a1b9a;
            text-decoration: none;
            font-weight: 500;
        }

        .form a:hover {
            color: #4A002A;
            text-decoration: underline;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            font-size: 1em;
            color: #4A002A;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .field input,
        .field select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .field input:focus,
        .field select:focus {
            border-color: #C2AE6D;
            outline: none;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #4A002A;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            background-color: #6a1b9a;
            transform: translateY(-2px);
        }

        /* QR Code Container */
        .qr-code-container {
            text-align: center;
            margin-top: 20px;
            display: none;
            padding: 15px;
            background-color: #F1E9E9;
            border-radius: 8px;
            border: 1px solid #C2AE6D;
        }

        .qr-code-container img {
            max-width: 200px;
            border-radius: 8px;
            padding: 10px;
            background: white;
        }

        .qr-code-container p {
            margin-top: 10px;
            font-size: 0.9em;
            color: #4B4B4B;
        }

        /* File Input Styling */
        .file-input-container {
            position: relative;
            margin-bottom: 20px;
        }

        .custom-file-input {
            display: none;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            background-color: #F1E9E9;
            border: 2px dashed #C2AE6D;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            color: #4A002A;
        }

        .file-input-label:hover {
            background-color: #e8e0c5;
        }

        .file-input-label i {
            margin-right: 10px;
            font-size: 1.2em;
            color: #4A002A;
        }

        .file-name {
            margin-top: 8px;
            font-size: 0.9em;
            color: #4A002A;
            text-align: center;
            word-break: break-all;
        }

        .file-selected {
            background-color: #e8f5e9;
            border-color: #4A002A;
            color: #4A002A;
        }

        .file-selected i {
            color: #4A002A;
        }

        /* Dropdown Styles */
        .dropdown-toggle {
            background-color: transparent !important;
            border: 2px solid #C2AE6D !important;
            color: white !important;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dropdown-toggle::after {
            margin-left: 8px;
            vertical-align: middle;
            border-top-color: #C2AE6D;
        }

        .dropdown-toggle:hover {
            background-color: rgba(194, 174, 109, 0.2) !important;
            transform: translateY(-2px);
        }

        .dropdown-menu {
            background-color: #4A002A;
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            padding: 10px 0;
            min-width: 200px;
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 5px;
            z-index: 1001;
        }

        .dropdown-item {
            color: white !important;
            padding: 10px 20px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        .dropdown-item:hover {
            background-color: rgba(194, 174, 109, 0.3) !important;
            color: #C2AE6D !important;
            padding-left: 25px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            section {
                flex-direction: column;
                padding: 20px;
            }
            
            .information, .form {
                max-width: 100%;
            }
            
            header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .header-left {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <button onclick="window.history.back()"><i class="fa-solid fa-arrow-left"></i></button>
            <h1>Pembayaran Booking</h1>
        </div>
        <div class="dropdown">
            <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle"></i>
                <?php echo htmlspecialchars($name); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
            </ul>
        </div>
    </header>
    
    <section>
        <div class="information">
            <img src="rm1.jpg" alt="<?= htmlspecialchars($room['room_name']); ?>">
            <h2>Detail Pemesanan</h2>
            <p>Nama Pemesan: <?= htmlspecialchars($name); ?></p>
            <p>Ruangan: <?= htmlspecialchars($room['room_name']); ?></p>
            <p>Lokasi: <?= htmlspecialchars($room['location']); ?></p>
            <p>Tanggal/Waktu: <?= $booking_period; ?></p>
            <p>Durasi: <?= ceil((strtotime($booking_data['end_time']) - strtotime($booking_data['start_time'])) / 3600); ?> jam</p>
            <p>Total Harga: Rp. <?= number_format($booking_data['total_price'], 0, ',', '.'); ?></p>
            <div class="dp-amount">
                DP (30%): Rp. <?= number_format($dp_amount, 0, ',', '.'); ?>
            </div>
        </div>
        
        <div class="form">
            <form action="payment_action.php?action=add" method="post" enctype="multipart/form-data">
                <input type="hidden" name="booking_id" value="<?= $booking_id; ?>">
                <input type="hidden" name="amount" value="<?= $dp_amount; ?>">
                
                <p>Download file Perjanjian <a href="README.MD" download>klik disini</a> dan isi semua data yang diperlukan</p>
                <p>Lampirkan surat resmi (jika ada) sebagai halaman berikutnya</p>
                
                <div class="field">
                    <label for="user_file">Upload File Perjanjian</label>
                    <div class="file-input-container">
                        <input type="file" name="user_file" id="user_file" class="custom-file-input" required>
                        <label for="user_file" class="file-input-label" id="fileInputLabel">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Klik untuk mengunggah file</span>
                        </label>
                        <div class="file-name" id="fileName"></div>
                    </div>
                </div>
                
                <div class="field">
                    <label for="payment_method">Metode Pembayaran</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="DANA">DANA</option>
                        <option value="BCA">BCA</option>
                        <option value="GOPAY">GOPAY</option>
                        <option value="MANDIRI">MANDIRI</option>
                    </select>
                </div>
                
                <div id="qrCodeContainer" class="qr-code-container">
                    <img id="qrCodeImage" src="" alt="QR Code">
                    <p id="qrCodeInstruction">Scan QR code untuk pembayaran</p>
                </div>
                
                <button type="submit">Bayar Sekarang</button>
            </form>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Payment method QR code display
        document.getElementById('payment_method').addEventListener('change', function() {
            const paymentMethod = this.value;
            const qrCodeContainer = document.getElementById('qrCodeContainer');
            const qrCodeImage = document.getElementById('qrCodeImage');
            
            if (paymentMethod) {
                qrCodeContainer.style.display = 'block';
                qrCodeImage.src = 'dana.jpg'; // All methods use same image in this example
            } else {
                qrCodeContainer.style.display = 'none';
            }
        });

        // File input styling
        document.getElementById('user_file').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Belum ada file dipilih';
            document.getElementById('fileName').textContent = fileName;
            
            const fileInputLabel = document.getElementById('fileInputLabel');
            if (e.target.files.length > 0) {
                fileInputLabel.classList.add('file-selected');
                fileInputLabel.innerHTML = `<i class="fas fa-check-circle"></i><span>File dipilih</span>`;
            } else {
                fileInputLabel.classList.remove('file-selected');
                fileInputLabel.innerHTML = `<i class="fas fa-cloud-upload-alt"></i><span>Klik untuk mengunggah file</span>`;
            }
        });

        // Drag and drop functionality
        const fileInputLabel = document.getElementById('fileInputLabel');
        
        fileInputLabel.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileInputLabel.style.backgroundColor = '#e8e0c5';
        });
        
        fileInputLabel.addEventListener('dragleave', () => {
            fileInputLabel.style.backgroundColor = '#F1E9E9';
        });
        
        fileInputLabel.addEventListener('drop', (e) => {
            e.preventDefault();
            fileInputLabel.style.backgroundColor = '#F1E9E9';
            document.getElementById('user_file').files = e.dataTransfer.files;
            const event = new Event('change');
            document.getElementById('user_file').dispatchEvent(event);
        });
    </script>
</body>
</html>