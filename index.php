<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPLO Ilagan - Business Permit and Licensing Office</title>
    <link rel="stylesheet" href="src/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="src/assets/vendors/css/vendor.bundle.base.css">
    <link rel="shortcut icon" href="src/assets/images/favicon.png" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .hero-container {
            max-width: 1200px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-content {
            color: white;
            z-index: 1;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-section img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .hero-content h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .hero-content .subtitle {
            font-size: 1.25rem;
            opacity: 0.95;
            margin-bottom: 1rem;
        }

        .hero-content .location {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        /* Admin Card */
        .admin-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .admin-card .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: white;
        }

        .admin-card h2 {
            font-size: 1.75rem;
            color: #333;
            margin-bottom: 0.75rem;
        }

        .admin-card p {
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .admin-btn {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .admin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        /* Info Section */
        .info-section {
            background: white;
            padding: 5rem 2rem;
        }

        .info-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 3rem;
            font-weight: 700;
        }

        .vision-mission {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 4rem;
        }

        .vm-card {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 15px;
            border-left: 4px solid #667eea;
        }

        .vm-card h3 {
            color: #667eea;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .vm-card p {
            color: #555;
            line-height: 1.8;
        }

        /* Services */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .service-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            border-radius: 15px;
            color: white;
            transition: transform 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-card i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .service-card h4 {
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
        }

        /* Contact Section */
        .contact-section {
            background: #f8f9fa;
            padding: 3rem 2rem;
            border-radius: 20px;
            margin-bottom: 3rem;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .contact-info {
            display: grid;
            gap: 1.5rem;
        }

        .contact-item {
            display: flex;
            align-items: start;
            gap: 1rem;
        }

        .contact-item i {
            font-size: 1.5rem;
            color: #667eea;
            margin-top: 0.25rem;
        }

        .contact-item div h4 {
            color: #333;
            margin-bottom: 0.25rem;
        }

        .contact-item div p {
            color: #666;
            margin: 0;
        }

        .officer-info {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .officer-info h3 {
            color: #667eea;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .officer-info h4 {
            color: #333;
            font-size: 1.125rem;
            margin-bottom: 0.25rem;
        }

        .officer-info p {
            color: #666;
            font-size: 0.9375rem;
        }

        /* Social Links */
        .social-links {
            text-align: center;
            padding: 2rem;
        }

        .social-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            background: #1877f2;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(24, 119, 242, 0.3);
        }

        .social-btn:hover {
            background: #166fe5;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(24, 119, 242, 0.4);
        }

        .social-btn i {
            font-size: 1.5rem;
        }

        /* Footer */
        footer {
            background: #191920;
            color: white;
            text-align: center;
            padding: 2rem;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .hero-container {
                grid-template-columns: 1fr;
                gap: 3rem;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .vision-mission {
                grid-template-columns: 1fr;
            }

            .contact-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .admin-card {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-container">
            <div class="hero-content">
                <div class="logo-section">
                    <img src="src/assets/images/logos/bplo.jpg" alt="BPLO Logo">
                </div>
                <h1>BPLO ILAGAN</h1>
                <p class="subtitle">Business Permit and Licensing Office</p>
                <p class="location">City of Ilagan, Isabela</p>
            </div>

            <div class="admin-card">
                <div class="icon">
                    <i class="mdi mdi-shield-account"></i>
                </div>
                <h2>Admin Portal</h2>
                <p>For BPLO staff and administrators to manage business permits, payments, and licensing operations.</p>
                <a href="login.php" class="admin-btn">Admin Login</a>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section class="info-section">
        <div class="info-container">
            <!-- Vision & Mission -->
            <h2 class="section-title">Our Vision & Mission</h2>
            <div class="vision-mission">
                <div class="vm-card">
                    <h3>Vision</h3>
                    <p>We envision the city of Ilagan as premiere business hub of the north; the most business friendly fortified by the humanized taxation.</p>
                </div>
                <div class="vm-card">
                    <h3>Mission</h3>
                    <p>The BPLO shall provide the catalyst for interdependent of the city government services unlimited business opportunities and growing economy ultimately for the welfare of all the Ilaguenos.</p>
                </div>
            </div>

            <!-- Services -->
            <h2 class="section-title">Services Offered</h2>
            <div class="services-grid">
                <div class="service-card">
                    <i class="mdi mdi-file-document"></i>
                    <h4>Business Permit Issuance</h4>
                    <p>New Business / Renewal / Special Lane</p>
                </div>
                <div class="service-card">
                    <i class="mdi mdi-certificate"></i>
                    <h4>Retirement Certificate</h4>
                    <p>Issuance of Business Retirement Certificate</p>
                </div>
                <div class="service-card">
                    <i class="mdi mdi-bike"></i>
                    <h4>Tricycle Permit</h4>
                    <p>Tricycle Permit / Franchise Registration</p>
                </div>
                <div class="service-card">
                    <i class="mdi mdi-account-check"></i>
                    <h4>Mayor's Clearance</h4>
                    <p>Working Permit Availment</p>
                </div>
            </div>

            <!-- Contact Section -->
            <h2 class="section-title">Contact Information</h2>
            <div class="contact-section">
                <div class="contact-grid">
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="mdi mdi-map-marker"></i>
                            <div>
                                <h4>Address</h4>
                                <p>Ground Floor Legislative Bldg., City Hall Complex<br>City of Ilagan, Isabela</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="mdi mdi-email"></i>
                            <div>
                                <h4>Email</h4>
                                <p>bplo@cityofilagan.gov.ph</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="mdi mdi-phone"></i>
                            <div>
                                <h4>Contact Numbers</h4>
                                <p>TM: 0906 643 3466<br>DITO: 0993 483 3561</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="mdi mdi-clock"></i>
                            <div>
                                <h4>Service Hours</h4>
                                <p>8:00 AM - 5:00 PM<br>Monday to Friday (except Holidays)</p>
                            </div>
                        </div>
                    </div>

                    <div class="officer-info">
                        <h3>City Business Permits & Licensing Office</h3>
                        <h4>SHARON KEITH G. PANAJON</h4>
                        <p>City Business Permits & Licensing Officer</p>
                    </div>
                </div>
            </div>

            <!-- Social Links -->
            <div class="social-links">
                <a href="https://www.facebook.com/CityofIlaganBPLO" target="_blank" class="social-btn">
                    <i class="mdi mdi-facebook"></i>
                    <span>Visit our Facebook Page</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> BPLO Ilagan. All rights reserved.</p>
        <p>City of Ilagan, Isabela</p>
    </footer>
</body>
</html>
