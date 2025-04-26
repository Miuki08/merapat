<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Add | MERAPAT ID</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #7c4dff;
            --secondary: #b388ff;
            --accent: #ff80ab;
            --background: linear-gradient(135deg, #f0e6ff 0%, #f8f4ff 100%);
            --glass: rgba(255, 255, 255, 0.95);
            --bs-primary: var(--primary);
            --bs-link-color: var(--primary);
        }

        body {
            background: var(--background);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
            position: relative;
        }

        .hero-section {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            border-radius: 20px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
            margin: 2rem auto;
            transition: transform 0.3s ease;
        }

        .hero-section:hover {
            transform: translateY(-5px);
        }

        .hero-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 20px 20px 0 0;
            position: relative;
        }

        .hero-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.4) 100%);
        }

        .hero-content {
            padding: 2rem;
            background: var(--glass);
            backdrop-filter: blur(15px);
            border-radius: 0 0 20px 20px;
            position: relative;
        }

        .form-container {
            background: var(--glass);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .form-control {
            border: 2px solid #e0d6ff;
            border-radius: 10px;
            padding: 12px 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(124, 77, 255, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            padding: 15px 35px;
            border-radius: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(124, 77, 255, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #b388ff, #d1b3ff);
            border: none;
            padding: 15px 35px;
            border-radius: 12px;
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(179, 136, 255, 0.4);
            color: white;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .floating-icon {
            position: absolute;
            opacity: 0.1;
            pointer-events: none;
        }

        .form-group {
            position: relative;
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row g-5">
            <!-- Hero Section -->
            <div class="col-lg-6">
                <div class="hero-section animate-float">
                    <img src="ideas.jpg" alt="Contoh Ruangan" class="hero-image">
                    <div class="hero-content">
                        <h1 class="text-primary mb-4 display-4 fw-bold">MERAPAT</h1>
                        <p class="lead fs-5">
                            <i class="fas fa-quote-left text-primary me-2"></i>
                            Jadikan kepercayaan pelanggan sebagai motivasi kita. 
                            Deskripsikan dengan jelas mengenai fasilitas serta lokasi dari ruangan yang akan disewakan.
                            <i class="fas fa-quote-right text-primary ms-2"></i>
                        </p>
                        <div class="mt-4 d-flex gap-2">
                            <div class="badge bg-primary bg-opacity-10 text-primary p-3">Profesional</div>
                            <div class="badge bg-primary bg-opacity-10 text-primary p-3">Terpercaya</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="col-lg-6">
                <form action="meet_action.php?action=add" method="post" class="form-container" id="roomForm">
                    <h1 class="text-center mb-4 text-primary display-5 fw-bold">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Ruangan
                    </h1>
                    
                    <div class="mb-4">
                        <label for="room_name" class="form-label">
                            <i class="fas fa-door-open"></i>Nama Ruangan
                        </label>
                        <input type="text" class="form-control" id="room_name" name="room_name" required>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="capacity" class="form-label">
                                <i class="fas fa-users"></i>Kapasitas
                            </label>
                            <input type="number" class="form-control" id="capacity" name="capacity" required>
                        </div>
                        <div class="col-md-6">
                            <label for="total_price" class="form-label">
                                <i class="fas fa-tag"></i>Harga
                            </label>
                            <input type="number" class="form-control" id="total_price" name="total_price" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="location" class="form-label">
                            <i class="fas fa-map-marker-alt"></i>Lokasi
                        </label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>

                    <div class="mb-4">
                        <label for="facilities" class="form-label">
                            <i class="fas fa-wifi"></i>Fasilitas
                        </label>
                        <textarea class="form-control" id="facilities" name="facilities" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">
                            <i class="fas fa-info-circle"></i>Status
                        </label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Ready">Tersedia</option>
                            <option value="Booking">Dipesan</option>
                            <option value="Repair">Perbaikan</option>
                        </select>
                    </div>

                    <div class="d-grid gap-3 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary btn-lg" onclick="history.back()">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('roomForm').addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input, select, textarea');
            let isValid = true;

            inputs.forEach(input => {
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Harap isi bidang ini';
                
                if (!input.nextElementSibling?.classList.contains('invalid-feedback')) {
                    input.after(feedback);
                }

                if (!input.checkValidity()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                    input.nextElementSibling.style.display = 'block';
                } else {
                    input.classList.remove('is-invalid');
                    input.nextElementSibling.style.display = 'none';
                }
            });

            if (!isValid) {
                e.preventDefault();
                this.classList.add('was-validated');
                const firstInvalid = this.querySelector('.is-invalid');
                firstInvalid?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        let timeout;
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    this.classList.toggle('is-invalid', !this.checkValidity());
                }, 300);
            });
        });
    </script>
</body>
</html>