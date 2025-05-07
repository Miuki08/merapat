<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login_register.php");
    exit();
}

// Check if user is customer
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

// Include required classes
require_once 'booking.php';
require_once 'meeting_admin.php';
require_once 'payment.php';

// Get booking data
$booking = new booking();
$booking_data = $booking->getBookingById($booking_id);

// Verify booking belongs to user
if (!$booking_data || $booking_data['user_id'] != $user_id) {
    header("Location: UI_scadule.php");
    exit();
}

// Get room data
$meet = new Meet();
$room = $meet->getRoomByID($booking_data['room_id']);

// Get payment data
$payment = new Payment();
$payment_data = $payment->getPaymentByBookingId($booking_id)->fetch_assoc();

// Format dates and times
$start_time = date('d M Y H:i', strtotime($booking_data['start_time']));
$end_time = date('H:i', strtotime($booking_data['end_time']));
$booking_period = $start_time . ' - ' . $end_time;
$payment_date = $payment_data ? date('d M Y H:i', strtotime($payment_data['payment_date'])) : 'N/A';

// Calculate duration
$duration = (strtotime($booking_data['end_time']) - strtotime($booking_data['start_time'])) / 3600;
$duration_display = ceil($duration) . ' jam';

// Prepare data for download
$download_data = [
    'Nama Pemesan' => $name,
    'Ruangan' => $room['room_name'],
    'Lokasi' => $room['location'],
    'Tanggal dan Waktu' => $booking_period,
    'Durasi' => $duration_display,
    'Status Booking' => $booking_data['status'],
    'Total Harga' => 'Rp. ' . number_format($booking_data['total_price'], 0, ',', '.'),
    'Tanggal Pembayaran' => $payment_date,
    'Metode Pembayaran' => $payment_data['payment_method'] ?? 'N/A',
    'Jumlah Dibayar' => 'Rp. ' . number_format($payment_data['amount'] ?? 0, 0, ',', '.'),
    'Status Pembayaran' => $payment_data ? 'Lunas' : 'Belum Dibayar'
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
    <!-- PDF download library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <title>MERAPAT | Detail Booking</title>
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
            text-align: left;
            padding: 0 20px;
        }

        section .information .info-label {
            font-weight: bold;
            color: #2c3e50;
            display: inline-block;
            width: 180px;
        }

        section .document {
            flex: 1;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }

        section .document h2 {
            font-size: 1.5em;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }

        section .document .document-preview {
            border: 1px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        section .document .document-preview i {
            font-size: 3em;
            color: #7f8c8d;
        }

        section .document .download-btn {
            width: 100%;
            padding: 10px;
            background-color: #9c27b0;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        section .document .download-btn:hover {
            background-color: #6a1b9a;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            margin-left: 10px;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .payment-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }

        .payment-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .payment-unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <button onclick="window.history.back()"><i class="fa-solid fa-arrow-left"></i></button>
            <h1>Detail Booking</h1>
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
            
            <p><span class="info-label">Nama Pemesan:</span> <?php echo htmlspecialchars($name); ?></p>
            <p><span class="info-label">Ruangan:</span> <?php echo htmlspecialchars($room['room_name']); ?></p>
            <p><span class="info-label">Lokasi:</span> <?php echo htmlspecialchars($room['location']); ?></p>
            <p><span class="info-label">Nomor Telephone Ruangan:</span> +62 851-7160-0930</p>
            <p><span class="info-label">Tanggal dan Waktu:</span> <?php echo $booking_period; ?></p>
            <p><span class="info-label">Durasi:</span> <?php echo $duration_display; ?></p>
            <p><span class="info-label">Status Booking:</span> 
                <span class="status-<?php echo strtolower($booking_data['status']); ?> status-badge">
                    <?php echo htmlspecialchars($booking_data['status']); ?>
                </span>
            </p>
            <p><span class="info-label">Total Harga:</span> Rp. <?php echo number_format($booking_data['total_price'], 0, ',', '.'); ?></p>
            
            <h2 style="margin-top: 30px;">Detail Pembayaran</h2>
            <p><span class="info-label">Tanggal Pembayaran:</span> <?php echo $payment_date; ?></p>
            <p><span class="info-label">Metode Pembayaran:</span> <?php echo htmlspecialchars($payment_data['payment_method'] ?? 'N/A'); ?></p>
            <p><span class="info-label">Jumlah Dibayar:</span> Rp. <?php echo number_format($payment_data['amount'] ?? 0, 0, ',', '.'); ?></p>
            <p><span class="info-label">Status Pembayaran:</span> 
                <span class="payment-<?php echo $payment_data ? 'paid' : 'unpaid'; ?> payment-status">
                    <?php echo $payment_data ? 'Lunas' : 'Belum Dibayar'; ?>
                </span>
            </p>
        </div>
        <div class="document">
            <h2>Dokumen</h2>
            
            <div class="document-preview">
                <?php if ($payment_data && !empty($payment_data['user_file'])): ?>
                    <iframe src="<?php echo htmlspecialchars($payment_data['user_file']); ?>#toolbar=0&navpanes=0" width="100%" height="500px" style="border: none;"></iframe>
                <?php else: ?>
                    <i class="fas fa-file-alt"></i>
                    <p>Tidak ada dokumen terupload</p>
                <?php endif; ?>
            </div>
            
            <button class="download-btn" onclick="downloadAsPDF()">
                <i class="fas fa-download"></i>
                Download Detail Booking (PDF)
            </button>
            
            <?php if ($payment_data && !empty($payment_data['user_file'])): ?>
                <a href="<?php echo htmlspecialchars($payment_data['user_file']); ?>" download class="download-btn" style="margin-top: 10px; text-decoration: none;">
                    <i class="fas fa-file-download"></i>
                    Download Dokumen Asli
                </a>
            <?php endif; ?>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize jsPDF
        const { jsPDF } = window.jspdf;
        
        function downloadAsPDF() {
            // Create new PDF document in landscape for better layout
            const doc = new jsPDF('l', 'mm', 'a4');
            
            // Set corporate colors
            const primaryColor = [106, 27, 154]; // Purple
            const secondaryColor = [156, 39, 176]; // Light purple
            const darkText = [44, 62, 80]; // Dark blue-gray
            const lightText = [127, 140, 141]; // Gray
            
            // Add header with logo and title
            doc.setFillColor(primaryColor[0], primaryColor[1], primaryColor[2]);
            doc.rect(0, 0, 297, 20, 'F');
            doc.setFontSize(16);
            doc.setTextColor(255, 255, 255);
            doc.setFont('helvetica', 'bold');
            doc.text('MERAPAT - Booking Confirmation', 148, 15, { align: 'center' });
            
            // Add document information section
            doc.setFontSize(10);
            doc.setTextColor(lightText[0], lightText[1], lightText[2]);
            doc.text(`Document ID: BK-${<?php echo $booking_id; ?>}`, 20, 30);
            doc.text(`Generated: ${new Date().toLocaleString()}`, 260, 30, { align: 'right' });
            
            // Add divider line
            doc.setDrawColor(primaryColor[0], primaryColor[1], primaryColor[2]);
            doc.setLineWidth(0.5);
            doc.line(20, 35, 277, 35);
            
            // Set starting position for content
            let y = 45;
            
            // Add booking summary section
            doc.setFontSize(14);
            doc.setTextColor(darkText[0], darkText[1], darkText[2]);
            doc.setFont('helvetica', 'bold');
            doc.text('BOOKING SUMMARY', 20, y);
            y += 10;
            
            // Create booking summary table
            const summaryData = [
                ['Booking ID', `BK-${<?php echo $booking_id; ?>}`],
                ['Customer Name', `<?php echo addslashes($name); ?>`],
                ['Booking Status', `<?php echo $booking_data['status']; ?>`],
                ['Payment Status', `<?php echo $payment_data ? 'Paid' : 'Pending'; ?>`]
            ];
            
            doc.setFontSize(10);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(darkText[0], darkText[1], darkText[2]);
            
            summaryData.forEach(row => {
                doc.setFont('helvetica', 'bold');
                doc.text(`${row[0]}:`, 20, y);
                doc.setFont('helvetica', 'normal');
                doc.text(row[1], 70, y);
                y += 7;
            });
            
            y += 10;
            
            // Add room details section
            doc.setFontSize(14);
            doc.setFont('helvetica', 'bold');
            doc.text('ROOM DETAILS', 20, y);
            y += 10;
            
            const roomData = [
                ['Room Name', `<?php echo addslashes($room['room_name']); ?>`],
                ['Location', `<?php echo addslashes($room['location']); ?>`],
                ['Contact', '+62 851-7160-0930'],
                ['Booking Period', `<?php echo $booking_period; ?>`],
                ['Duration', `<?php echo $duration_display; ?>`]
            ];
            
            doc.setFontSize(10);
            doc.setFont('helvetica', 'normal');
            
            roomData.forEach(row => {
                doc.setFont('helvetica', 'bold');
                doc.text(`${row[0]}:`, 20, y);
                doc.setFont('helvetica', 'normal');
                doc.text(row[1], 70, y);
                y += 7;
            });
            
            y += 10;
            
            // Add payment details section
            doc.setFontSize(14);
            doc.setFont('helvetica', 'bold');
            doc.text('PAYMENT DETAILS', 20, y);
            y += 10;
            
            const paymentData = [
                ['Total Amount', `Rp. <?php echo number_format($booking_data['total_price'], 0, ',', '.'); ?>`],
                ['Payment Date', `<?php echo $payment_date; ?>`],
                ['Payment Method', `<?php echo htmlspecialchars($payment_data['payment_method'] ?? 'N/A'); ?>`],
                ['Amount Paid', `Rp. <?php echo number_format($payment_data['amount'] ?? 0, 0, ',', '.'); ?>`],
                ['Payment Status', `<?php echo $payment_data ? 'Paid' : 'Pending'; ?>`]
            ];
            
            doc.setFontSize(10);
            doc.setFont('helvetica', 'normal');
            
            paymentData.forEach(row => {
                doc.setFont('helvetica', 'bold');
                doc.text(`${row[0]}:`, 20, y);
                doc.setFont('helvetica', 'normal');
                doc.text(row[1], 70, y);
                y += 7;
            });
            
            y += 15;
            
            // Add terms and conditions
            doc.setFontSize(11);
            doc.setFont('helvetica', 'bold');
            doc.setTextColor(primaryColor[0], primaryColor[1], primaryColor[2]);
            doc.text('TERMS AND CONDITIONS', 20, y);
            y += 7;
            
            doc.setFontSize(9);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(darkText[0], darkText[1], darkText[2]);
            
            const terms = [
                '1. This document serves as official proof of booking.',
                '2. Please present this document when checking in at the location.',
                '3. Cancellations must be made at least 24 hours before the booking time.',
                '4. No-shows will be charged 50% of the total booking amount.',
                '5. Any damages to the room will be charged to the booking customer.',
                '6. For any inquiries, please contact our customer service.'
            ];
            
            terms.forEach(term => {
                doc.text(term, 25, y);
                y += 6;
            });
            
            y += 10;
            
            // Add QR code placeholder (would be replaced with actual QR generation in production)
            doc.setFillColor(240, 240, 240);
            doc.rect(200, 60, 70, 70, 'F');
            doc.setFontSize(8);
            doc.setTextColor(lightText[0], lightText[1], lightText[2]);
            doc.text('Scan for verification', 200, 135, { align: 'center' });
            doc.setFont('helvetica', 'bold');
            doc.text('BK-<?php echo $booking_id; ?>', 200, 140, { align: 'center' });
            
            // Add footer
            doc.setFillColor(primaryColor[0], primaryColor[1], primaryColor[2]);
            doc.rect(0, 200, 297, 10, 'F');
            doc.setFontSize(8);
            doc.setTextColor(255, 255, 255);
            doc.text('MERAPAT Meeting Room Booking System', 148, 205, { align: 'center' });
            doc.setTextColor(200, 200, 200);
            doc.text('This is an auto-generated document. Please do not reply.', 148, 210, { align: 'center' });
            
            // Save the PDF with corporate naming convention
            doc.save(`MERAPAT_Booking_${<?php echo $booking_id; ?>}_${<?php echo $user_id; ?>}.pdf`);
        }
    </script>
</body>
</html>