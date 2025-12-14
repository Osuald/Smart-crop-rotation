<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Crop Rotation System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-green: #22c55e;
            --dark-green: #16a34a;
            --light-green: #dcfce7;
            --earth-brown: #8b4513;
            --sky-blue: #0ea5e9;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .hero-overlay {
            background: rgba(34, 197, 94, 0.9);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .btn-get-started {
            background: white;
            color: var(--primary-green);
            padding: 15px 40px;
            font-size: 1.2rem;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .btn-get-started:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.3);
            color: var(--dark-green);
        }

        .stats-section {
            background: white;
            padding: 4rem 0;
            margin-top: -50px;
            position: relative;
            z-index: 3;
            border-radius: 20px 20px 0 0;
        }

        .stat-card {
            text-align: center;
            padding: 2rem;
            border-radius: 15px;
            background: var(--light-green);
            height: 100%;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--dark-green);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .journey-section {
            padding: 5rem 0;
            background: #f8fafc;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            color: #1f2937;
        }

        .step-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            height: 100%;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .step-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-green);
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.2);
        }

        .step-icon {
            width: 80px;
            height: 80px;
            background: var(--light-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: var(--primary-green);
        }

        .how-it-works {
            padding: 5rem 0;
            background: white;
        }

        .process-step {
            background: #f8fafc;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
        }

        .process-step:hover {
            background: var(--light-green);
            transform: translateY(-3px);
        }

        .process-icon {
            font-size: 3rem;
            color: var(--primary-green);
            margin-bottom: 1rem;
        }

        .partners-section {
            padding: 4rem 0;
            background: #f8fafc;
        }

        .partner-logo {
            width: 120px;
            height: 80px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .partner-logo:hover {
            transform: scale(1.05);
        }

        .footer {
            background: #1f2937;
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer h5 {
            color: var(--primary-green);
            margin-bottom: 1rem;
        }

        .footer a {
            color: #d1d5db;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: var(--primary-green);
        }

        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .floating-icon {
            position: absolute;
            color: rgba(255,255,255,0.1);
            animation: float 6s ease-in-out infinite;
        }

        .floating-icon:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
        .floating-icon:nth-child(2) { top: 60%; right: 15%; animation-delay: 2s; }
        .floating-icon:nth-child(3) { bottom: 30%; left: 20%; animation-delay: 4s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: rgba(34, 197, 94, 0.95); backdrop-filter: blur(10px);">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-circle-fill me-2"></i>
                Smart Crop Rotation System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#partners">Partners</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-light btn-sm" href="{{ route('register') }}" style="color: var(--primary-green);">Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section d-flex align-items-center">
        <div class="hero-overlay"></div>
        <div class="floating-elements">
            <i class="bi bi-flower1 floating-icon" style="font-size: 4rem;"></i>
            <i class="bi bi-sun floating-icon" style="font-size: 3rem;"></i>
            <i class="bi bi-droplet floating-icon" style="font-size: 2.5rem;"></i>
        </div>

        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">Welcome to Smart Crop Rotation System</h1>
                    <p class="hero-subtitle">Preserve Your Soil Quality Through Smart Crop Rotation!</p>
                    <p class="lead mb-4">Efficiently manage your crop rotation strategies for better yield and sustainability with our advanced agricultural technology.</p>
                    <!-- <button class="btn btn-get-started me-3" href="{{ route('login') }}">Get Started</button> -->
                    <a class="btn btn-get-started me-3" href="{{ route('login') }}">Get Started</a>
<a href="#how-it-works" class="btn btn-outline-light btn-lg">Learn More</a>
                </div>
          <div class="col-lg-6 text-center">
    <div class="hero-image" style="width: 100%; height: 400px; border-radius: 20px; overflow: hidden;">
        <img src="{{ asset('images/maize_crop.png') }}" alt="Crop Field" style="width: 100%; height: 100%; object-fit: cover;">
    </div>
</div>

</div>

            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">{{ $stats['total_users'] ?? 50 }}</div>
                        <div class="stat-label">Total Users</div>
                        <small class="text-muted">We Are On The Rise!</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">{{ $stats['views_today'] ?? 128 }}</div>
                        <div class="stat-label">Views Today</div>
                        <small class="text-muted">Growing Interest</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">{{ $stats['active_devices'] ?? 0 }}</div>
                        <div class="stat-label">Active Devices</div>
                        <small class="text-muted">Connected Farmers</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">{{ $stats['negative_reviews'] ?? 0 }}</div>
                        <div class="stat-label">Negative Reviews</div>
                        <small class="text-muted">100% Satisfaction</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Journey Section -->
    <section class="journey-section">
        <div class="container">
            <h2 class="section-title">Start Your Journey With Us And Enjoy Productive Soil!</h2>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="step-card">
                        <div class="step-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4>1. The Soil Quality is Preserved</h4>
                        <p>Our system ensures your soil maintains its natural nutrients and health through intelligent rotation planning.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="step-card">
                        <div class="step-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h4>2. The Soil Harvest is Increased</h4>
                        <p>Experience higher yields and better crop quality with our scientifically-backed rotation strategies.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="step-card">
                        <div class="step-icon">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <h4>3. Rotate With Confidence</h4>
                        <p>Make informed decisions about when and what to plant next with our intelligent recommendation system.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="step-card">
                        <div class="step-icon">
                            <i class="bi bi-emoji-smile"></i>
                        </div>
                        <h4>4. Enjoy More Profit</h4>
                        <p>Maximize your agricultural investment with improved efficiency and reduced farming costs.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="process-step">
                        <div class="process-icon">
                            <i class="bi bi-phone"></i>
                        </div>
                        <h5>Buy Your Device and apply it accordingly</h5>
                        <p>Get our smart monitoring device and install it in your field for real-time soil analysis.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="process-step">
                        <div class="process-icon">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <h5>Sign Up On The Platform</h5>
                        <p>Create your account and connect your device to our intelligent farming platform.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="process-step">
                        <div class="process-icon">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <h5>Get Seasonal Crop Recommendations</h5>
                        <p>Receive personalized crop rotation suggestions based on your soil data and climate conditions.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="process-step">
                        <div class="process-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h5>Practice and Enjoy Productivity</h5>
                        <p>Implement our recommendations and watch your farm's productivity and profitability soar.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section id="partners" class="partners-section">
        <div class="container">
            <h2 class="section-title">Our Partners</h2>
            <div class="row align-items-center justify-content-center">
                <div class="col-6 col-md-4 col-lg-2 mb-4">
                    <div class="partner-logo">
        <img src="{{ asset('images/minagri.png') }}" alt="Crop Field" style="width: 100%; height: 100%; object-fit: cover;">

                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2 mb-4">
                    <div class="partner-logo">
        <img src="{{ asset('images/rab.png') }}" alt="Crop Field" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2 mb-4">
                    <div class="partner-logo">
        <img src="{{ asset('images/naeb.png') }}" alt="Crop Field" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2 mb-4">
                    <div class="partner-logo">
        <img src="{{ asset('images/rca.png') }}" alt="Crop Field" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2 mb-4">
                    <div class="partner-logo">
        <img src="{{ asset('images/rica.png') }}" alt="Crop Field" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5>Contact Us</h5>
                    <p><i class="bi bi-telephone me-2"></i> +250 784857700</p>
                    <p><i class="bi bi-envelope me-2"></i> info@crop.com</p>
                    <p><i class="bi bi-geo-alt me-2"></i> KN 7 Kigali Rwanda</p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Quick Links</h5>
                    <p><a href="#">Rwanda Soil Information System</a></p>
                    <p><a href="#">Smart Re-agriculture</a></p>
                    <p><a href="#">Plowing Forecast</a></p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Support</h5>
                    <p><a href="#">Device Monitoring</a></p>
                    <p><a href="#">Pest Management</a></p>
                    <p><a href="#">Technical Support</a></p>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2025 Smart Crop Rotation System. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="me-3"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="me-3"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="me-3"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(34, 197, 94, 0.98)';
            } else {
                navbar.style.background = 'rgba(34, 197, 94, 0.95)';
            }
        });

        // Add entrance animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards and sections
        document.querySelectorAll('.stat-card, .step-card, .process-step').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });
    </script>
</body>
</html>
