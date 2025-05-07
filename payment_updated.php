<?php
require_once 'payment.php';
$payment = new Payment();

if (isset($_GET['payment_id'])) {
    $payment->payment_id = $_GET['payment_id'];
    $data = $payment->getPaymentId($payment->payment_id);

    if ($data && $result = $data->fetch_assoc()) {
        $payment_id = $result['payment_id'];
        $booking_id = $result['booking_id'];
        $payment_date = $result['payment_date'];
        $user_file = $result['user_file'];
        $payment_method = $result['payment_method'];
        $status = $result['status'];
        $amount = $result['amount'];
    } else {
        echo "<script>
                alert('Data tidak ditemukan');
                location.href = 'payment_view.php';
              </script>";
        exit;
    }
} else {
    echo "<script>
            alert('Payment ID tidak valid');
            location.href = 'payment_view.php';
          </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Payment | MERAPAT ID</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .form-label {
            font-weight: bold;
            color: #343a40;
        }

        .form-control {
            border: 1px solid #ced4da;
            border-radius: 10px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn {
            border-radius: 10px;
            padding: 10px 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-light {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
        }

        .btn-light:hover {
            background-color: #e2e6ea;
        }

        .is-invalid {
            border-color: #dc3545;
        }
        
        .file-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
        }

        .file-preview-container {
            border: 1px dashed #ccc;
            padding: 15px;
            border-radius: 8px;
            background : #f9f9f9;
        }
        .alert {
            border-radius: 8px;
            padding: 12px 15px;
        }
    </style>
</head>
<body class="d-flex align-items-center">
    <div class="container py-5">
        <div class="row g-5">
            <!-- Hero Section -->
            <div class="col-lg-6">
                <div class="hero-section animate-float">
                    <img src="ides.jpg" alt="replika" class="hero-image">
                    <div class="content-text">
                        <h2 class="display-4 fw-bold mb-4">MERAPAT</h2>
                        <p class="lead fs-5">
                            <i class="fas fa-quote-left me-2"></i>
                            Perbarui data pembayaran dengan hati-hati. Pastikan semua informasi yang dimasukkan valid 
                            dan sesuai dengan bukti pembayaran yang diterima.
                            <i class="fas fa-quote-right ms-2"></i>
                        </p>
                        <div class="mt-4 d-flex gap-2">
                            <span class="badge bg-light text-primary p-3">Akurat</span>
                            <span class="badge bg-light text-primary p-3">Terverifikasi</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="col-lg-6">
                <form action="payment_action.php?action=update" method="post" enctype="multipart/form-data" class="glass-panel">
                    <h2 class="text-center mb-4 display-5 fw-bold text-primary">
                        <i class="fas fa-money-bill-wave me-2"></i>Update Pembayaran
                    </h2>
                    
                    <input type="hidden" name="payment_id" value="<?= $payment_id ?>">

                    <div class="mb-4">
                        <label class="form-label">Booking ID</label>
                        <input type="text" class="form-control" name="booking_id" value="<?= $booking_id ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Tanggal Pembayaran</label>
                        <input type="datetime-local" class="form-control" name="payment_date" value="<?= date('Y-m-d\TH:i', strtotime($payment_date)) ?>" required>
                    </div>

                    <!-- Bagian Preview File -->
                    <div class="mb-4">
                        <label class="form-label">Bukti Pembayaran</label>
                        <?php if($user_file): ?>
                            <div class="file-preview-container mt-3">
                                <?php
                                $file_ext = pathinfo($user_file, PATHINFO_EXTENSION);
                                if(in_array($file_ext, ['pdf', 'PDF'])): ?>
                                    <iframe src="<?= $user_file ?>" width="100%" height="500px" class="border rounded"></iframe>
                                    <div class="mt-2">
                                        <a href="<?= $user_file ?>" class="btn btn-primary" download>
                                            <i class="fas fa-download me-2"></i>Download PDF
                                        </a>
                                    </div>
                                <?php elseif(in_array($file_ext, ['doc', 'docx', 'DOC', 'DOCX'])): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-file-word me-2"></i>
                                        Dokumen Word: <a href="<?= $user_file ?>" download><?= basename($user_file) ?></a>
                                        <div class="mt-2">
                                            <a href="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($user_file) ?>" 
                                            target="_blank" class="btn btn-success">
                                                <i class="fas fa-eye me-2"></i>Preview Online
                                            </a>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-secondary">
                                        <i class="fas fa-file me-2"></i>
                                        <a href="<?= $user_file ?>" download><?= basename($user_file) ?></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">Tidak ada file yang diupload</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Metode Pembayaran</label>
                        <select class="form-select" name="payment_method" required>
                            <option value="DANA" <?= $payment_method == 'DANA' ? 'selected' : '' ?>>DANA</option>
                            <option value="BCA" <?= $payment_method == 'BCA' ? 'selected' : '' ?>>BCA</option>
                            <option value="GOPAY" <?= $payment_method == 'GOPAY' ? 'selected' : '' ?>>GOPAY</option>
                            <option value="MANDIRI" <?= $payment_method == 'MANDIRI' ? 'selected' : '' ?>>MANDIRI</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Jumlah Pembayaran</label>
                        <input type="number" class="form-control" name="amount" value="<?= $amount ?>" required step="0.01">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Status Pembayaran</label>
                        <select class="form-select" name="status" required>
                            <option value="confirmed" <?= $status == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            <option value="" <?= $status == '' ? 'selected' : '' ?>>(Empty)</option>
                        </select>
                    </div>

                    <div class="d-grid gap-3 d-md-flex justify-content-end">
                        <button type="button" class="btn btn-light btn-lg" onclick="history.back()">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced Form Validation
        document.querySelector('form').addEventListener('submit', function(e) {
            let isValid = true;
            
            this.querySelectorAll('.form-control').forEach(input => {
                if (!input.checkValidity()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                this.classList.add('was-validated');
                
                // Smooth scroll to first invalid input
                const firstInvalid = this.querySelector('.is-invalid');
                firstInvalid?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });

        // Real-time validation
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.toggle('is-invalid', !this.checkValidity());
            });
        });

        // Preview image before upload
        document.querySelector('input[name="user_file"]')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const previewContainer = document.querySelector('.file-preview-container');
                    if (!previewContainer) {
                        const container = document.createElement('div');
                        container.className = 'file-preview-container mt-2';
                        document.querySelector('input[name="user_file"]').after(container);
                    }
                    
                    const img = document.querySelector('.file-preview') || document.createElement('img');
                    img.className = 'file-preview img-thumbnail';
                    img.src = event.target.result;
                    
                    if (!document.querySelector('.file-preview')) {
                        document.querySelector('.file-preview-container').appendChild(img);
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>