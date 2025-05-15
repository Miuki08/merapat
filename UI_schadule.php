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

require_once 'booking.php';
require_once 'payment.php';

$notification = '';
$alert_class = '';
if (isset($_GET['payment'])) {
    switch ($_GET['payment']) {
        case 'success':
            $notification = "Pembayaran berhasil ditambahkan!";
            $alert_class = "alert-success";
            break;
        case 'deleted':
            $notification = "Pembayaran berhasil dihapus!";
            $alert_class = "alert-info";
            break;
    }
}

$name = $_SESSION['name'];
$user_id = $_SESSION['user_id'];

$booking = new booking();
$payment = new Payment();

$result = $booking->getBookingsByUserId($user_id);
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
    <title>MERAPAT | Daftar Pesanan</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F1E9E9;
            margin: 0;
            padding: 0;
        }

        /* Header Sticky */
        header {
            width: 100%;
            background: linear-gradient(135deg, #4A002A, #6a1b9a);
            color: white;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(74, 0, 42, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: #F1E9E9;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            z-index: 999;
            padding-top: 80px;
        }

        .sidebar.open {
            left: 0;
        }

        .sidebar .nav-link {
            color: #4A002A;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }

        .sidebar .nav-link:hover {
            background-color: #C2AE6D;
            color: #4A002A;
            transform: translateX(10px);
        }

        .sidebar .nav-link i {
            width: 30px;
            font-size: 18px;
        }

        /* Main Content */
        .main-content {
            margin-left: 0;
            padding: 30px;
            transition: margin 0.3s ease;
        }

        .sidebar.open + .main-content {
            margin-left: 250px;
        }

        /* Notification Alert */
        .notification-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1100;
            width: 300px;
        }

        /* Card Styling */
        .carto {
            background: white;
            border: 1px solid #e1bee7;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .carto:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(74, 0, 42, 0.1);
            border-color: #C2AE6D;
        }

        .carto img {
            width: 200px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 20px;
            border: 2px solid #C2AE6D;
        }

        .carto-content {
            flex: 1;
        }

        .carto h2 {
            margin: 0 0 10px 0;
            color: #4A002A;
            font-size: 1.3em;
            font-family: 'Cinzel Decorative', serif;
        }

        .carto p {
            margin: 5px 0;
            color: #4B4B4B;
            font-size: 0.95em;
            display: flex;
            justify-content: space-between;
            max-width: 400px;
        }

        .carto p span:first-child {
            font-weight: 600;
            color: #4A002A;
        }

        .status-indicator {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            box-shadow: 0 0 8px rgba(39,174,96,0.3);
        }

        /* Button Styling */
        .cancel-btn, .payment-btn, .delete-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            font-family: 'Poppins', sans-serif;
        }

        .cancel-btn {
            background-color: #e74c3c;
            color: white;
        }

        .cancel-btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .payment-btn {
            background-color: #4A002A;
            color: white;
        }

        .payment-btn:hover {
            background-color: #6a1b9a;
            transform: translateY(-2px);
        }

        .delete-btn {
            background-color: #7f8c8d;
            color: white;
        }

        .delete-btn:hover {
            background-color: #6c7a7d;
            transform: translateY(-2px);
        }

        .cancel-btn i, .payment-btn i, .delete-btn i {
            margin-right: 5px;
        }

        /* No bookings message */
        .no-bookings {
            text-align: center;
            padding: 40px;
            color: #4B4B4B;
            font-size: 1.2em;
        }

        .no-bookings i {
            color: #C2AE6D;
            margin-bottom: 15px;
        }

        /* Cancelled booking style */
        .carto.cancelled {
            opacity: 0.7;
            background-color: #f5f5f5;
        }

        .carto.cancelled img {
            filter: grayscale(80%);
        }

        .carto.cancelled h2,
        .carto.cancelled p span {
            color: #95a5a6 !important;
        }

        /* IMPROVED DROPDOWN STYLES */
        .dropdown {
            position: relative;
        }

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
            header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .sidebar {
                left: -250px;
            }
            
            .sidebar.open {
                left: 0;
            }
            
            .sidebar.open + .main-content {
                margin-left: 0;
            }
            
            .carto {
                flex-direction: column;
                text-align: center;
            }
            
            .carto img {
                width: 100%;
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .carto p {
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .carto p span {
                width: 100%;
                text-align: center;
                margin-bottom: 5px;
            }
            
            .notification-container {
                width: 90%;
                left: 5%;
                right: 5%;
            }
        }
    </style>
</head>
<body>
    <header>
        <button class="btn btn-light sidebar-toggle"><i class="fas fa-bars"></i></button>
        <h1 style="margin: 0; flex-grow: 1; text-align: center; font-family: 'Cinzel Decorative', serif;">Daftar Pesanan</h1>
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

    <?php if (!empty($notification)): ?>
    <div class="notification-container">
        <div class="alert <?php echo $alert_class; ?> alert-dismissible fade show" role="alert">
            <?php echo $notification; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php endif; ?>

    <nav class="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="UI_Home.php">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="UI_listpage.php">
                    <i class="fas fa-list"></i>
                    <span>List</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-calendar"></i>
                    <span>Daftar Pesanan</span>
                </a>
            </li>
        </ul>
    </nav>

    <section class="main-content">
        <div class="jadwal">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $status = $row['status'];
                    $is_paid = $payment->isPaid($row['booking_id']);
                    
                    if ($status == 'confirmed') {
                        $status_color = '#27ae60';
                        $card_class = 'carto';
                    } elseif ($status == 'pending') {
                        $status_color = '#e67e22';
                        $card_class = 'carto';
                    } else {
                        $status_color = '#95a5a6';
                        $card_class = 'carto cancelled';
                    }
            ?>
            <div class="<?php echo $card_class; ?>">
                <div class="status-indicator" style="background-color: <?php echo $status_color; ?>;"></div>
                <img src="rm2.jpg" alt="Ruang Meeting">
                <div class="carto-content">
                    <h2><?php echo htmlspecialchars($row['room_name']); ?></h2>
                    <p>
                        <span>Tanggal:</span>
                        <span><?php echo htmlspecialchars($row['start_time']); ?></span>
                    </p>
                    <p>
                        <span>Mulai:</span>
                        <span><?php echo htmlspecialchars($row['start_time']); ?></span>
                    </p>
                    <p>
                        <span>Selesai:</span>
                        <span><?php echo htmlspecialchars($row['end_time']); ?></span>
                    </p>
                    <p>
                        <span>Status:</span>
                        <span style="color: <?php echo $status_color; ?>; font-weight: 600;">
                            <?php 
                            echo htmlspecialchars($status); 
                            if ($status == 'confirmed') {
                                echo ' <i class="fas fa-check-circle"></i>';
                            } elseif ($status == 'cancelled') {
                                echo ' <i class="fas fa-times-circle"></i>';
                            } else {
                                echo ' <i class="fas fa-clock"></i>';
                            }
                            ?>
                        </span>
                    </p>
                    <p>
                        <span>Status Pembayaran:</span>
                        <span style="color: <?php echo $is_paid ? '#27ae60' : '#e74c3c'; ?>; font-weight: 600;">
                            <?php 
                            echo $is_paid ? 'Lunas' : 'Belum Dibayar'; 
                            echo $is_paid ? ' <i class="fas fa-check-circle"></i>' : ' <i class="fas fa-exclamation-circle"></i>';
                            ?>
                        </span>
                    </p>
                    
                    <?php if ($status == 'pending') { ?>
                        <button class="cancel-btn" onclick="deleteBooking(<?php echo $row['booking_id']; ?>)">
                            <i class="fas fa-times"></i>
                            Batalkan Pesanan
                        </button>
                    <?php } elseif ($status == 'confirmed') { ?>
                        <?php if ($is_paid) { ?>
                            <button class="payment-btn" onclick="viewBookingDetail(<?php echo $row['booking_id']; ?>)">
                                <i class="fas fa-eye"></i>
                                Lihat Detail Pemesanan
                            </button>
                        <?php } else { ?>
                            <button class="payment-btn" onclick="goToPayment(<?php echo $row['booking_id']; ?>)">
                                <i class="fas fa-credit-card"></i>
                                Menuju Pembayaran
                            </button>
                        <?php } ?>
                    <?php } elseif ($status == 'cancelled') { ?>
                        <button class="delete-btn" onclick="deleteBookingPermanently(<?php echo $row['booking_id']; ?>)">
                            <i class="fas fa-trash"></i>
                            Hapus Pesanan
                        </button>
                    <?php } ?>
                </div>
            </div>
            <?php
                }
            } else {
                echo '<div class="no-bookings"><i class="fas fa-calendar-times fa-3x"></i><p>Tidak ada pesanan yang ditemukan.</p></div>';
            }
            ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
                mainContent.classList.toggle('open');
            });

            const notificationAlert = document.querySelector('.alert');
            if (notificationAlert) {
                setTimeout(() => {
                    notificationAlert.classList.remove('show');
                    notificationAlert.classList.add('fade');
                }, 5000);
            }
        });

        function deleteBooking(bookingId) {
            if (confirm("Apakah Anda yakin ingin membatalkan pesanan ini?")) {
                window.location.href = 'booking_action.php?action=batal&booking_id=' + bookingId;
            }
        }

        function deleteBookingPermanently(bookingId) {
            if (confirm("Apakah Anda yakin ingin menghapus permanen pesanan ini?")) {
                window.location.href = 'booking_action.php?action=delete&booking_id=' + bookingId;
            }
        }

        function goToPayment(bookingId) {
            window.location.href = `UI_formpayment.php?booking_id=${bookingId}`;
        }

        function viewBookingDetail(bookingId) {
            window.location.href = `booking_detail.php?booking_id=${bookingId}`;
        }
    </script>
</body>
</html>