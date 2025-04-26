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
$user_id = $_SESSION['user_id']; // Ambil user_id dari session
// Include class booking
require_once 'booking.php';
// Buat objek booking
$booking = new booking();
// Ambil data booking berdasarkan user_id
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
    <title>MERAPAT | Schedule</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        /* Header Sticky */
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

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: -250px; /* Sidebar hidden by default */
            background-color: #fff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            z-index: 999;
            padding-top: 80px;
        }

        .sidebar.open {
            left: 0; /* Sidebar visible */
        }

        .sidebar .nav-link {
            color: #2c3e50;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            background-color: #9c27b0;
            color: white;
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
            box-shadow: 0 10px 20px rgba(156,39,176,0.1);
        }

        .carto img {
            width: 200px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 20px;
        }

        .carto-content {
            flex: 1;
        }

        .carto h2 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 1.2em;
        }

        .carto p {
            margin: 5px 0;
            color: #7f8c8d;
            font-size: 0.9em;
            display: flex;
            justify-content: space-between;
            max-width: 400px;
        }

        .carto p span:first-child {
            font-weight: 600;
            color: #2c3e50;
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
        .cancel-btn, .payment-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
        }

        .cancel-btn {
            background-color: #e74c3c;
            color: white;
        }

        .cancel-btn:hover {
            background-color: #c0392b;
        }

        .payment-btn {
            background-color: #27ae60;
            color: white;
        }

        .payment-btn:hover {
            background-color: #219653;
        }

        .cancel-btn i, .payment-btn i {
            margin-right: 5px;
        }

        /* No bookings message */
        .no-bookings {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <header>
        <button class="btn btn-light sidebar-toggle"><i class="fas fa-bars"></i></button>
        <h1 style="margin: 0; flex-grow: 1; text-align: center;">Bookingan Kamu</h1>
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($name); ?>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
            </ul>
        </div>
    </header>
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
                    $status_color = ($status == 'confirmed') ? '#27ae60' : '#e67e22';
            ?>
            <div class="carto">
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
                        <span style="color: <?php echo $status_color; ?>;"><?php echo htmlspecialchars($status); ?></span>
                    </p>
                    <?php if ($status == 'pending') { ?>
                        <button class="cancel-btn" onclick="deleteBooking(<?php echo $row['booking_id']; ?>)">
                            <i class="fas fa-times"></i>
                            Batalkan Pesanan
                        </button>
                    <?php } elseif ($status == 'confirmed') { ?>
                        <button class="payment-btn" onclick="goToPayment(<?php echo $row['booking_id']; ?>)">
                            <i class="fas fa-credit-card"></i>
                            Menuju Pembayaran
                        </button>
                    <?php } ?>
                </div>
            </div>
            <?php
                }
            } else {
                echo '<div class="no-bookings"><i class="fas fa-calendar-times fa-2x" style="margin-bottom: 15px;"></i><p>Tidak ada pesanan yang ditemukan.</p></div>';
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

            // Toggle sidebar
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
                mainContent.classList.toggle('open');
            });
        });

        function deleteBooking(bookingId) {
            if (confirm("Apakah Anda yakin ingin membatalkan pesanan ini?")) {
                window.location.href = 'booking_action.php?action=batal&booking_id=' + bookingId;
            }
        }

        function goToPayment(bookingId) {
            window.location.href = `UI_formpayment.php?booking_id=${bookingId}`;
        }
    </script>
</body>
</html>