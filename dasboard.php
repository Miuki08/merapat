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
    <!-- <link rel="stylesheet" href="dasstyle.css" type="css/text"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    /* CSS yang sudah ada */
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

    .glass-card {
        background: var(--glass);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
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

    .particle {
        position: absolute;
        pointer-events: none;
        background: radial-gradient(circle, var(--accent) 20%, transparent 100%);
        border-radius: 50%;
        z-index: 9999;
        mix-blend-mode: screen;
        animation: particle 1s linear forwards;
    }

    @keyframes particle {
        0% {
            opacity: 1;
            transform: scale(0) translate(0, 0);
        }
        100% {
            opacity: 0;
            transform: scale(2) translate(var(--tx, 0), var(--ty, 0));
        }
    }

    .navbar-glass {
        background: var(--glass);
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
    }

    .floating {
        animation: float 6s ease-in-out infinite;
    }

    .data-card {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
    }

    .data-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, var(--secondary), transparent);
        transform: rotate(45deg);
        animation: borderFlow 6s linear infinite;
    }

    .data-card-content {
        position: relative;
        z-index: 1;
        background: var(--glass);
        border-radius: 15px;
    }

    .progress-glass {
        height: 8px;
        background: rgba(124, 77, 255, 0.1);
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

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }

    @keyframes borderFlow {
        0% { transform: rotate(45deg) translateX(-50%); }
        100% { transform: rotate(45deg) translateX(50%); }
    }

    .navbar-nav .nav-item {
        margin-right: 15px;
    }

    /* Tambahkan di bagian style */
    .navbar-brand.pulse {
        animation: pulse 1.5s ease infinite;
        transform-origin: center;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Efek saat speech aktif */
    .speech-active {
        position: relative;
    }
    .speech-active::after {
        content: '';
        position: absolute;
        top: -5px;
        right: -5px;
        width: 15px;
        height: 15px;
        background: var(--accent);
        border-radius: 50%;
        animation: ping 1.5s ease infinite;
    }
</style>
<body>
    <!-- Navigation -->
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
                        <a class="nav-link nav-link-custom" href="#">
                            <i class="fa-solid fa-house me-2"></i> Laman Utama
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="meet_min_view.php">
                            <i class="fas fa-door-open me-2"></i>Ruangan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="booking_view.php">
                            <i class="fas fa-calendar-check me-2"></i>Pemesanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="user_view.php">
                            <i class="fas fa-users me-2"></i>User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="payment_view.php">
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
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mt-5 pt-5">
        <!-- Stats Section -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="data-card p-4">
                    <div class="data-card-content p-3">
                        <h3>Total Ruangan</h3>
                        <h1 class="counter" data-target="5">0</h1>
                        <div class="progress progress-glass">
                            <div class="progress-bar" style="width: 75%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="data-card p-4">
                    <div class="data-card-content p-3">
                        <h3>Total Pengguna</h3>
                        <h1 class="counter" data-target="12">0</h1>
                        <div class="progress progress-glass">
                            <div class="progress-bar" style="width: 45%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="data-card p-4">
                    <div class="data-card-content p-3">
                        <h3>Pemesanan Aktif</h3>
                        <h1 class="counter" data-target="10">0</h1>
                        <div class="progress progress-glass">
                            <div class="progress-bar" style="width: 90%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="glass-card p-4 mb-4">
            <canvas id="neoChart"></canvas>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Script (Same as original with Bootstrap adjustments) -->
    <script>
        // Particle System
        class ParticleSystem {
            constructor() {
                this.particles = [];
                this.init();
            }

            init() {
                document.addEventListener('mousemove', (e) => {
                    this.createParticle(e.clientX, e.clientY);
                });
            }

            createParticle(x, y) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Random properties
                const size = Math.random() * 15 + 5;
                const tilt = Math.random() * 10 - 5;
                const tx = (Math.random() - 0.5) * 100;
                const ty = (Math.random() - 0.5) * 100;
                
                particle.style.cssText = `
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    transform: translate(-50%, -50%) rotate(${tilt}deg);
                    --tx: ${tx}px;
                    --ty: ${ty}px;
                `;
                
                document.body.appendChild(particle);
                
                // Otomatis menghapus element setelah animasi
                setTimeout(() => {
                    particle.remove();
                }, 1000);
            }
        }

        // Animated Counters
        class SmartCounter {
            constructor() {
                this.counters = document.querySelectorAll('.counter')
                this.options = {
                    threshold: 0.5,
                    rootMargin: '0px'
                }
                this.init()
            }

            init() {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if(entry.isIntersecting && !entry.target.animated) {
                            this.animateCounter(entry.target)
                            entry.target.animated = true
                        }
                    })
                }, this.options)

                this.counters.forEach(counter => observer.observe(counter))
            }

            animateCounter(element) {
                const target = +element.dataset.target
                const duration = 2000
                const startTime = Date.now()
                
                const easeOutQuad = (t) => t*(2-t) // Easing function
                
                const update = () => {
                    const elapsed = Date.now() - startTime
                    const progress = Math.min(elapsed / duration, 1)
                    const easedProgress = easeOutQuad(progress)
                    
                    element.textContent = Math.floor(easedProgress * target).toLocaleString()
                    
                    if(progress < 1) {
                        requestAnimationFrame(update)
                    } else {
                        element.textContent = target.toLocaleString()
                    }
                }
                
                requestAnimationFrame(update)
            }
        }

    // Advanced Chart
    class InteractiveChart {
        constructor() {
            this.ctx = document.getElementById('neoChart').getContext('2d')
            this.colors = {
                primary: getComputedStyle(document.documentElement)
                    .getPropertyValue('--primary').trim(),
                accent: getComputedStyle(document.documentElement)
                    .getPropertyValue('--accent').trim()
            }
            this.init()
        }

        init() {
            const gradient = this.ctx.createLinearGradient(0, 0, 0, 400)
            gradient.addColorStop(0, `${this.colors.primary}80`)
            gradient.addColorStop(1, `${this.colors.primary}00`)

            this.chart = new Chart(this.ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Statistik Pemesanan',
                        data: [12, 19, 3, 5, 2, 3],
                        borderColor: this.colors.primary,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: gradient,
                        pointBackgroundColor: this.colors.accent,
                        pointRadius: 5,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: this.colors.primary,
                                font: { size: 14 }
                            }
                        },
                        tooltip: {
                            backgroundColor: this.colors.primary,
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: '#eee' },
                            ticks: { color: this.colors.primary }
                        },
                        y: {
                            grid: { color: '#eee' },
                            ticks: { color: this.colors.primary }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    }
                }
            })

            // Add resize listener
            window.addEventListener('resize', () => {
                this.chart.resize()
            })
        }
        }

        // Initialize components
        document.addEventListener('DOMContentLoaded', () => {
            new ParticleSystem()
            new SmartCounter()
            new InteractiveChart()
            
            // Bootstrap dropdown initialization
            
        })

         // Fungsi utama dengan penanganan voice yang lebih robust
        async function playAdminWelcomeSpeech() {
            if (!('speechSynthesis' in window)) {
                console.warn("Browser tidak mendukung text-to-speech");
                return;
            }

            // 1. Siapkan pesan sambutan
            const hour = new Date().getHours();
            const greetings = [
                { time: 5, msg: "Selamat pagi" }, 
                { time: 12, msg: "Selamat siang" },
                { time: 15, msg: "Selamat sore" },
                { time: 19, msg: "Selamat malam" }
            ];
            const greeting = greetings.find(g => hour < g.time)?.msg || "Halo";
            const message = `${greeting}, <?php echo htmlspecialchars($name); ?>. Selamat datang di Aplikasi Pemesanan Ruang Rapat.`;

            // 2. Sistem anti-block dengan fallback
            const speak = (text) => {
                return new Promise((resolve) => {
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'id-ID';
                    utterance.rate = 0.9;
                    utterance.volume = 0.7;

                    // Handle voice selection
                    const voices = speechSynthesis.getVoices();
                    const indonesianVoice = voices.find(v => v.lang.includes('id-ID')) || 
                                        voices.find(v => v.lang.includes('id-')) || 
                                        voices[0];
                    
                    if (indonesianVoice) {
                        utterance.voice = indonesianVoice;
                    }

                    utterance.onend = resolve;
                    utterance.onerror = (e) => {
                        console.error("Speech error:", e);
                        resolve();
                    };

                    // Coba teknik khusus untuk bypass autoplay block
                    try {
                        speechSynthesis.speak(utterance);
                    } catch (e) {
                        console.warn("Autoplay blocked, trying fallback...");
                        // Fallback 1: Hidden button click
                        document.dispatchEvent(new MouseEvent('click'));
                        setTimeout(() => speechSynthesis.speak(utterance), 100);
                    }
                });
            };

            // 3. Teknik chaining untuk handle voice loading
            if (speechSynthesis.getVoices().length === 0) {
                await new Promise(resolve => {
                    speechSynthesis.onvoiceschanged = resolve;
                    setTimeout(resolve, 1000); // Timeout safety
                });
            }

            // 4. Visual feedback sebelum memulai
            document.querySelector('.navbar-brand').classList.add('pulse');
            
            // 5. Main play dengan retry mechanism
            let attempts = 0;
            const playWithRetry = async () => {
                try {
                    await speak(message);
                } catch (e) {
                    if (attempts++ < 2) {
                        console.log(`Retry attempt ${attempts}`);
                        await new Promise(resolve => setTimeout(resolve, 300));
                        await playWithRetry();
                    }
                }
            };
            
            await playWithRetry();
            
            // 6. Hapus efek visual setelah selesai
            setTimeout(() => {
                document.querySelector('.navbar-brand').classList.remove('pulse');
            }, 2000);
        }

        // Inisialisasi saat halaman load dengan strategi bertahap
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Teknik khusus untuk trigger autoplay
            const handleAutoPlay = () => {
                // Persiapan visual
                const style = document.createElement('style');
                style.innerHTML = `
                    @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
                    .pulse { animation: pulse 1s ease infinite; }
                `;
                document.head.appendChild(style);

                // Trigger dengan delay untuk bypass autoplay restrictions
                setTimeout(async () => {
                    try {
                        console.log("Memulai sambutan otomatis...");
                        await playAdminWelcomeSpeech();
                    } catch (e) {
                        console.error("Gagal memutar sambutan:", e);
                    }
                }, 500);
            };

            // 2. Sistem fallback jika autoplay diblokir
            const fallbackHandler = () => {
                const fallbackMsg = document.createElement('div');
                fallbackMsg.innerHTML = `
                    <div style="position: fixed; top: 20px; right: 20px; background: var(--glass); padding: 10px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 1000;">
                        <p>Klik untuk mendengar sambutan!</p>
                    </div>
                `;
                document.body.appendChild(fallbackMsg);
                fallbackMsg.addEventListener('click', () => {
                    playAdminWelcomeSpeech();
                    fallbackMsg.remove();
                });
            };

            // 3. Coba autoplay, jika gagal tampilkan fallback
            handleAutoPlay();
            
            // 4. Deteksi jika speech tidak berjalan setelah 3 detik
            setTimeout(() => {
                if (!window.speechSynthesis.speaking) {
                    fallbackHandler();
                }
            }, 3000);
        });
    </script>
</body>
</html>