{{-- resources/views/layouts/authdashbpard.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Your company</title>
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('build/assets/img/YourEUSKA 2.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('build/assets/img/YourEUSKA 2.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('build/assets/img/YourEUSKA 2.png') }}" />
    <link rel="shortcut icon" href="{{ asset('build/assets/img/YourEUSKA 2.png') }}" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            /* Body background - RED */
            background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Construction Pattern Background - BLACK lines */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                repeating-linear-gradient(45deg, rgba(0, 0, 0, 0.15) 0px, rgba(0, 0, 0, 0.15) 2px, transparent 2px, transparent 8px),
                repeating-linear-gradient(135deg, rgba(0, 0, 0, 0.15) 0px, rgba(0, 0, 0, 0.15) 2px, transparent 2px, transparent 8px);
            pointer-events: none;
        }

        /* Animated Grid Lines - BLACK lines */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(rgba(0, 0, 0, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 0, 0, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            animation: gridMove 20s linear infinite;
        }

        @keyframes gridMove {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(40px, 40px);
            }
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modern Card Design - WHITE background */
        .auth-card {
            background: white;
            border-radius: 32px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.3);
        }

        /* Floating Elements inside form */
        .floating-element {
            position: absolute;
            pointer-events: none;
            z-index: 0;
            opacity: 0.3;
        }

        .floating-1 {
            top: 10%;
            left: -10%;
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, rgba(255, 0, 0, 0.1) 0%, rgba(255, 0, 0, 0) 70%);
            border-radius: 50%;
            animation: floatAround 8s ease-in-out infinite;
        }

        .floating-2 {
            bottom: 15%;
            right: -15%;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(255, 0, 0, 0.08) 0%, rgba(255, 0, 0, 0) 70%);
            border-radius: 50%;
            animation: floatAround 12s ease-in-out infinite reverse;
        }

        .floating-3 {
            top: 40%;
            right: -5%;
            width: 80px;
            height: 80px;
            background: radial-gradient(circle, rgba(255, 0, 0, 0.1) 0%, rgba(255, 0, 0, 0) 70%);
            border-radius: 50%;
            animation: floatAround 6s ease-in-out infinite;
        }

        .floating-4 {
            bottom: 30%;
            left: -8%;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(255, 0, 0, 0.08) 0%, rgba(255, 0, 0, 0) 70%);
            border-radius: 50%;
            animation: floatAround 10s ease-in-out infinite reverse;
        }

        @keyframes floatAround {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(20px, -20px) scale(1.1);
            }

            66% {
                transform: translate(-15px, 15px) scale(0.9);
            }
        }

        /* Enhanced Floating particles - Coming from bottom and disappearing */
        .floating-particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: #ff0000;
            border-radius: 50%;
            opacity: 0;
            animation: particleFloat 3s ease-in-out infinite;
            pointer-events: none;
            box-shadow: 0 0 4px rgba(255, 0, 0, 0.5);
        }

        /* Different sizes for variety */
        .floating-particle.small {
            width: 3px;
            height: 3px;
            animation-duration: 2.5s;
        }

        .floating-particle.medium {
            width: 5px;
            height: 5px;
            animation-duration: 3s;
        }

        .floating-particle.large {
            width: 8px;
            height: 8px;
            animation-duration: 3.5s;
            box-shadow: 0 0 6px rgba(255, 0, 0, 0.6);
        }

        @keyframes particleFloat {
            0% {
                opacity: 0;
                transform: translateY(0) scale(0);
            }

            10% {
                opacity: 0.6;
                transform: translateY(-10px) scale(1);
            }

            20% {
                opacity: 0.8;
            }

            80% {
                opacity: 0.4;
                transform: translateY(-70px) scale(0.9);
            }

            100% {
                opacity: 0;
                transform: translateY(-100px) scale(0.5);
            }
        }

        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }

        /* Custom Company Logo */
        .company-logo {
            width: 150px;
            height: 150px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .company-logo img {
            max-width: 150%;
            max-height: 150%;
            object-fit: contain;
            transition: transform 0.3s ease;
            position: relative;
        }

        .company-logo img:hover {
            transform: scale(1.05);
        }

        /* Stylish Text with Floating Red Inside */
        .logo-section h1 {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(120deg,
                    #000000 0%,
                    #000000 30%,
                    #ff0000 45%,
                    #ff0000 55%,
                    #000000 70%,
                    #000000 100%);
            background-size: 250% auto;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: flowRed 5s linear infinite;
            display: inline-block;
        }

        @keyframes flowRed {
            0% {
                background-position: 0% 50%;
            }

            100% {
                background-position: 200% 50%;
            }
        }

        .logo-section p {
            color: #666;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        /* Form Styles */
        .auth-slot {
            margin-top: 0;
            position: relative;
            z-index: 1;
        }

        /* Input Group Styles with floating effect */
        .input-group-custom {
            margin-bottom: 1.5rem;
            position: relative;
            animation: slideInUp 0.5s ease-out;
            animation-fill-mode: both;
        }

        .input-group-custom:nth-child(1) {
            animation-delay: 0.1s;
        }

        .input-group-custom:nth-child(2) {
            animation-delay: 0.2s;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .input-icon {
            position: relative;
            display: flex;
            align-items: center;
            transition: transform 0.3s ease;
        }

        .input-icon:hover {
            transform: translateX(5px);
        }

        .input-icon i {
            position: absolute;
            left: 1rem;
            color: #ff0000;
            font-size: 1.1rem;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .input-icon input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            background: #f8f9fa;
            border: 1.5px solid #e0e0e0;
            border-radius: 14px;
            color: #333;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .input-icon input:focus {
            outline: none;
            border-color: #ff0000;
            background: white;
            box-shadow: 0 0 0 4px rgba(255, 0, 0, 0.1);
            transform: scale(1.02);
        }

        .input-icon input:focus+i {
            transform: scale(1.1);
            color: #cc0000;
        }

        .input-icon input::placeholder {
            color: #aaa;
            font-weight: 400;
        }

        /* Error Messages */
        .error-message {
            color: #ff0000;
            font-size: 0.75rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            animation: shake 0.3s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .error-message i {
            font-size: 0.7rem;
        }

        /* Login Button - BLACK with RED hover */
        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: #000000;
            border: none;
            border-radius: 14px;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.5s ease-out 0.3s both;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            background: #ff0000;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(255, 0, 0, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Footer Links */
        .auth-footer {
            margin-top: 2rem;
            text-align: center;
            color: #999;
            font-size: 0.8rem;
            animation: slideInUp 0.5s ease-out 0.4s both;
        }

        .auth-footer a {
            color: #ff0000;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-footer a:hover {
            color: #cc0000;
            text-decoration: underline;
        }

        /* Loading State */
        .btn-login.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-login.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 640px) {
            .auth-card {
                padding: 1.75rem;
            }

            .company-logo {
                width: 80px;
                height: 80px;
            }

            .logo-section h1 {
                font-size: 1.5rem;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #ff0000;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #cc0000;
        }
    </style>

    @livewireStyles
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-card">
                <!-- Floating Elements inside form -->
                <div class="floating-element floating-1"></div>
                <div class="floating-element floating-2"></div>
                <div class="floating-element floating-3"></div>
                <div class="floating-element floating-4"></div>

                <div class="logo-section">
                    <!-- Custom Company Logo -->
                    <div class="company-logo">
                        <img src="{{ asset('build/assets/img/YourEUSKA 2.png') }}" alt="Your company">
                    </div>
                    <h1>Your company</h1>
                    <p>Construction ERP Solution</p>
                </div>

                <div class="auth-slot">
                    {{ $slot }}
                </div>

                <div class="auth-footer">
                    <p>&copy; {{ date('Y') }} Your company. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Create enhanced floating particles dynamically - INCREASED COUNT
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.auth-card');
            if (card) {
                // Increased from 20 to 80 particles
                const particleCount = 80;
                const sizes = ['small', 'medium', 'large'];

                for (let i = 0; i < particleCount; i++) {
                    const particle = document.createElement('div');
                    // Random size class
                    const randomSize = sizes[Math.floor(Math.random() * sizes.length)];
                    particle.className = `floating-particle ${randomSize}`;

                    // Random horizontal position across the entire width
                    particle.style.left = Math.random() * 100 + '%';

                    // Random starting position from bottom
                    particle.style.bottom = Math.random() * 30 + 'px';

                    // Random animation delay for continuous flow
                    particle.style.animationDelay = Math.random() * 8 + 's';

                    // Random animation duration for variety
                    const duration = 2 + Math.random() * 3;
                    particle.style.animationDuration = duration + 's';

                    // Random opacity variation
                    particle.style.opacity = 0.3 + Math.random() * 0.5;

                    card.appendChild(particle);
                }

                // Continuous particle generation
                setInterval(function() {
                    if (card.children.length < 120) {
                        const particle = document.createElement('div');
                        const sizes = ['small', 'medium', 'large'];
                        const randomSize = sizes[Math.floor(Math.random() * sizes.length)];
                        particle.className = `floating-particle ${randomSize}`;
                        particle.style.left = Math.random() * 100 + '%';
                        particle.style.bottom = '-10px';
                        particle.style.animationDelay = '0s';
                        particle.style.animationDuration = 2 + Math.random() * 3 + 's';
                        card.appendChild(particle);

                        // Remove old particles after animation
                        setTimeout(() => {
                            if (particle && particle.remove) {
                                particle.remove();
                            }
                        }, 5000);
                    }
                }, 200);
            }
        });
    </script>

    @livewireScripts
</body>

</html>
