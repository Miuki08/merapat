<?php 

require_once 'users.php';
$user = new Users;

if (isset($_GET['user_id'])) {
    $user->user_id = $_GET['user_id'];
    $data = $user->getByID();

    if ($data && $result = $data->fetch_assoc()) {
        $user_id = $result['user_id'];
        $name = $result['name'];
        $email = $result['email'];
        $password = $result['password'];
        $role = $result['role'];
    } else {
        echo "<script>
                alert('Data tidak ditemukan');
                location.href = 'user_view.php';
              </script>";
        exit;
    }
} else {
    echo "<script>
            alert('User  ID tidak valid');
            location.href = 'user_view.php';
          </script>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User | MERAPAT ID</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Light background for contrast */
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.8); /* Semi-transparent background */
            border-radius: 15px; /* Rounded corners */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Soft shadow */
            padding: 30px; /* Padding for inner spacing */
        }

        .form-label {
            font-weight: bold; /* Bold labels for emphasis */
            color: #343a40; /* Darker color for labels */
        }

        .form-control {
            border: 1px solid #ced4da; /* Light border */
            border-radius: 10px; /* Rounded input fields */
            transition: border-color 0.3s; /* Smooth transition for focus */
        }

        .form-control:focus {
            border-color: #007bff; /* Blue border on focus */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Glow effect */
        }

        .btn {
            border-radius: 10px; /* Rounded buttons */
            padding: 10px 20px; /* Padding for buttons */
        }

        .btn-primary {
            background-color: #007bff; /* Primary button color */
            border: none; /* Remove border */
        }

        .btn-light {
            background-color: #f8f9fa; /* Light button color */
            border: 1px solid #ced4da; /* Light border */
        }

        .btn-light:hover {
            background-color: #e2e6ea; /* Darker on hover */
        }

        .is-invalid {
            border-color: #dc3545; /* Red border for invalid inputs */
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
                            Bijaklah dalam mengubah data user, karena data user adalah data yang sangat penting.
                            Jangan sampai data user terhapus atau berubah tanpa sepengetahuan user.
                            <i class="fas fa-quote-right ms-2"></i>
                        </p>
                        <div class="mt-4 d-flex gap-2">
                            <span class="badge bg-light text-primary p-3">Profesional</span>
                            <span class="badge bg-light text-primary p-3">Terpercaya</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="col-lg-6">
            <form action="users_action.php?action=update" method="post" class="glass-panel">
                    <h2 class="text-center mb-4 display-5 fw-bold text-primary">
                        <i class="fas fa-edit me-2"></i>Update Data User
                    </h2>
                    
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">

                    <div class="mb-4">
                        <label class="form-label">Nama Pengguna</label>
                        <input type="text" class="form-control" name="name" value="<?= $name ?>" required>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Email Pengguna</label>
                            <input type="email" class="form-control" name="email" value="<?= $email ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="text" inputmode="numeric" class="form-control" name="password" value="<?= $password ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Role Pengguna</label>
                        <select class="form-select" name="role" required>
                            <option value="manager" <?= $role == 'manager' ? 'selected' : ''; ?>>Manager</option>
                            <option value="officer" <?= $role == 'officer' ? 'selected' : ''; ?>>Officer</option>
                            <option value="customer" <?= $role == 'customer' ? 'selected' : ''; ?>>Customer</option>
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
    </script>
</body>
</html> 