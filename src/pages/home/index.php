<?php
/**
 * ORLMS - Public Landing Page View
 *
 * Restored to original structure (Hero, 4 Boxes, Timeline, Footer).
 * Optimized with premium design cards representing the official CSJDM Sangguniang Panlungsod Committees,
 * official CSJDM logo, official CSJDM color palette, and fixed layout grid clashes.
 * Background image: Official San Jose del Monte Government Center with a left-to-right text-readability gradient.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ordinance and Resolution Lifecycle Management System (ORLMS) - City Government of San Jose del Monte, Bulacan.">
    <title>Home | ORLMS CSJDM</title>

    <?php $gaCode = (defined('GA_TRACKING_ID') && !empty(trim(GA_TRACKING_ID))) ? trim(GA_TRACKING_ID) : 'G-M0BYB5DP6M'; ?>
    <!-- Google Analytics (GA4) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $gaCode ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= $gaCode ?>');
    </script>

    <?php if (defined('CLARITY_PROJECT_ID') && !empty(CLARITY_PROJECT_ID)): ?>
    <!-- Microsoft Clarity -->
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "<?= CLARITY_PROJECT_ID ?>");
    </script>
    <?php endif; ?>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- ORLMS Global Stylesheet -->
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">

    <style>
        :root {
            /* Official CSJDM Color Palette */
            --color-lgu-blue: #0C2340;        /* Deep Presidential Blue */
            --color-lgu-blue-light: #16365c;
            --color-lgu-sky: #0084FF;         /* Vibrant Interactive Blue */
            --color-lgu-gold: #F2A900;        /* Golden Yellow / Sunburst */
            --color-lgu-gold-light: #fff5df;
            --color-lgu-accent-dark: #cc8f00;
            --color-lgu-bg: #F8F9FA;
            --color-lgu-border: #e2e8f0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-lgu-bg);
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        /* Fix Bootstrap grid clash with custom stylesheet .row { display: grid; } */
        .row {
            display: flex !important;
            flex-wrap: wrap !important;
        }

        /* 1. Main Header / Nav */
        .main-header {
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 12px 24px;
            border-bottom: 3px solid var(--color-lgu-gold);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .brand-section {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }
        .brand-section img {
            object-fit: contain;
            transition: transform 0.2s ease;
        }
        .brand-section:hover img {
            transform: scale(1.05);
        }
        .brand-text h1 {
            font-size: 19px;
            font-weight: 800;
            color: var(--color-lgu-blue);
            margin: 0;
            line-height: 1.2;
            letter-spacing: -0.3px;
        }
        .brand-text p {
            font-size: 11px;
            font-weight: 700;
            color: var(--color-lgu-gold);
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        
        /* 2. Hero Section (City Hall Photo Background with Left-to-Right Readability Overlay) */
        .hero-section {
            background: linear-gradient(90deg, rgba(12, 35, 64, 0.94) 0%, rgba(12, 35, 64, 0.75) 50%, rgba(12, 35, 64, 0.15) 100%), 
                        url('<?= APP_URL ?>/public/img/csjdm_cityhall.webp') no-repeat center center;
            background-size: cover;
            background-position: center 30%; /* Shift background down slightly to frame the cityhall well */
            color: #ffffff;
            padding: 160px 0 185px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 5px solid var(--color-lgu-gold);
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 80% 20%, rgba(0, 132, 255, 0.18) 0%, transparent 60%);
            pointer-events: none;
        }
        .hero-tag {
            background-color: rgba(242, 169, 0, 0.2);
            color: var(--color-lgu-gold);
            border: 1px solid rgba(242, 169, 0, 0.4);
            font-size: 11.5px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 22px;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        .hero-title {
            font-size: 42px;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 24px;
            letter-spacing: -0.6px;
            text-shadow: 0 2px 5px rgba(0,0,0,0.4);
            max-width: 820px;
        }
        .hero-subtitle {
            font-size: 17px;
            color: rgba(255, 255, 255, 0.92);
            margin-bottom: 40px;
            line-height: 1.65;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
            max-width: 720px;
        }
        .btn-custom-gold {
            background-color: rgba(242, 169, 0, 0.15); /* Transparent gold glass */
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: var(--color-lgu-gold);
            font-weight: 700;
            border: 2px solid var(--color-lgu-gold);
            padding: 12px 28px;
            border-radius: 6px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 10px rgba(242, 169, 0, 0.15);
            text-decoration: none;
            display: inline-block;
        }
        .btn-custom-gold:hover {
            background-color: var(--color-lgu-gold);
            border-color: var(--color-lgu-gold);
            color: var(--color-lgu-blue);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(242, 169, 0, 0.35);
        }
        .btn-custom-outline {
            background-color: transparent;
            color: #ffffff;
            font-weight: 600;
            border: 2px solid rgba(255, 255, 255, 0.45);
            padding: 12px 28px;
            border-radius: 6px;
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-custom-outline:hover {
            background-color: rgba(255, 255, 255, 0.12);
            border-color: #ffffff;
            color: #ffffff;
            transform: translateY(-2px);
        }

        /* 3. Arya Slogan Banner */
        .arya-banner {
            background-color: var(--color-lgu-gold);
            color: var(--color-lgu-blue);
            font-weight: 800;
            text-align: center;
            padding: 10px 0;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        /* 4. Premium Features Section */
        .section-title {
            color: var(--color-lgu-blue);
            font-weight: 800;
            font-size: 32px;
            margin-bottom: 12px;
            position: relative;
            display: inline-block;
        }
        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background-color: var(--color-lgu-gold);
            margin: 12px auto 0 auto;
            border-radius: 2px;
        }
        .feature-cards-grid {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 20px !important;
        }
        .feature-card-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            flex: none !important;
            padding: 0 !important;
        }
        @media (max-width: 640px) {
            .feature-cards-grid {
                grid-template-columns: 1fr !important;
            }
        }
        .feature-card {
            background-color: #ffffff;
            border: 1px solid var(--color-lgu-border);
            border-top: 4px solid var(--color-lgu-blue);
            border-radius: 8px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(12, 35, 64, 0.03);
            transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
            height: 100%;
            position: relative;
            text-decoration: none;
            display: block;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 36px rgba(12, 35, 64, 0.08);
            border-top-color: var(--color-lgu-gold);
        }
        .feature-icon-wrapper {
            background-color: #f1f5f9;
            color: var(--color-lgu-blue);
            width: 64px;
            height: 64px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            font-size: 26px;
            transition: all 0.3s ease;
        }
        .feature-card:hover .feature-icon-wrapper {
            background-color: var(--color-lgu-gold-light);
            color: var(--color-lgu-accent-dark);
            transform: rotate(5deg);
        }
        .feature-card h3 {
            font-size: 19px;
            font-weight: 800;
            color: var(--color-lgu-blue);
            margin-bottom: 14px;
            letter-spacing: -0.3px;
        }
        .feature-card p {
            font-size: 13.5px;
            color: #475569;
            line-height: 1.6;
            margin: 0;
        }
        .feature-card-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            color: var(--color-lgu-sky);
            margin-top: 20px;
            transition: gap 0.2s ease;
        }
        .feature-card:hover .feature-card-link {
            gap: 10px;
        }

        /* 5. Premium Connected Timeline */
        .timeline-wrapper {
            position: relative;
            padding: 40px 0;
        }
        /* Dotted horizontal line connecting steps on desktop */
        .timeline-wrapper::before {
            content: '';
            position: absolute;
            top: 62px;
            left: 8%;
            right: 8%;
            height: 2px;
            border-top: 2px dashed rgba(12, 35, 64, 0.18);
            z-index: 1;
        }
        .timeline-step {
            text-align: center;
            position: relative;
            z-index: 2;
            padding: 0 15px;
        }
        .timeline-number {
            background-color: var(--color-lgu-blue);
            color: #ffffff;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 17px;
            margin: 0 auto 20px auto;
            border: 3px solid var(--color-lgu-gold);
            position: relative;
            box-shadow: 0 4px 10px rgba(12, 35, 64, 0.2);
            transition: all 0.3s ease;
        }
        .timeline-step:hover .timeline-number {
            background-color: var(--color-lgu-gold);
            border-color: var(--color-lgu-blue);
            color: var(--color-lgu-blue);
            transform: scale(1.1);
        }
        .timeline-step h4 {
            font-size: 16px;
            font-weight: 800;
            color: var(--color-lgu-blue);
            margin-bottom: 10px;
            letter-spacing: -0.2px;
        }
        .timeline-step p {
            font-size: 13px;
            color: #475569;
            line-height: 1.5;
            margin: 0;
            padding: 0 5px;
        }

        /* Responsive timeline overrides */
        @media (max-width: 991px) {
            .timeline-wrapper::before {
                display: none;
            }
            .timeline-step {
                margin-bottom: 30px;
            }
        }
        
        /* 6. Gov Footer */
        .gov-footer {
            background-color: #0b1521;
            color: #ced4da;
            padding: 60px 0 35px 0;
            font-size: 13.5px;
            border-top: 5px solid var(--color-lgu-gold);
        }
        .gov-footer h5 {
            color: #ffffff;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 22px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 10px;
        }
        .gov-footer a {
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.2s;
        }
        .gov-footer a:hover {
            color: var(--color-lgu-gold);
        }
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 25px;
            margin-top: 45px;
            font-size: 11px;
            text-align: center;
            color: #6c757d;
            letter-spacing: 0.3px;
        }

        /* Mobile Responsiveness Enhancements */
        @media (max-width: 768px) {
            .main-header {
                padding: 8px 12px;
            }
            .hero-section {
                padding: 45px 0 55px 0;
            }
            .hero-title {
                font-size: 23px;
                line-height: 1.3;
                margin-bottom: 14px;
            }
            .hero-subtitle {
                font-size: 13px;
                line-height: 1.5;
                margin-bottom: 20px;
            }
            .hero-tag {
                font-size: 10px;
                padding: 4px 10px;
                margin-bottom: 12px;
            }
            .btn-custom-gold, .btn-custom-outline {
                width: 100%;
                text-align: center;
                padding: 10px 14px;
                font-size: 13px;
            }
            .chatbot-wrapper {
                bottom: 16px !important;
                right: 16px !important;
            }
            #chatbot-toggle-btn {
                width: 48px !important;
                height: 48px !important;
                font-size: 20px !important;
            }
            #chatbot-box {
                width: calc(100vw - 32px) !important;
                right: 0 !important;
                bottom: 65px !important;
                height: 430px !important;
            }
        }

        @media (max-width: 480px) {
            .brand-section {
                gap: 8px;
            }
            .brand-section img {
                width: 36px;
                height: 36px;
            }
            .brand-text h1 {
                font-size: 13.5px;
            }
            .brand-text p {
                font-size: 8px;
                letter-spacing: 0.1px;
            }
            .main-header .btn {
                padding: 5px 8px !important;
                font-size: 11px !important;
            }
            .header-btn-text-full {
                display: none;
            }
            .header-btn-text-short {
                display: inline;
            }
        }

        .header-btn-text-short {
            display: none;
        }

        /* Citizen Portal Enhancements CSS */
        .topic-chips-wrapper {
            margin-top: 24px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }
        .topic-chips-label {
            font-size: 12px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.75);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-right: 4px;
        }
        .topic-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(4px);
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 13px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .topic-chip:hover {
            background: var(--color-lgu-gold);
            color: var(--color-lgu-blue);
            border-color: var(--color-lgu-gold);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(242, 169, 0, 0.3);
        }
        
        /* Stats Cards Section */
        .stats-section {
            background-color: #ffffff;
            padding: 40px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .stats-cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            width: 100%;
        }
        @media (max-width: 992px) {
            .stats-cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 576px) {
            .stats-cards-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
        .stat-card {
            background: #ffffff;
            border: 1px solid var(--color-lgu-border);
            border-top: 4px solid var(--color-lgu-blue);
            border-radius: 12px;
            padding: 22px 18px;
            text-align: center;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(12, 35, 64, 0.08);
            border-top-color: var(--color-lgu-gold);
        }
        .stat-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: var(--color-lgu-gold-light);
            color: var(--color-lgu-blue);
            font-size: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px auto;
        }
        .stat-card-number {
            font-size: 28px;
            font-weight: 800;
            color: var(--color-lgu-blue);
            line-height: 1.1;
            margin-bottom: 4px;
        }
        .stat-card-label {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
        }

        /* 3-Step Citizen Guide */
        .guide-step-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 28px 20px;
            text-align: center;
            height: 100%;
            transition: all 0.25s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
            position: relative;
        }
        .guide-step-card:hover {
            border-color: var(--color-lgu-sky);
            box-shadow: 0 10px 25px rgba(0, 132, 255, 0.08);
            transform: translateY(-3px);
        }
        .guide-step-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--color-lgu-blue);
            color: var(--color-lgu-gold);
            font-size: 15px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            border: 2px solid var(--color-lgu-gold);
        }
        .guide-step-card h4 {
            font-size: 17px;
            font-weight: 700;
            color: var(--color-lgu-blue);
            margin-bottom: 8px;
        }
        .guide-step-card p {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.5;
            margin: 0;
        }

        /* Citizen FAQ Accordion */
        .faq-accordion .accordion-item {
            border: 1px solid #e2e8f0;
            border-radius: 10px !important;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .faq-accordion .accordion-button {
            font-weight: 700;
            font-size: 15px;
            color: var(--color-lgu-blue);
            background-color: #ffffff;
            padding: 16px 20px;
        }
        .faq-accordion .accordion-button:not(.collapsed) {
            color: var(--color-lgu-blue);
            background-color: var(--color-lgu-gold-light);
            box-shadow: none;
        }
        .faq-accordion .accordion-body {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
            background-color: #ffffff;
            padding: 18px 20px;
        }
    </style>
</head>
<body>

    <!-- 1. Main Header / Navigation -->
    <header class="main-header">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="<?= APP_URL ?>/" class="brand-section">
                <!-- Official Seal of the City Government of San Jose del Monte -->
                <img src="<?= APP_URL ?>/public/img/csjdm_logo.webp" alt="CSJDM Logo" width="52" height="52">
                <div class="brand-text">
                    <h1>ORLMS CSJDM</h1>
                    <p>City Government of San Jose del Monte</p>
                </div>
            </a>
            
            <div class="d-flex gap-2 align-items-center">
                <a href="<?= APP_URL ?>/portal" class="btn btn-sm btn-outline-dark px-2 px-sm-3 py-1.5 fw-semibold" style="font-size: 12px; border-radius: 4px; border-color: #dee2e6;">
                    <i class="bi bi-search me-1"></i> <span class="header-btn-text-full">Public Search</span><span class="header-btn-text-short">Search</span>
                </a>
                <?php if (is_internal_portal()): ?>
                <a href="<?= APP_URL ?>/auth/login" class="btn btn-sm btn-primary px-2 px-sm-3 py-1.5 fw-semibold" style="font-size: 12px; border-radius: 4px; background-color: var(--color-lgu-blue); border-color: var(--color-lgu-blue);">
                    <i class="bi bi-box-arrow-in-right me-1"></i> <span class="header-btn-text-full">Staff Login</span><span class="header-btn-text-short">Login</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- 2. Hero Banner Section (With CSJDM City Hall Photo Background - Clean Text Overlay) -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 text-start">
                    <span class="hero-tag">
                        Sangguniang Panlungsod Gateway
                    </span>
                    <h2 class="hero-title">Ordinance and Resolution Lifecycle Management System</h2>
                    <p class="hero-subtitle">
                        Ang opisyal na portal ng Lungsod ng San Jose del Monte para sa pagsubaybay, pagsusuri, at AI-powered validation ng mga lokal na ordinansa at resolusyon.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="<?= APP_URL ?>/portal" class="btn btn-custom-gold">
                            <i class="bi bi-search me-2"></i> Maghanap sa Public Registry
                        </a>
                        <?php if (is_internal_portal()): ?>
                        <a href="<?= APP_URL ?>/auth/login" class="btn btn-custom-outline">
                            <i class="bi bi-lock me-2"></i> Legislative Staff Portal
                        </a>
                        <?php endif; ?>
                    </div>

                    <!-- Popular Topic Search Chips for Citizens -->
                    <div class="topic-chips-wrapper">
                        <span class="topic-chips-label"><i class="bi bi-fire me-1"></i> POPULAR TOPICS:</span>
                        <a href="<?= APP_URL ?>/portal?search=Tricycle" class="topic-chip"><i class="bi bi-search"></i> #TricycleFare</a>
                        <a href="<?= APP_URL ?>/portal?search=Senior" class="topic-chip"><i class="bi bi-search"></i> #SeniorCitizen</a>
                        <a href="<?= APP_URL ?>/portal?search=Business" class="topic-chip"><i class="bi bi-search"></i> #BusinessPermit</a>
                        <a href="<?= APP_URL ?>/portal?search=Curfew" class="topic-chip"><i class="bi bi-search"></i> #Curfew</a>
                        <a href="<?= APP_URL ?>/portal?search=Waste" class="topic-chip"><i class="bi bi-search"></i> #WasteManagement</a>
                        <a href="<?= APP_URL ?>/portal?search=Health" class="topic-chip"><i class="bi bi-search"></i> #HealthOrdinance</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Arya Slogan Banner -->
    <div class="arya-banner">
        Arya San Joseño! • Disiplinado, Progresibo, at Makabagong Pamahalaan
    </div>

    <!-- 3.1 Public Transparency Live Stats Counter -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-cards-grid">
                <div class="stat-card">
                    <div class="stat-card-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="stat-card-number"><?= number_format($stats['ordinances'] ?? 0) ?></div>
                    <div class="stat-card-label">Napasang Ordinansa</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon">
                        <i class="bi bi-file-earmark-check"></i>
                    </div>
                    <div class="stat-card-number"><?= number_format($stats['resolutions'] ?? 0) ?></div>
                    <div class="stat-card-label">Pinagtibay na Resolusyon</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="stat-card-number"><?= number_format($stats['committees'] ?? 18) ?></div>
                    <div class="stat-card-label">Aktibong Komite</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="stat-card-number">100%</div>
                    <div class="stat-card-label">Libreng Access ng Publiko</div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Premium Legislative Committees Section (The 4 Boxes - Tailored for CSJDM LGU) -->
    <section class="py-5 my-3">
        <div class="container text-center">
            <h3 class="section-title">Mga Pangunahing Lupon at Kategorya</h3>
            <p class="text-muted mx-auto mb-5" style="max-width: 600px; font-size:15px; line-height:1.6;">
                I-click ang kategorya upang maghanap ng mga opisyal na ordinansa at resolusyon na pinagtibay sa ilalim ng bawat komite.
            </p>
            
            <div class="feature-cards-grid text-start">
                <!-- Card 1: Laws & Rules -->
                <div class="feature-card-wrapper">
                    <a href="<?= APP_URL ?>/portal?search=Laws" class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-bank"></i>
                        </div>
                        <h3>Kautusan at Patakaran</h3>
                        <p>Sumasaklaw sa pagrepaso ng mga lokal na ordinansa, kapayapaan at kaayusan, mga batas-trapiko, at internal na tuntunin sa lungsod.</p>
                        <span class="feature-card-link">Tingnan ang Batas <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>

                <!-- Card 2: Finance & Appropriations -->
                <div class="feature-card-wrapper">
                    <a href="<?= APP_URL ?>/portal?search=Finance" class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <h3>Badyet at Pananalapi</h3>
                        <p>Saklaw ang taunang badyet ng lungsod, mga lokal na buwis, alokasyon ng pondo para sa imprastraktura, at programang pang-ekonomiya.</p>
                        <span class="feature-card-link">Tingnan ang Badyet <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>

                <!-- Card 3: Health & Environment -->
                <div class="feature-card-wrapper">
                    <a href="<?= APP_URL ?>/portal?search=Health" class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-heart-pulse"></i>
                        </div>
                        <h3>Kalusugan at Kalikasan</h3>
                        <p>Tumutukoy sa mga ordinansa sa kalusugan, sanitasyon, pangangalaga sa kapaligiran, at ecological solid waste management sa CSJDM.</p>
                        <span class="feature-card-link">Tingnan ang Kalusugan <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>

                <!-- Card 4: Education & Social Welfare -->
                <div class="feature-card-wrapper">
                    <a href="<?= APP_URL ?>/portal?search=Education" class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-book"></i>
                        </div>
                        <h3>Edukasyon at Kapakanan</h3>
                        <p>Programa sa pampublikong paaralan, kultura, sining, turismo, at pagsuporta sa kapakanan ng kabataan at senior citizens.</p>
                        <span class="feature-card-link">Tingnan ang Edukasyon <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>

                <!-- Card 5: Infrastructure & Zoning -->
                <div class="feature-card-wrapper">
                    <a href="<?= APP_URL ?>/portal?search=Infrastructure" class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-buildings"></i>
                        </div>
                        <h3>Imprastraktura at Lupaing Pampubliko</h3>
                        <p>Mga ordinansa ukol sa pampublikong pasilidad, land use zoning, pabahay, kalsada, at urban development sa lungsod.</p>
                        <span class="feature-card-link">Tingnan ang Imprastraktura <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>

                <!-- Card 6: Trade, Business & Labor -->
                <div class="feature-card-wrapper">
                    <a href="<?= APP_URL ?>/portal?search=Trade" class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <h3>Komersyo at Trabaho</h3>
                        <p>Promosyon ng lokal na negosyo, pamilihan, kabuhayan, mga lisensya sa merkado, at oportunidad sa paggawa sa CSJDM.</p>
                        <span class="feature-card-link">Tingnan ang Komersyo <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 4.1 Citizen How-It-Works Guide -->
    <section class="py-5 bg-light border-top border-bottom">
        <div class="container text-center">
            <h3 class="section-title">Paano Gamitin ang Public Portal?</h3>
            <p class="text-muted mx-auto mb-5" style="max-width: 600px; font-size:15px; line-height:1.6;">
                Mabilis at simpleng paraan para sa mga San Joseño upang maghanap at mag-download ng mga opisyal na lokal na batas.
            </p>

            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="guide-step-card">
                        <div class="guide-step-badge">1</div>
                        <div class="mb-3 text-warning" style="font-size: 32px;"><i class="bi bi-search"></i></div>
                        <h4>1. Maghanap ng Paksa</h4>
                        <p>I-type ang paksa o gamitin ang aming <strong>Mabilisang Hanap chips</strong> (halimbawa: <em>#TricycleFare, #BusinessPermit</em>) sa pampublikong rehistro.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="guide-step-card">
                        <div class="guide-step-badge">2</div>
                        <div class="mb-3 text-warning" style="font-size: 32px;"><i class="bi bi-robot"></i></div>
                        <h4>2. Magtanong sa ORLMS AI</h4>
                        <p>I-click ang <strong>AI Chatbot widget</strong> sa kanang ibaba upang magtanong sa Tagalog/Ingles tungkol sa anumang ordinansa o resolusyon.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="guide-step-card">
                        <div class="guide-step-badge">3</div>
                        <div class="mb-3 text-warning" style="font-size: 32px;"><i class="bi bi-file-earmark-arrow-down"></i></div>
                        <h4>3. Libreng I-download</h4>
                        <p>Basahin ang buod o i-download nang libre ang opisyal na <strong>PDF copy</strong> ng aprubadong batas para sa iyong kailangan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (is_internal_portal()): ?>
    <!-- 5. Premium Connected Workflow (The Timeline - Aligned & Beautified) -->
    <section class="py-5" style="background-color: #ffffff; border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
        <div class="container text-center">
            <h3 class="section-title">Legislative Workflow</h3>
            <p class="text-muted mx-auto mb-5" style="max-width: 600px; font-size:15px; line-height:1.6;">
                Ang proseso ng dokumento mula sa pagbalangkas hanggang sa pagiging opisyal na batas ng lungsod.
            </p>
            
            <div class="row g-4 timeline-wrapper justify-content-center">
                <!-- Step 1 -->
                <div class="col-sm-6 col-md-4 col-lg-2 timeline-step">
                    <div class="timeline-number">1</div>
                    <h4>Drafting</h4>
                    <p>Pag-input ng panukalang batas ng Legislative Staff.</p>
                </div>
                <!-- Step 2 -->
                <div class="col-sm-6 col-md-4 col-lg-2 timeline-step">
                    <div class="timeline-number">2</div>
                    <h4>AI Audit</h4>
                    <p>Pagsusuri ng AI para sa similarity checks at duplicate checks.</p>
                </div>
                <!-- Step 3 -->
                <div class="col-sm-6 col-md-4 col-lg-2 timeline-step">
                    <div class="timeline-number">3</div>
                    <h4>Committee</h4>
                    <p>Pagrepaso ng kaukulang Komite at pag-isyu ng ulat.</p>
                </div>
                <!-- Step 4 -->
                <div class="col-sm-6 col-md-4 col-lg-2 timeline-step">
                    <div class="timeline-number">4</div>
                    <h4>Enactment</h4>
                    <p>Pagpasa ng Sangguniang Panlungsod at lagda ng Mayor.</p>
                </div>
                <!-- Step 5 -->
                <div class="col-sm-6 col-md-4 col-lg-2 timeline-step">
                    <div class="timeline-number">5</div>
                    <h4>Publication</h4>
                    <p>Pag-upload sa Public Registry para sa mga mamamayan.</p>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 5.1 Citizen Frequently Asked Questions (FAQ) Section -->
    <section class="py-5 my-4">
        <div class="container" style="max-width: 860px;">
            <div class="text-center mb-5">
                <h3 class="section-title">Mga Madalas Itanong ng Mamamayan (FAQ)</h3>
                <p class="text-muted" style="font-size:15px;">
                    Mga kasagutan sa karaniwang katanungan ng mga mamamayan tungkol sa mga opisyal na batas sa San Jose del Monte.
                </p>
            </div>

            <div class="accordion faq-accordion" id="citizenFaqAccordion">
                <!-- FAQ 1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeadingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseOne" aria-expanded="true" aria-controls="faqCollapseOne">
                            <i class="bi bi-question-circle text-warning me-2"></i> Paano ako makakakuha ng opisyal na kopya ng isang ordinansa o resolusyon?
                        </button>
                    </h2>
                    <div id="faqCollapseOne" class="accordion-collapse collapse show" aria-labelledby="faqHeadingOne" data-bs-parent="#citizenFaqAccordion">
                        <div class="accordion-body">
                            Maaari mong i-search ang pamagat o paksa ng ordinansa sa aming <strong>Public Registry</strong> at i-download agad ang libreng PDF copy. Kung kailangan mo naman ng <em>Certified True Copy</em> na may opisyal na dry seal, maaari kang bumisita sa Sangguniang Panlungsod Office sa CSJDM City Hall.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeadingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseTwo" aria-expanded="false" aria-controls="faqCollapseTwo">
                            <i class="bi bi-question-circle text-warning me-2"></i> Libre ba ang paggamit at pag-download sa ORLMS Portal?
                        </button>
                    </h2>
                    <div id="faqCollapseTwo" class="accordion-collapse collapse" aria-labelledby="faqHeadingTwo" data-bs-parent="#citizenFaqAccordion">
                        <div class="accordion-body">
                            <strong>Opo, 100% libre po.</strong> Ang ORLMS ay inisyatiba ng Pamahalaang Lungsod ng San Jose del Monte upang tiyakin ang transparency at mabilis na access ng bawat San Joseño sa ating mga lokal na batas.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeadingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseThree" aria-expanded="false" aria-controls="faqCollapseThree">
                            <i class="bi bi-question-circle text-warning me-2"></i> Ano ang ginagawa ng ORLMS AI Chatbot sa gilid ng screen?
                        </button>
                    </h2>
                    <div id="faqCollapseThree" class="accordion-collapse collapse" aria-labelledby="faqHeadingThree" data-bs-parent="#citizenFaqAccordion">
                        <div class="accordion-body">
                            Ang ORLMS AI ay ang ating opisyal na <strong>Smart Legislative Assistant</strong>. Maaari mo itong tanungin tungkol sa mga ordinansa (halimbawa: <em>"Magkano ang pamasahe sa tricycle?"</em> o <em>"Ano ang mga kailangan sa business permit?"</em>) at magbibigay ito ng mabilisang paliwanag sa Tagalog o Ingles.
                        </div>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeadingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseFour" aria-expanded="false" aria-controls="faqCollapseFour">
                            <i class="bi bi-question-circle text-warning me-2"></i> Paano mag-endorso o maghain ng panukala sa Sangguniang Panlungsod?
                        </button>
                    </h2>
                    <div id="faqCollapseFour" class="accordion-collapse collapse" aria-labelledby="faqHeadingFour" data-bs-parent="#citizenFaqAccordion">
                        <div class="accordion-body">
                            Maaaring magsumite ng pormal na liham o panukalang resolusyon sa Tanggapan ng Sangguniang Panlungsod sa City Hall. Isasailalim ito sa review ng kaukulang Komite para sa kauna-unahang pagbasa (First Reading) sa regular session.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Official Government Footer -->
    <footer class="gov-footer">
        <div class="container">
            <div class="row g-4">
                <!-- Column 1: LGU Info -->
                <div class="col-lg-4 text-start">
                    <h5>LGU of San Jose del Monte</h5>
                    <p class="mb-2">Sangguniang Panlungsod Legislative Department</p>
                    <p class="text-muted" style="line-height:1.65;">
                        Ang Legislative Ordinance and Resolution Lifecycle Management System (ORLMS) ay isang inisyatiba upang mapataas ang antas ng serbisyo, bilis, at katapatan sa pamamagitan ng teknolohiya at Artipisyal na Katalinuhan (AI).
                    </p>
                </div>
                <!-- Column 2: Quick Links -->
                <div class="col-lg-4 text-start">
                    <h5>Mga Links</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="<?= APP_URL ?>/portal"><i class="bi bi-chevron-right me-1"></i> Public Registry Search</a></li>
                        <?php if (is_internal_portal()): ?>
                        <li><a href="<?= APP_URL ?>/auth/login"><i class="bi bi-chevron-right me-1"></i> Staff Portal Login</a></li>
                        <?php endif; ?>
                        <li><a href="https://csjdm.gov.ph/" target="_blank"><i class="bi bi-chevron-right me-1"></i> Official CSJDM Website</a></li>
                        <li><a href="https://bulacan.gov.ph/" target="_blank"><i class="bi bi-chevron-right me-1"></i> Bulacan Province Portal</a></li>
                    </ul>
                </div>
                <!-- Column 3: Contact & Address -->
                <div class="col-lg-4 text-start">
                    <h5>Makipag-ugnayan</h5>
                    <p class="mb-2"><i class="bi bi-geo-alt me-2 text-warning"></i> CSJDM City Hall, Brgy. Dulong Bayan, City of San Jose del Monte, Bulacan, Philippines</p>
                    <p class="mb-2"><i class="bi bi-envelope me-2 text-warning"></i> sp_legislative@csjdm.gov.ph</p>
                    <p><i class="bi bi-telephone me-2 text-warning"></i> (044) 815-2831 / Local 103</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div>
                    © 2026 Ordinance and Resolution Lifecycle Management System (ORLMS) CSJDM Bulacan. All rights reserved.
                </div>
                <div class="mt-2 text-muted" style="font-size: 10px;">
                    Republika ng Pilipinas • Lungsod ng San Jose del Monte • Arya San Joseño!
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating AI Chatbot Widget -->
    <div class="chatbot-wrapper no-print" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999; font-family: 'Inter', sans-serif;">
        <!-- Chat Toggle Button -->
        <button id="chatbot-toggle-btn" style="width: 60px; height: 60px; border-radius: 50%; background-color: #0c2340; border: 3px solid #f2a900; color: #f2a900; font-size: 26px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 10px 25px rgba(12, 35, 64, 0.35); transition: all 0.25s cubic-bezier(0.25, 0.8, 0.25, 1); outline: none;">
            <i class="bi bi-chat-dots-fill"></i>
        </button>

        <!-- Chat Box -->
        <div id="chatbot-box" style="display: none; width: 370px; height: 490px; max-height: 80vh; background: #ffffff; border: 1px solid #dee2e6; border-top: 4px solid #0c2340; border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.22); position: absolute; bottom: 80px; right: 0; flex-direction: column; overflow: hidden; animation: slideUp 0.25s ease;">
            <!-- Header -->
            <div style="background-color: #0c2340; color: #ffffff; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #f2a900;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #f2a900; color: #0c2340; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 800;">
                        O
                    </div>
                    <div style="text-align: left;">
                        <div style="font-size: 13.5px; font-weight: 700; line-height: 1;">ORLMS AI</div>
                        <span style="font-size: 10px; color: #f2a900; font-weight: 600;">CSJDM Legislative AI Assistant</span>
                    </div>
                </div>
                <button id="chatbot-close-btn" style="background: none; border: none; color: #ffffff; font-size: 20px; cursor: pointer; padding: 0; line-height: 1;">
                    <i class="bi bi-x"></i>
                </button>
            </div>

            <!-- Messages Area -->
            <div id="chatbot-messages" style="flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 12px; background-color: #f8f9fa;">
                <!-- Welcome Message -->
                <div style="display: flex; flex-direction: column; align-items: flex-start; max-width: 85%; align-self: flex-start; text-align: left;">
                    <span style="font-size: 9.5px; font-weight: 700; color: #0c2340; margin-bottom: 2px; text-transform: uppercase;">ORLMS AI</span>
                    <div style="background-color: #ffffff; border: 1px solid #dee2e6; color: #212529; border-radius: 4px 12px 12px 12px; padding: 10px 14px; font-size: 12.5px; line-height: 1.6; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        Magandang araw po! Ako si <strong>ORLMS AI</strong>, ang opisyal na Legislative AI Assistant ng San Jose del Monte. Mayroon po ba kayong katanungan tungkol sa ating mga ordinansa, resolusyon, o lokal na regulasyon?
                    </div>
                </div>
            </div>

            <!-- Input Form Footer -->
            <form id="chatbot-form" style="border-top: 1px solid #dee2e6; padding: 12px; display: flex; gap: 8px; background-color: #ffffff; margin: 0;">
                <input type="text" id="chatbot-input" placeholder="Magtanong po dito..." style="flex: 1; border: 1px solid #ced4da; border-radius: 20px; padding: 8px 16px; font-size: 13px; outline: none; transition: border-color 0.2s;" autocomplete="off" required>
                <button type="submit" style="width: 36px; height: 36px; border-radius: 50%; background-color: #0c2340; border: none; color: #f2a900; font-size: 16px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background-color 0.2s;">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>
    </div>

    <style>
        @keyframes slideUp {
            from { transform: translateY(15px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>

    <!-- Chatbot Javascript Logic -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleBtn = document.getElementById("chatbot-toggle-btn");
        const closeBtn = document.getElementById("chatbot-close-btn");
        const chatBox = document.getElementById("chatbot-box");
        const chatForm = document.getElementById("chatbot-form");
        const chatInput = document.getElementById("chatbot-input");
        const messagesContainer = document.getElementById("chatbot-messages");

        // Toggle open/close chat widget
        toggleBtn.addEventListener("click", function() {
            if (chatBox.style.display === "none" || chatBox.style.display === "") {
                chatBox.style.display = "flex";
                chatInput.focus();
                toggleBtn.style.transform = "scale(0.9)";
            } else {
                chatBox.style.display = "none";
                toggleBtn.style.transform = "scale(1)";
            }
        });

        closeBtn.addEventListener("click", function() {
            chatBox.style.display = "none";
            toggleBtn.style.transform = "scale(1)";
        });

    // Markdown parser for AI responses
    function parseMarkdown(str) {
        if (!str) return "";
        let safe = str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");

        // Bold **text**
        safe = safe.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        // Bullets * text
        safe = safe.replace(/^\*\s+(.*)$/gim, '<div style="margin-left:8px; margin-bottom:2px;">• $1</div>');
        // Newlines
        safe = safe.replace(/\n/g, '<br>');

        return safe;
    }

    // Helper to append message bubble to box
    function appendMessage(sender, text, isAi = false) {
        const wrapper = document.createElement("div");
        wrapper.style.display = "flex";
        wrapper.style.flexDirection = "column";
        wrapper.style.alignItems = isAi ? "flex-start" : "flex-end";
        wrapper.style.maxWidth = "85%";
        if (isAi) {
            wrapper.style.alignSelf = "flex-start";
            wrapper.style.textAlign = "left";
        } else {
            wrapper.style.alignSelf = "flex-end";
            wrapper.style.textAlign = "right";
        }

        const label = document.createElement("span");
        label.style.fontSize = "9.5px";
        label.style.fontWeight = "700";
        label.style.color = isAi ? "#0c2340" : "#6c757d";
        label.style.marginBottom = "2px";
        label.style.textTransform = "uppercase";
        label.textContent = sender;

        const bubble = document.createElement("div");
        bubble.style.fontSize = "12.5px";
        bubble.style.lineHeight = "1.6";
        bubble.style.padding = "10px 14px";
        bubble.style.boxShadow = "0 2px 4px rgba(0,0,0,0.02)";
        
        if (isAi) {
            bubble.style.backgroundColor = "#ffffff";
            bubble.style.border = "1px solid #dee2e6";
            bubble.style.color = "#212529";
            bubble.style.borderRadius = "4px 12px 12px 12px";
            bubble.innerHTML = parseMarkdown(text);
        } else {
            bubble.style.backgroundColor = "#0c2340";
            bubble.style.color = "#ffffff";
            bubble.style.borderRadius = "12px 12px 4px 12px";
            bubble.style.border = "none";
            bubble.textContent = text;
        }

        wrapper.appendChild(label);
        wrapper.appendChild(bubble);
        messagesContainer.appendChild(wrapper);

            // Auto scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Append a typing loader indicator
        let typingIndicator = null;
        function showTypingIndicator() {
            typingIndicator = document.createElement("div");
            typingIndicator.style.display = "flex";
            typingIndicator.style.flexDirection = "column";
            typingIndicator.style.alignItems = "flex-start";
            typingIndicator.style.maxWidth = "85%";
            typingIndicator.style.alignSelf = "flex-start";
            typingIndicator.style.textAlign = "left";

            const label = document.createElement("span");
            label.style.fontSize = "9.5px";
            label.style.fontWeight = "700";
            label.style.color = "#0c2340";
            label.style.marginBottom = "2px";
            label.style.textTransform = "uppercase";
            label.textContent = "ORLMS AI";

            const bubble = document.createElement("div");
            bubble.style.backgroundColor = "#ffffff";
            bubble.style.border = "1px solid #dee2e6";
            bubble.style.color = "#6c757d";
            bubble.style.borderRadius = "4px 12px 12px 12px";
            bubble.style.padding = "10px 14px";
            bubble.style.fontSize = "12.5px";
            bubble.style.fontStyle = "italic";
            bubble.textContent = "Sumusulat...";

            typingIndicator.appendChild(label);
            typingIndicator.appendChild(bubble);
            messagesContainer.appendChild(typingIndicator);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function removeTypingIndicator() {
            if (typingIndicator) {
                typingIndicator.remove();
                typingIndicator = null;
            }
        }

        // Handle form submit (Sending message)
        chatForm.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const messageText = chatInput.value.trim();
            if (!messageText) return;

            // 1. Add user message bubble
            appendMessage("Mamamayan", messageText, false);
            chatInput.value = "";
            chatInput.disabled = true;

            // 2. Show typing loading
            showTypingIndicator();

            // 3. Make AJAX request to /portal/chat
            const formData = new FormData();
            formData.append("message", messageText);

            fetch("<?= APP_URL ?>/portal/chat", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json().catch(() => null);
                if (response.status === 429) {
                    return { success: false, reply: data?.reply || "Masyado pong mabilis ang inyong pagtatanong. Pakiusap maghintay ng ilang segundo bago magtanong muli." };
                }
                return data || { success: false, reply: "Paumanhin po, nagkaroon ng error. Subukan muli." };
            })
            .then(data => {
                removeTypingIndicator();
                chatInput.disabled = false;
                chatInput.focus();
                if (data.success) {
                    appendMessage("ORLMS AI", data.reply, true);
                } else {
                    appendMessage("ORLMS AI", data.reply || "Paumanhin po, nagkaroon ng error. Subukan muli.", true);
                }
            })
            .catch(err => {
                removeTypingIndicator();
                chatInput.disabled = false;
                chatInput.focus();
                appendMessage("ORLMS AI", "Paumanhin po, hindi makakonekta sa server. Pakisiguro na active ang connection.", true);
                console.error("Chatbot Error: ", err);
            });
        });
    });
    </script>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ORLMS Global JavaScript & Client Protection Module -->
    <script src="<?= APP_URL ?>/public/js/main.js"></script>
</body>
</html>
