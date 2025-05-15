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
    <title>MERAPAT | LAMAN RUANGAN</title>
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

        /* Card Styling */
        .card1 {
            background: white;
            border: 1px solid #e1bee7;
            border-radius: 15px;
            padding: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .card1:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(156,39,176,0.1);
            border-color: #C2AE6D;
        }

        .card1 img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
            border: 2px solid #C2AE6D;
        }

        .card1 h3 {
            margin: 0 0 10px 0;
            color: #4A002A;
            font-size: 1.2em;
            font-family: 'Cinzel Decorative', serif;
        }

        .card1 p {
            margin: 5px 0;
            color: #4B4B4B;
            font-size: 0.9em;
        }

        .card1 span {
            color: #27ae60;
            font-weight: 600;
            font-size: 0.95em;
        }

        .status-indicator {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #27ae60;
            box-shadow: 0 0 8px rgba(39,174,96,0.3);
        }

        /* Remove text-decoration for card links */
        a.card-link {
            text-decoration: none;
            color: inherit;
        }

        /* Search Form */
        .search-form {
            flex-grow: 1;
            margin: 0 20px;
        }

        .search-form input[type="text"] {
            width: 100%;
            max-width: 600px;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: rgba(255,255,255,0.9);
            font-family: 'Poppins', sans-serif;
        }

        .search-form input[type="text"]:focus {
            outline: none;
            box-shadow: 0 0 10px rgba(74, 0, 42, 0.2);
            border: 1px solid #C2AE6D;
        }

        .search-form button {
            padding: 10px 20px;
            border: none;
            background-color: #4A002A;
            color: white;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: 10px;
            font-family: 'Poppins', sans-serif;
        }

        .search-form button:hover {
            background-color: #6a1b9a;
            transform: translateY(-1px);
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
            .sidebar {
                left: -250px;
            }
            .sidebar.open {
                left: 0;
            }
            .sidebar.open + .main-content {
                margin-left: 0;
            }
            
            header {
                flex-direction: column;
                gap: 15px;
            }
            
            .search-form {
                width: 100%;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <header>
        <button class="btn btn-light sidebar-toggle"><i class="fas fa-bars"></i></button>
        <form method="GET" action="" class="search-form d-flex align-items-center">
            <input type="text" name="keyword" class="form-control" placeholder="Cari ruangan..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
            <button type="submit" class="btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
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
    <nav class="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="UI_Home.php">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
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
        <div class="row">
            <?php 
                require_once 'meeting_admin.php';
                $tanggapan = new Meet();

                $id = 1;
                    
                // Cek apakah ada keyword pencarian
                $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
                if (!empty($keyword)) {
                    $data = $tanggapan->search($keyword);
                } else {
                    $data = $tanggapan->meetread();
                }      
                while ($result = $data->fetch_assoc()) {    
            ?>
            <!-- Di bagian card ruangan -->
            <div class="col-md-4 mb-4">
                <a href="UI_formbooking.php?room_id=<?= $result['room_id'] ?>" class="card-link">
                    <div class="card1">
                        <div class="status-indicator"></div>
                        <img src="rm2.jpg" alt="<?= htmlspecialchars($result['room_name']); ?>">
                        <h3><?= htmlspecialchars($result['room_name']); ?></h3>
                        <p>Kapasitas: <?= htmlspecialchars($result['capacity']); ?> Orang</p>
                        <p>Status <span><?= htmlspecialchars($result['status']); ?></span></p>
                    </div>
                </a>
            </div>
            <?php } ?>
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