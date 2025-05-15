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
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

if ($room_id === 0) {
    header("Location: UI_listpage.php");
    exit();
}

require_once 'meeting_admin.php';
require_once 'review.php';

$meeting = new Meet();
$review = new Review();
$room = $meeting->getRoomByID($room_id);

if (!$room) {
    header("Location: UI_listpage.php");
    exit();
}

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
    <link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Cinzel+Decorative:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>BOOKING ROOM | MERAPAT</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            list-style: none;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
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
            border: 3px solid #C2AE6D;
        }

        .description h2 {
            color: #4A002A;
            margin-bottom: 15px;
            font-family: 'Cinzel Decorative', serif;
        }

        .description p {
            font-size: 1.1em;
            margin: 10px 0;
            color: #4B4B4B;
        }

        /* Form Styles */
        .form {
            flex: 1;
            max-width: 35%;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form h3 {
            color: #4A002A;
            margin-bottom: 25px;
            font-family: 'Cinzel Decorative', serif;
        }

        .input-field {
            margin-bottom: 20px;
        }

        .input-field label {
            display: block;
            margin-bottom: 8px;
            color: #4B4B4B;
            font-weight: 500;
        }

        .input-field input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .input-field input:focus {
            border-color: #C2AE6D;
            outline: none;
        }

        .lestgoo input[type="submit"] {
            background: #4A002A;
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

        /* Review Section */
        .review {
            padding: 40px;
            background: #F1E9E9;
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
            color: #C2AE6D;
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
            background: #4A002A;
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
            background: white;
            border: 1px solid #C2AE6D;
            padding: 12px 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            font-size: 14px;
            color: #4A002A;
            z-index: 1000;
            max-width: 300px;
            word-wrap: break-word;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .price-calculation {
            margin: 20px 0;
            padding: 15px;
            background-color: #F1E9E9;
            border-radius: 8px;
            border: 1px solid #C2AE6D;
        }

        .price-calculation h4 {
            margin-bottom: 10px;
            color: #4A002A;
        }

        .price-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total-price {
            font-weight: bold;
            font-size: 1.2em;
            color: #4A002A;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #C2AE6D;
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

        /* Existing Reviews */
        .existing-reviews {
            margin-top: 50px;
        }

        .existing-reviews h3 {
            color: #4A002A;
            margin-bottom: 20px;
            font-family: 'Cinzel Decorative', serif;
        }

        .review-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border: 1px solid #F1E9E9;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .review-stars {
            color: #C2AE6D;
        }

        .admin-response {
            background: #F1E9E9;
            padding: 10px;
            border-left: 3px solid #4A002A;
            margin-top: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
                padding: 20px;
            }

            .description, .form {
                max-width: 100%;
            }

            .review {
                margin: 20px;
                padding: 20px;
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
            <h1>Detail Ruangan</h1>
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
    
    <section class="main-content">
        <div class="description">
            <img src="rm3.jpg" alt="<?= htmlspecialchars($room['room_name']); ?>">
            <h2><?= htmlspecialchars($room['room_name']); ?></h2>
            <p>Lokasi: <?= htmlspecialchars($room['location']); ?></p>
            <p>Kapasitas: <?= htmlspecialchars($room['capacity']); ?> Orang</p>
            <p>Harga Sewa: Rp. <?= number_format($room['total_price'], 0, ',', '.'); ?>/Jam</p>
            <p>
                <span onmouseover="showTooltip(event, 'Ketentuan: 1. Minimal booking 1 jam. 2. Pembayaran penuh dilakukan H -1 sebelum penggunaan ruangan. 3. Dilarang merokok di dalam ruangan. 4. Membatalkan pemesanan ruangan akan mengakibatkan uang tidak kembali baik itu uang muka ataupun uang penuh. ')" onmouseout="hideTooltip()" style="cursor: pointer; color: #6a1b9a;">Ketentuan</span> 
                dan 
                <span onmouseover="showTooltip(event, 'Persyaratan: 1. Menunjukkan identitas yang valid. 2. Membawa bukti pembayaran. 3. Menandatangani perjanjian penggunaan ruangan (perjanjian akan dikirim kepada pengguna melalui email 2x24 jam). ')" onmouseout="hideTooltip()" style="cursor: pointer; color: #6a1b9a;">Persyaratan</span> 
                pemesanan
            </p>
            <div id="tooltip"></div>
        </div>
        
        <div class="form">
            <h3>Tetapkan tanggal dan waktu</h3>
            <form action="booking_action.php?action=add" method="post">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                <input type="hidden" name="room_id" value="<?= $room_id ?>">
                <input type="hidden" name="total_price" id="total_price_input" value="0">
                
                <div class="input-field">
                    <label for="start_time">Waktu Mulai</label>
                    <input type="datetime-local" name="start_time" id="start_time" required>
                </div>
                
                <div class="input-field">
                    <label for="end_time">Waktu Selesai</label>
                    <input type="datetime-local" name="end_time" id="end_time" required>
                </div>
                
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
                    <input type="submit" value="Pesan Sekarang">
                </div>
            </form>
        </div>
    </section>
    
    <section class="review">
        <h2 style="text-align: center; margin-bottom: 30px; color: #4A002A; font-family: 'Cinzel Decorative', serif;">Berikan Review Anda</h2>
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

        <div class="existing-reviews">
            <h3>Review Pengguna Lain</h3>
            <?php
            $reviews = $review->getReviewsByRoomId($room_id);
            if ($reviews->num_rows > 0) {
                while ($row = $reviews->fetch_assoc()) {
                    echo '<div class="review-item">';
                    echo '<div class="review-header">';
                    echo '<strong>' . htmlspecialchars($row['user_name'] ?? 'Anonymous') . '</strong>';
                    echo '<div class="review-stars">';
                    for ($i = 0; $i < 5; $i++) {
                        echo $i < $row['rating'] ? '★' : '☆';
                    }
                    echo '</div>';
                    echo '</div>';
                    echo '<p>' . nl2br(htmlspecialchars($row['review_text'])) . '</p>';
                    if (!empty($row['response'])) {
                        echo '<div class="admin-response">';
                        echo '<strong>Respon Admin:</strong> ' . nl2br(htmlspecialchars($row['response']));
                        echo '</div>';
                    }
                    echo '</div>';
                }
            } else {
                echo '<p style="text-align: center; color: #4B4B4B;">Belum ada review untuk ruangan ini.</p>';
            }
            ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                const durationInMs = endTime - startTime;
                const durationInHours = durationInMs / (1000 * 60 * 60);
                const totalPrice = Math.ceil(durationInHours) * roomPricePerHour;
                
                durationDisplay.textContent = `${Math.ceil(durationInHours)} jam`;
                totalPriceDisplay.textContent = new Intl.NumberFormat('id-ID').format(totalPrice);
                totalPriceInput.value = totalPrice;
                priceCalculation.style.display = 'block';
            } else {
                priceCalculation.style.display = 'none';
            }
        }
        
        startTimeInput.addEventListener('change', calculatePrice);
        endTimeInput.addEventListener('change', calculatePrice);
        
        document.querySelector('.form form').addEventListener('submit', function(e) {
            const startTime = this.elements.start_time.value;
            const endTime = this.elements.end_time.value;
            
            if (!startTime || !endTime) {
                e.preventDefault();
                alert('Harap isi semua field terlebih dahulu!');
                return false;
            }
            
            if (new Date(endTime) <= new Date(startTime)) {
                e.preventDefault();
                alert('Waktu akhir harus setelah waktu mulai!');
                return false;
            }
            
            const durationInHours = (new Date(endTime) - new Date(startTime)) / (1000 * 60 * 60);
            if (durationInHours < 1) {
                e.preventDefault();
                alert('Minimal booking adalah 1 jam!');
                return false;
            }
            
            return true;
        });

        function showTooltip(event, text) {
            const tooltip = document.getElementById('tooltip');
            tooltip.innerHTML = text;
            tooltip.style.display = 'block';
            tooltip.style.left = event.clientX + 10 + 'px';
            tooltip.style.top = (event.clientY + 20) + 'px';
            setTimeout(() => {
                tooltip.style.opacity = '1';
            }, 10);
        }

        function hideTooltip() {
            const tooltip = document.getElementById('tooltip');
            tooltip.style.opacity = '0';
            setTimeout(() => {
                tooltip.style.display = 'none';
            }, 300);
        }
    </script>
</body>
</html>