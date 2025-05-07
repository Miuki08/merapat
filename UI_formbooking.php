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
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

if ($room_id === 0) {
    header("Location: UI_listpage.php");
    exit();
}

require_once 'meeting_admin.php';
require_once 'review.php'; // Include the Review class

$meeting = new Meet();
$review = new Review(); // Create Review instance
$room = $meeting->getRoomByID($room_id);

if (!$room) {
    header("Location: UI_listpage.php");
    exit();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'], $_POST['review'])) {
    $review->user_id = $user_id;
    $review->room_id = $room_id;
    $review->rating = intval($_POST['rating']);
    $review->review_text = $_POST['review'];
    
    if ($review->add()) {
        echo "<script>alert('Review berhasil dikirim!');</script>";
    } else {
        echo "<script>alert('Gagal mengirim review.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="logoweb.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>BOOKING ROOM | MERAPAT</title>
    <style>
        /* =============== BASE STYLES =============== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            list-style: none;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            background-color: #f8f9fa;
            color: #2c3e50;
            margin-top: 70px; /* Untuk header fixed */
        }

        /* =============== HEADER STYLES =============== */
        header {
            width: 100%;
            background: linear-gradient(135deg, #6a1b9a, #9c27b0);
            color: white;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(106,27,154,0.2);
            position: fixed;
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

        .dropdown-menu {
            right: 0;
            left: auto;
        }

        /* =============== MAIN CONTENT =============== */
        .main-content {
            display: flex;
            justify-content: space-between;
            padding: 30px 40px;
            gap: 40px;
        }

        .description {
            flex: 1;
            max-width: 60%;
        }

        .description img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .description h2 {
            color: #6a1b9a;
            margin-bottom: 15px;
        }

        .description p {
            font-size: 1.1em;
            margin: 10px 0;
            color: #555;
        }

        /* =============== FORM STYLES =============== */
        .form {
            flex: 1;
            max-width: 35%;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form h3 {
            color: #6a1b9a;
            margin-bottom: 25px;
        }

        .input-field {
            margin-bottom: 20px;
        }

        .input-field label {
            display: block;
            margin-bottom: 8px;
            color: #666;
            font-weight: 500;
        }

        .input-field input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .input-field input:focus {
            border-color: #9c27b0;
            outline: none;
        }

        .lestgoo input[type="submit"] {
            background: #9c27b0;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .lestgoo input[type="submit"]:hover {
            background: #6a1b9a;
            transform: translateY(-2px);
        }

        /* =============== REVIEW SECTION =============== */
        .review {
            padding: 40px;
            background: #f8f9fa;
            margin: 40px;
            border-radius: 15px;
        }

        .review form {
            max-width: 800px;
            margin: 0 auto;
        }

        .star-rating {
            direction: rtl;
            display: inline-block;
            margin-bottom: 20px;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label {
            color: #ddd;
            font-size: 28px;
            padding: 0 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input[type="radio"]:checked ~ label {
            color: #ffd700;
        }

        .pendapat textarea {
            width: 100%;
            height: 120px;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            margin: 15px 0;
            resize: vertical;
            font-size: 16px;
        }

        .goo input[type="submit"] {
            background: #9c27b0;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .goo input[type="submit"]:hover {
            background: #6a1b9a;
            transform: translateY(-2px);
        }

        #tooltip {
            display: none;
            position: absolute;
            background: #ffffff;
            border: 1px solid #e0e0e0;
            padding: 12px 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            font-size: 14px;
            color: #333333;
            z-index: 1000;
            font-family: 'Arial', sans-serif;
            line-height: 1.5;
            max-width: 300px;
            word-wrap: break-word;
            opacity: 0;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        #tooltip.show {
            opacity: 1;
            transform: translateY(0);
        }

        .price-calculation {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .price-calculation h4 {
            margin-bottom: 10px;
            color: #6c757d;
        }
        .price-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .total-price {
            font-weight: bold;
            font-size: 1.2em;
            color: #28a745;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #6c757d;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <button onclick="window.history.back()"><i class="fa-solid fa-arrow-left"></i></button>
            <h1>Room Details</h1>
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
    <section class="main-content">
        <div class="description">
            <img src="rm3.jpg" alt="">
            <h2><?= htmlspecialchars($room['room_name']); ?></h2>
            <p>Lokasi: <?= htmlspecialchars($room['location']); ?></p>
            <p>Kapasitas: <?= htmlspecialchars($room['capacity']); ?> Orang</p>
            <p>Harga Sewa: Rp. <?= number_format($room['total_price'], 0, ',', '.'); ?>/Jam</p>
            <p>
                <span onmouseover="showTooltip(event, 'Ketentuan: 1. Minimal booking 1 jam. 2. Pembayaran penuh dilakukan H -1 sebelum penggunaan ruangan. 3. Dilarang merokok di dalam ruangan. 4. Membatalkan pemesanan ruangan akan mengakibatkan uang tidak kembali baik itu uang muka ataupun uang penuh. ')" onmouseout="hideTooltip()" style="cursor: pointer; color: blueviolet;">Ketentuan</span> 
                dan 
                <span onmouseover="showTooltip(event, 'Persyaratan: 1. Menunjukkan identitas yang valid. 2. Membawa bukti pembayaran. 3. Menandatangani perjanjian penggunaan ruangan (perjanjian akan dikirim kepada pengguna melalui email 2x24 jam). ')" onmouseout="hideTooltip()" style="cursor: pointer; color: blueviolet;">Persyaratan</span> 
                pemesanan
            </p>
            <div id="tooltip"></div>
        </div>
        <div class="form">
            <h3>Tetapkan tanggal dan waktu kamu</h3>
            <form action="booking_action.php?action=add" method="post">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                <input type="hidden" name="room_id" value="<?= $room_id ?>">
                <input type="hidden" name="total_price" id="total_price_input" value="0">
                <div class="input-field">
                    <label for="start_time">Start Time</label>
                    <input type="datetime-local" name="start_time" id="start_time" required>
                </div>
                <div class="input-field">
                    <label for="end_time">End Time</label>
                    <input type="datetime-local" name="end_time" id="end_time" required>
                </div>
                
                <!-- Price Calculation Section -->
                <div class="price-calculation" id="priceCalculation" style="display: none;">
                    <h4>Detail Harga</h4>
                    <div class="price-details">
                        <span>Harga per Jam:</span>
                        <span>Rp. <span id="hourly_price"><?= number_format($room['total_price'], 0, ',', '.'); ?></span></span>
                    </div>
                    <div class="price-details">
                        <span>Durasi:</span>
                        <span id="duration">0 jam</span>
                    </div>
                    <div class="total-price">
                        Total Harga: Rp. <span id="total_price">0</span>
                    </div>
                </div>
                
                <div class="lestgoo">
                    <input type="submit" value="Pesan">
                </div>
            </form>
        </div>
    </section>
    <section class="review">
        <h2 style="text-align: center; margin-bottom: 30px; color: #6a1b9a;">Berikan Review Anda</h2>
        <form action="" method="post">
            <input type="hidden" name="room_id" value="<?= $room_id ?>">
            <input type="hidden" name="user_id" value="<?= $user_id ?>">
            
            <div class="star">
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" required />
                    <label for="star5" title="5 stars">★</label>
                    <input type="radio" id="star4" name="rating" value="4" />
                    <label for="star4" title="4 stars">★</label>
                    <input type="radio" id="star3" name="rating" value="3" />
                    <label for="star3" title="3 stars">★</label>
                    <input type="radio" id="star2" name="rating" value="2" />
                    <label for="star2" title="2 stars">★</label>
                    <input type="radio" id="star1" name="rating" value="1" />
                    <label for="star1" title="1 star">★</label>
                </div>
            </div>
            <div class="pendapat">
                <label for="review">Bagaimana pengalaman Anda menggunakan ruangan ini?</label>
                <textarea name="review" id="review" required></textarea>
            </div>
            <div class="goo">
                <input type="submit" value="Kirim Review">
            </div>
        </form>

        <!-- Display existing reviews -->
        <div class="existing-reviews" style="margin-top: 50px;">
            <h3 style="color: #6a1b9a; margin-bottom: 20px;">Review Pengguna Lain</h3>
            <?php
            $reviews = $review->getReviewsByRoomId($room_id);
            if ($reviews->num_rows > 0) {
                while ($row = $reviews->fetch_assoc()) {
                    echo '<div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">';
                    echo '<div style="display: flex; justify-content: space-between; margin-bottom: 10px;">';
                    echo '<strong>' . htmlspecialchars($row['user_name'] ?? 'Anonymous') . '</strong>';
                    echo '<div style="color: gold;">';
                    for ($i = 0; $i < 5; $i++) {
                        echo $i < $row['rating'] ? '★' : '☆';
                    }
                    echo '</div>';
                    echo '</div>';
                    echo '<p>' . nl2br(htmlspecialchars($row['review_text'])) . '</p>';
                    if (!empty($row['response'])) {
                        echo '<div style="background: #f8f9fa; padding: 10px; border-left: 3px solid #6a1b9a; margin-top: 10px;">';
                        echo '<strong>Respon Admin:</strong> ' . nl2br(htmlspecialchars($row['response']));
                        echo '</div>';
                    }
                    echo '</div>';
                }
            } else {
                echo '<p style="text-align: center; color: #666;">Belum ada review untuk ruangan ini.</p>';
            }
            ?>
        </div>
    </section>
    <script>
        const roomPricePerHour = <?= $room['total_price']; ?>;
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const priceCalculation = document.getElementById('priceCalculation');
        const durationDisplay = document.getElementById('duration');
        const totalPriceDisplay = document.getElementById('total_price');
        const totalPriceInput = document.getElementById('total_price_input');
        
        function calculatePrice() {
            const startTime = new Date(startTimeInput.value);
            const endTime = new Date(endTimeInput.value);
            
            if (startTime && endTime && endTime > startTime) {
                // Calculate duration in hours
                const durationInMs = endTime - startTime;
                const durationInHours = durationInMs / (1000 * 60 * 60);
                
                // Calculate total price
                const totalPrice = Math.ceil(durationInHours) * roomPricePerHour;
                
                // Update display
                durationDisplay.textContent = `${Math.ceil(durationInHours)} jam`;
                totalPriceDisplay.textContent = new Intl.NumberFormat('id-ID').format(totalPrice);
                totalPriceInput.value = totalPrice;
                
                // Show price calculation
                priceCalculation.style.display = 'block';
            } else {
                priceCalculation.style.display = 'none';
            }
        }
        
        // Add event listeners
        startTimeInput.addEventListener('change', calculatePrice);
        endTimeInput.addEventListener('change', calculatePrice);
        
        document.querySelector('.form form').addEventListener('submit', function(e) {
            // Ambil nilai input
            const startTime = this.elements.start_time.value;
            const endTime = this.elements.end_time.value;
            
            // Validasi field kosong
            if (!startTime || !endTime) {
                e.preventDefault(); // Mencegah pengiriman form
                alert('Harap isi semua field terlebih dahulu!');
                return false;
            }
            
            // Validasi waktu akhir harus setelah waktu mulai
            if (new Date(endTime) <= new Date(startTime)) {
                e.preventDefault();
                alert('Waktu akhir harus setelah waktu mulai!');
                return false;
            }
            
            // Validasi minimal 1 jam
            const durationInHours = (new Date(endTime) - new Date(startTime)) / (1000 * 60 * 60);
            if (durationInHours < 1) {
                e.preventDefault();
                alert('Minimal booking adalah 1 jam!');
                return false;
            }
            
            // Jika semua validasi terpenuhi, form akan dikirim
            return true;
        });

        function showTooltip(event, text) {
            const tooltip = document.getElementById('tooltip');
            tooltip.innerHTML = text;
            tooltip.style.display = 'block';
            tooltip.style.left = event.clientX + 10 + 'px'; // Geser sedikit ke kanan
            tooltip.style.top = (event.clientY + 20) + 'px'; // Geser sedikit ke bawah

            // Tambahkan class 'show' untuk animasi
            setTimeout(() => {
                tooltip.classList.add('show');
            }, 10); // Delay kecil untuk memastikan CSS diproses
        }

        function hideTooltip() {
            const tooltip = document.getElementById('tooltip');
            tooltip.classList.remove('show'); // Hapus class 'show' untuk animasi menghilang

            // Sembunyikan tooltip setelah animasi selesai
            setTimeout(() => {
                tooltip.style.display = 'none';
            }, 300); // Sesuaikan dengan durasi animasi
        }
    </script> 
</body>
</html>