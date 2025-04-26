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
    <title>MERAPAT | LAMAN UTAMA</title>
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
            width: 100px; /* Ukuran gambar diperkecil */
            height: 100px; /* Ukuran gambar diperkecil */
            border-radius: 50%; /* Membuat gambar menjadi bulat */
            display: block; /* Memposisikan gambar di tengah */
            margin: 0 auto 20px auto; /* Margin untuk posisi tengah dan jarak dari elemen bawah */
            object-fit: cover; /* Memastikan gambar tidak terdistorsi */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Menambahkan shadow untuk efek elegan */
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Animasi saat hover */
        }

        section img:hover {
            transform: scale(1.1); /* Membesar sedikit saat dihover */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3); /* Shadow lebih besar saat dihover */
        }

        /* Flexbox untuk memposisikan gambar di tengah vertikal dan horizontal */
        section .user {
            display: flex;
            flex-direction: column;
            align-items: center; /* Posisi tengah horizontal */
            justify-content: center; /* Posisi tengah vertikal */
            text-align: center; /* Teks rata tengah */
            margin-bottom: 30px;
        }

        section .user h1 {
            font-size: 2em;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        section .user p {
            font-size: 1.1em;
            color: #7f8c8d;
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
            color: #6a1b9a;
            margin-bottom: 15px;
        }

        section .ketentuan p, section .persyaratan p {
            font-size: 1em;
            color: #2c3e50;
            line-height: 1.6;
            margin: 0;
        }

        section .ketentuan p:hover, section .persyaratan p:hover {
            color: #9c27b0;
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
        }

        .grid-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
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

            // Toggle sidebar
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
                mainContent.classList.toggle('open');
            });
        });
    </script>
</body>
</html>