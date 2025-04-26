<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login_register.php");
    exit();
}

//Cek apakah role user adalah manager atau officer
if ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'officer') {
    // Jika bukan manager atau officer, redirect ke dashboard yang sesuai
    if ($_SESSION['role'] == 'customer') {
        header("Location: UI_listpage.php");
    } else {
        // Role tidak dikenali, redirect ke halaman login
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
    <title>MERAPAT DASHBOARD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    /* GLOBAL STYLES */
    :root {
        --primary: #7c4dff;
        --secondary: #b388ff;
        --accent: #ff80ab;
        --background: #f0e6ff;
        --glass: rgba(255, 255, 255, 0.9);
        --bs-primary: var(--primary);
        --bs-link-color: var(--primary);
    }

    body {
        background: linear-gradient(45deg, var(--background), #ffffff);
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* NAVIGATION STYLES */
    .navbar-glass {
        background: var(--glass);
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
    }

    .dropdown-menu {
        background: var(--glass);
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
    }

    .dropdown-item {
        color: var(--primary) !important;
        transition: all 0.3s ease;
    }

    .dropdown-item:hover {
        background: rgba(124, 77, 255, 0.05) !important;
    }

    .nav-link-custom {
        color: var(--primary);
        padding: 0.8rem 1.5rem;
        border-radius: 15px !important;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .nav-link-custom:hover {
        background: rgba(124, 77, 255, 0.05);
        transform: translateY(-3px);
    }

    .navbar-nav .nav-item {
        margin-right: 15px;
    }

    .floating {
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }

    /* TABLE STYLES */
    .table-glass {
        background: var(--glass);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        overflow: hidden;
    }

    .table {
        --bs-table-bg: transparent;
        margin-bottom: 0;
    }

    .table-primary {
        --bs-table-bg: rgba(124, 77, 255, 0.1);
        --bs-table-border-color: rgba(124, 77, 255, 0.2);
    }

    .table-hover tbody tr:hover {
        background: rgba(124, 77, 255, 0.05) !important;
    }

    .status-badge {
        font-size: 0.9em;
        padding: 0.6em 1em;
        border-radius: 20px;
        font-weight: 500;
    }

    .status-badge.pending {
        background-color: #ffc107;
        color: #000;
        animation: blink 1.5s infinite;
    }

    .status-badge.confirmed {
        background-color: #28a745;
        color: #fff;
    }

    .status-badge.cancelled {
        background-color: #dc3545;
        color: #fff;
    }

    @keyframes blink {
        0%, 50%, 100% { opacity: 1; }
        25%, 75% { opacity: 0.5; }
    }

    .btn-action {
        background: rgba(124, 77, 255, 0.1);
        color: var(--primary);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-action:hover {
        background: var(--primary);
        color: white;
    }

    .rounded-start {
        border-radius: 20px 0 0 20px !important;
    }

    .rounded-end {
        border-radius: 0 20px 20px 0 !important;
    }
</style>
<body>
    <nav class="navbar navbar-glass navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="logoweb.png" alt="logo" class="floating me-2" height="60">
                <span class="fw-bold">MERAPAT</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="dasboard.php">
                            <i class="fa-solid fa-house me-2"></i>Laman Utama
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="meet_min_view.php">
                            <i class="fas fa-door-open me-2"></i>Ruangan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#">
                            <i class="fas fa-calendar-check me-2"></i>Pemesanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="user_view.php">
                            <i class="fas fa-users me-2"></i>User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#">
                            <i class="fas fa-wallet me-2"></i>Pembayaran
                        </a>
                    </li>
                </ul>
                <div class="dropdown">
                    <a class="btn glass-card dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo htmlspecialchars($name); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">
                            <i class="fas fa-user-cog me-2"></i>Profile
                        </a></li>
                        <li><a class="dropdown-item" href="User_add.php">
                            <i class="fa-solid fa-user-plus me-2"></i>Invite Member
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <!-- Search and Add Section -->
        <div class="row mb-4 g-3 align-items-center">
            <div class="col-md-8">
                <form method="GET" action="" class="search-form">
                    <div class="input-group shadow-lg">
                        <input type="text" name="keyword" class="form-control search-input" placeholder="Search payments..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                        <button type="submit" class="btn btn-primary search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <a href="payment_add.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Pembayaran
                </a>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-glass shadow-lg">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th class="rounded-start">Payment ID</th>
                            <th>User Name</th>
                            <th>Room Name</th>
                            <th>Booking Period</th>
                            <th>Payment Date</th>
                            <th>Payment Method</th>
                            <th class="rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            require_once 'payment.php';
                            $payment = new Payment();

                            // Check if there's a search keyword
                            $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
                            if (!empty($keyword)) {
                                $data = $payment->search($keyword);
                            } else {
                                $data = $payment->paymentread();
                            }

                            while ($result = $data->fetch_assoc()) {
                        ?>
                        <tr class="table-row-hover">
                            <td><?= htmlspecialchars($result['payment_id']); ?></td>
                            <td><?= htmlspecialchars($result['name']); ?></td>
                            <td><?= htmlspecialchars($result['room_name']); ?></td>
                            <td>
                                <?= date('d M Y H:i', strtotime($result['start_time'])) ?> - 
                                <?= date('H:i', strtotime($result['end_time'])) ?>
                            </td>
                            <td><?= date('d M Y H:i', strtotime($result['payment_date'])) ?></td>
                            <td>
                                <span class="badge bg-primary rounded-pill">
                                    <?= htmlspecialchars($result['payment_method']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="payment_view.php?payment_id=<?= $result['payment_id']; ?>" class="btn btn-action btn-sm" data-bs-toggle="tooltip" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="payment_edit.php?payment_id=<?= $result['payment_id']; ?>" class="btn btn-action btn-sm" data-bs-toggle="tooltip" title="Edit Pembayaran">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="payment_action.php?action=delete&payment_id=<?= $result['payment_id']; ?>" class="btn btn-action btn-sm delete-btn" data-bs-toggle="tooltip" title="Hapus Pembayaran">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Script -->
    <script>
        // Konfirmasi sebelum menghapus
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Apakah Anda yakin ingin menghapus booking ini?')) {
                    e.preventDefault();
                }
            });
        });

        // Initialize Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
</body>
</html>