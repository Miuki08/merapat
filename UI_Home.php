
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
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'Email tidak tersedia';
$created_at = isset($_SESSION['created_at']) ? $_SESSION['created_at'] : 'Tanggal pembuatan akun tidak tersedia';
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
    <title>MERAPAT | LAMAN UTAMA</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F1E9E9;
            margin: 0;
            padding: 0;
        }

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
            position: relative; /* Added for dropdown positioning */
        }

        /* Sidebar and main content styles remain the same */
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

        /* Section Styling */
        section {
            margin: 20px;
            padding: 20px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        section:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        /* Styling untuk gambar */
        section img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: block;
            margin: 0 auto 20px auto;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 3px solid #C2AE6D;
        }

        section img:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        /* Flexbox untuk memposisikan gambar di tengah vertikal dan horizontal */
        section .user {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-bottom: 30px;
        }

        section .user h1 {
            font-size: 2em;
            color: #4A002A;
            margin-bottom: 10px;
            font-family: 'Cinzel Decorative', serif;
        }

        section .user p {
            font-size: 1.1em;
            color: #4B4B4B;
        }

        section .ketentuan, section .persyaratan {
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        section .ketentuan h1, section .persyaratan h1 {
            text-align: center;
            font-size: 1.5em;
            color: #4A002A;
            margin-bottom: 15px;
            font-family: 'Cinzel Decorative', serif;
        }

        section .ketentuan p, section .persyaratan p {
            font-size: 1em;
            color: #4B4B4B;
            line-height: 1.6;
            margin: 0;
        }

        section .ketentuan p:hover, section .persyaratan p:hover {
            color: #C2AE6D;
            cursor: pointer;
        }

        /* Grid Layout for Sections */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .grid-item {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #F1E9E9;
        }

        .grid-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            border-color: #C2AE6D;
        }

        /* IMPROVED DROPDOWN STYLES - THIS WILL WORK */
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
            .grid-container {
                grid-template-columns: 1fr;
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
        }
    </style>
</head>
<body>
    <header>
        <button class="btn btn-light sidebar-toggle"><i class="fas fa-bars"></i></button>
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

    <!-- Rest of your HTML content remains the same -->
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
                <a class="nav-link" href="UI_schadule.php">
                    <i class="fas fa-calendar"></i>
                    <span>Daftar Pesanan</span>
                </a>
            </li>
        </ul>
    </nav>

    <section class="main-content">
        <section>
            <img src="kola.gif" alt="yoolooo">
            <div class="user">
                <h1>Selamat datang, <?php echo htmlspecialchars($name); ?></h1>
                <p>Kamu login dengan akun <?php echo htmlspecialchars($email); ?></p>
                <p>Kapan kamu membuat akun? Kamu membuat akun pada <?php echo htmlspecialchars($created_at); ?></p>
            </div>
        </section>
        <div class="grid-container">
            <div class="grid-item">
                <div class="ketentuan">
                    <h1>Ketentuan Pemesanan Ruang Meeting</h1>
                    <p>1. Minimal waktu pemesanan adalah 1 jam dan maksimal waktu pemesanan adalah 10 jam.</p>
                    <p>2. Pembayaran yang dilakukan di website ini hanyalah uang muka saja, pembayaran penuh dilakukan ditempat meeting paling lambat H-1 sebelum meeting.</p>
                    <p>3. Dilarang merokok, membawa senjata tajam, dan membawa senjata api di dalam ruangan.</p>
                    <p>4. Membatalkan pemesanan ruangan akan mengakibatkan uang tidak kembali baik itu uang muka ataupun uang penuh.</p>
                    <p>5. Dilarang melakukan penambahan atau perubahan yang menimbulkan kerusakan pada ruangan (missal paku, lakban, dsb.)</p>
                </div>
            </div>
            <div class="grid-item">
                <div class="persyaratan">
                    <h1>Persyaratan Pemesanan Ruang Meeting</h1>
                    <p>1. Menunjukkan identitas yang valid (KTP/SIM/Kartu Pelajar).</p>
                    <p>2. Membawa bukti pembayaran uang muka.</p>
                    <p>3. Menandatangani perjanjian penggunaan ruangan (perjanjian akan dikirim kepada pengguna saat melakukab pembayaran uang muka).</p>
                    <p>4. Pada surat perjanjian kalian wajib menyampaikan informasi tentang jadwal, jumlah peserta, dan kebutuhan khusus, seperti peralatan atau tata letak ruangan.</p>
                    <p>5. Untuk instansi tertentu, diperlukan surat resmi yang berisi permintaan pemakaian ruangan dengan rincian acara.</p>
                </div>
            </div>
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
        });
    </script>
</body>
</html>