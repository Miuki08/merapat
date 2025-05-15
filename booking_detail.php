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
require_once 'payment.php';

$booking = new booking();
$booking_data = $booking->getBookingById($booking_id);

if (!$booking_data || $booking_data['user_id'] != $user_id) {
    header("Location: UI_scadule.php");
    exit();
}

$meet = new Meet();
$room = $meet->getRoomByID($booking_data['room_id']);

$payment = new Payment();
$payment_data = $payment->getPaymentByBookingId($booking_id)->fetch_assoc();

$start_time = date('d M Y H:i', strtotime($booking_data['start_time']));
$end_time = date('H:i', strtotime($booking_data['end_time']));
$booking_period = $start_time . ' - ' . $end_time;
$payment_date = $payment_data ? date('d M Y H:i', strtotime($payment_data['payment_date'])) : 'N/A';

$duration = (strtotime($booking_data['end_time']) - strtotime($booking_data['start_time'])) / 3600;
$duration_display = ceil($duration) . ' jam';

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
    <link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Cinzel+Decorative:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <title>MERAPAT | Detail Booking</title>
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
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .information h2 {
            font-size: 1.5em;
            color: #4A002A;
            margin-bottom: 15px;
            font-family: 'Cinzel Decorative', serif;
            text-align: center;
        }

        .information p {
            font-size: 1em;
            color: #4B4B4B;
            margin: 10px 0;
            text-align: left;
            padding: 0 20px;
        }

        .info-label {
            font-weight: bold;
            color: #4A002A;
            display: inline-block;
            width: 180px;
        }

        /* Document Card */
        .document {
            flex: 1;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 25px;
            border: 1px solid #F1E9E9;
        }

        .document:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            border-color: #C2AE6D;
        }

        .document h2 {
            font-size: 1.5em;
            color: #4A002A;
            margin-bottom: 20px;
            text-align: center;
            font-family: 'Cinzel Decorative', serif;
        }

        .document-preview {
            border: 2px dashed #C2AE6D;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #F1E9E9;
        }

        .document-preview i {
            font-size: 3em;
            color: #4A002A;
        }

        .download-btn {
            width: 100%;
            padding: 12px;
            background-color: #4A002A;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .download-btn:hover {
            background-color: #6a1b9a;
            transform: translateY(-2px);
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            margin-left: 10px;
        }

        .status-confirmed {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .status-pending {
            background-color: #fff8e1;
            color: #ff8f00;
            border: 1px solid #ffecb3;
        }

        .status-cancelled {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .payment-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }

        .payment-paid {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .payment-unpaid {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
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
            
            .information, .document {
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
            
            .info-label {
                width: 140px;
            }
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
            
            <p><span class="info-label">Nama Pemesan:</span> <?= htmlspecialchars($name); ?></p>
            <p><span class="info-label">Ruangan:</span> <?= htmlspecialchars($room['room_name']); ?></p>
            <p><span class="info-label">Lokasi:</span> <?= htmlspecialchars($room['location']); ?></p>
            <p><span class="info-label">Nomor Telephone:</span> +62 851-7160-0930</p>
            <p><span class="info-label">Tanggal/Waktu:</span> <?= $booking_period; ?></p>
            <p><span class="info-label">Durasi:</span> <?= $duration_display; ?></p>
            <p><span class="info-label">Status Booking:</span> 
                <span class="status-<?= strtolower($booking_data['status']); ?> status-badge">
                    <?= htmlspecialchars($booking_data['status']); ?>
                </span>
            </p>
            <p><span class="info-label">Total Harga:</span> Rp. <?= number_format($booking_data['total_price'], 0, ',', '.'); ?></p>
            
            <h2 style="margin-top: 30px;">Detail Pembayaran</h2>
            <p><span class="info-label">Tanggal Pembayaran:</span> <?= $payment_date; ?></p>
            <p><span class="info-label">Metode Pembayaran:</span> <?= htmlspecialchars($payment_data['payment_method'] ?? 'N/A'); ?></p>
            <p><span class="info-label">Jumlah Dibayar:</span> Rp. <?= number_format($payment_data['amount'] ?? 0, 0, ',', '.'); ?></p>
            <p><span class="info-label">Status Pembayaran:</span> 
                <span class="payment-<?= $payment_data ? 'paid' : 'unpaid'; ?> payment-status">
                    <?= $payment_data ? 'Lunas' : 'Belum Dibayar'; ?>
                </span>
            </p>
        </div>
        
        <div class="document">
            <h2>Dokumen</h2>
            
            <div class="document-preview">
                <?php if ($payment_data && !empty($payment_data['user_file'])): ?>
                    <iframe src="<?= htmlspecialchars($payment_data['user_file']); ?>#toolbar=0&navpanes=0" width="100%" height="500px" style="border: none;"></iframe>
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
                <a href="<?= htmlspecialchars($payment_data['user_file']); ?>" download class="download-btn" style="margin-top: 10px; text-decoration: none;">
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
            const primaryColor = [74, 0, 42]; // Dark purple (#4A002A)
            const secondaryColor = [194, 174, 109]; // Gold (#C2AE6D)
            const darkText = [74, 0, 42]; // Dark purple
            const lightText = [75, 75, 75]; // Dark gray
            
            // Add header with logo and title
            doc.setFillColor(primaryColor[0], primaryColor[1], primaryColor[2]);
            doc.rect(0, 0, 297, 20, 'F');
            doc.setFontSize(16);
            doc.setTextColor(255, 255, 255);
            doc.setFont('helvetica', 'bold');
            doc.text('MERAPAT - Booking Confirmation', 148, 15, { align: 'center' });
            
            // Add document information section
            doc.setFontSize(10);
            doc.setTextColor(255, 255, 255);
            doc.text(`Document ID: BK-${<?= $booking_id; ?>}`, 20, 30);
            doc.text(`Generated: ${new Date().toLocaleString()}`, 260, 30, { align: 'right' });
            
            // Add divider line
            doc.setDrawColor(secondaryColor[0], secondaryColor[1], secondaryColor[2]);
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
                ['Booking ID', `BK-${<?= $booking_id; ?>}`],
                ['Customer Name', `<?= addslashes($name); ?>`],
                ['Booking Status', `<?= $booking_data['status']; ?>`],
                ['Payment Status', `<?= $payment_data ? 'Paid' : 'Pending'; ?>`]
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
                ['Room Name', `<?= addslashes($room['room_name']); ?>`],
                ['Location', `<?= addslashes($room['location']); ?>`],
                ['Contact', '+62 851-7160-0930'],
                ['Booking Period', `<?= $booking_period; ?>`],
                ['Duration', `<?= $duration_display; ?>`]
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
                ['Total Amount', `Rp. <?= number_format($booking_data['total_price'], 0, ',', '.'); ?>`],
                ['Payment Date', `<?= $payment_date; ?>`],
                ['Payment Method', `<?= htmlspecialchars($payment_data['payment_method'] ?? 'N/A'); ?>`],
                ['Amount Paid', `Rp. <?= number_format($payment_data['amount'] ?? 0, 0, ',', '.'); ?>`],
                ['Payment Status', `<?= $payment_data ? 'Paid' : 'Pending'; ?>`]
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
                '1. Dokumen ini merupakan bukti pemesanan resmi.',
                '2. Harap tunjukkan dokumen ini saat check-in di lokasi.',
                '3. Pembatalan harus dilakukan minimal 24 jam sebelum waktu booking.',
                '4. Tidak hadir akan dikenakan biaya 50% dari total pemesanan.',
                '5. Kerusakan pada ruangan akan menjadi tanggung jawab pemesan.',
                '6. Untuk pertanyaan, hubungi layanan pelanggan kami.'
            ];
            
            terms.forEach(term => {
                doc.text(term, 25, y);
                y += 6;
            });
            
            y += 10;
            
            // Add QR code placeholder
            doc.setFillColor(241, 233, 233); // #F1E9E9
            doc.rect(200, 60, 70, 70, 'F');
            doc.setTextColor(secondaryColor[0], secondaryColor[1], secondaryColor[2]);
            doc.setFontSize(8);
            doc.text('Scan untuk verifikasi', 200, 135, { align: 'center' });
            doc.setFont('helvetica', 'bold');
            doc.setTextColor(primaryColor[0], primaryColor[1], primaryColor[2]);
            doc.text('BK-<?= $booking_id; ?>', 200, 140, { align: 'center' });
            
            // Add footer
            doc.setFillColor(primaryColor[0], primaryColor[1], primaryColor[2]);
            doc.rect(0, 200, 297, 10, 'F');
            doc.setFontSize(8);
            doc.setTextColor(255, 255, 255);
            doc.text('Sistem Pemesanan Ruangan MERAPAT', 148, 205, { align: 'center' });
            doc.setTextColor(200, 200, 200);
            doc.text('Dokumen ini dibuat otomatis. Mohon tidak membalas.', 148, 210, { align: 'center' });
            
            // Save the PDF
            doc.save(`MERAPAT_Booking_${<?= $booking_id; ?>}_${<?= $user_id; ?>}.pdf`);
        }
    </script>
</body>
</html>