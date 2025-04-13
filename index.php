<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deklink - Pasidaryk savo iPhone dėklą</title>
    
    <!-- Stilių bibliotekos -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    
    <!-- Custom stiliai -->
    <style>
        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Custom animacijos */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        /* Hover efektai */
        .hover-scale {
            transition: transform 0.3s ease;
        }
        
        .hover-scale:hover {
            transform: scale(1.05);
        }
        
        /* Custom gradient */
        .gradient-bg {
            background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #f06292;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #ec407a;
        }
    </style>
    
    <?php 
    $configPath = dirname(__FILE__) . '/config/stripe-config.php';
    if (file_exists($configPath)) {
        include $configPath;
    } else {
        error_log('Stripe config file not found at ' . $configPath);
    }
    ?>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Hero sekcija -->
    <div class="gradient-bg min-h-screen flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="container mx-auto px-6 py-16 relative z-10" data-aos="fade-up">
            <h1 class="text-5xl md:text-6xl font-bold text-center text-gray-800 mb-8">
                Pasidaryk savo iPhone dėklą
            </h1>
            <p class="text-xl text-center text-gray-600 mb-12">
                Unikalūs dizainai, kokybiški medžiagai, greitas pristatymas
            </p>
        </div>
    </div>

    <!-- Užsakymo forma -->
    <div class="max-w-2xl mx-auto p-6 py-16" data-aos="fade-up" data-aos-delay="200">
        <div class="bg-white rounded-2xl shadow-xl p-8 hover-scale">
            <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">Užsakymo forma</h2>
            
            <form id="orderForm" class="space-y-6">
                <div data-aos="fade-right" data-aos-delay="300">
                    <label class="block text-sm font-medium text-gray-700">Vardas Pavardė</label>
                    <input type="text" id="customerName" name="customerName" required 
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 transition duration-300">
                </div>

                <div data-aos="fade-left" data-aos-delay="400">
                    <label class="block text-sm font-medium text-gray-700">Adresas</label>
                    <input type="text" id="customerAddress" name="customerAddress" required 
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 transition duration-300">
                </div>

                <div data-aos="fade-right" data-aos-delay="500">
                    <label class="block text-sm font-medium text-gray-700">Telefono numeris</label>
                    <input type="tel" id="customerPhone" name="customerPhone" required 
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 transition duration-300">
                </div>

                <div data-aos="fade-up" data-aos-delay="600">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pristatymo būdas</label>
                    <div class="grid grid-cols-3 gap-4">
                        <label class="delivery-option p-4 rounded-lg border hover:border-pink-500 cursor-pointer transition duration-300 hover:shadow-lg">
                            <input type="radio" name="deliveryMethod" value="Omniva" class="hidden" required>
                            <i class="fas fa-box text-2xl mb-2 text-pink-500"></i>
                            <span class="text-sm font-medium">Omniva</span>
                        </label>
                        <label class="delivery-option p-4 rounded-lg border hover:border-pink-500 cursor-pointer transition duration-300 hover:shadow-lg">
                            <input type="radio" name="deliveryMethod" value="LT Express" class="hidden" required>
                            <i class="fas fa-truck text-2xl mb-2 text-pink-500"></i>
                            <span class="text-sm font-medium">LT Express</span>
                        </label>
                        <label class="delivery-option p-4 rounded-lg border hover:border-pink-500 cursor-pointer transition duration-300 hover:shadow-lg">
                            <input type="radio" name="deliveryMethod" value="DPD" class="hidden" required>
                            <i class="fas fa-box-open text-2xl mb-2 text-pink-500"></i>
                            <span class="text-sm font-medium">DPD</span>
                        </label>
                    </div>
                </div>

                <div data-aos="fade-up" data-aos-delay="700">
                    <label class="block text-sm font-medium text-gray-700">Telefono modelis</label>
                    <select id="phoneModel" name="phoneModel" required 
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 transition duration-300">
                        <option value="">Pasirinkite modelį</option>
                        <option value="iPhone 6s">iPhone 6s</option>
                        <option value="iPhone 7">iPhone 7</option>
                        <option value="iPhone 8">iPhone 8</option>
                        <option value="iPhone X">iPhone X</option>
                        <option value="iPhone 11">iPhone 11</option>
                        <option value="iPhone 12">iPhone 12</option>
                        <option value="iPhone 13">iPhone 13</option>
                        <option value="iPhone 14">iPhone 14</option>
                        <option value="iPhone 15">iPhone 15</option>
                        <option value="iPhone 15 Pro">iPhone 15 Pro</option>
                        <option value="iPhone 15 Pro Max">iPhone 15 Pro Max</option>
                    </select>
                </div>

                <div class="mt-8" data-aos="fade-up" data-aos-delay="800">
                    <button type="submit" id="orderButton" 
                        class="w-full bg-gradient-to-r from-pink-500 to-pink-600 text-white py-4 px-6 rounded-lg hover:from-pink-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition duration-300 transform hover:scale-105">
                        <span class="flex items-center justify-center">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Užsakyti
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12 mt-16">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div data-aos="fade-up">
                    <h3 class="text-xl font-bold mb-4">Kontaktai</h3>
                    <p class="text-gray-300">info@deklink.lt</p>
                    <p class="text-gray-300">+370 6XX XXXXX</p>
                </div>
                <div data-aos="fade-up" data-aos-delay="200">
                    <h3 class="text-xl font-bold mb-4">Pristatymas</h3>
                    <p class="text-gray-300">Omniva</p>
                    <p class="text-gray-300">LT Express</p>
                    <p class="text-gray-300">DPD</p>
                </div>
                <div data-aos="fade-up" data-aos-delay="400">
                    <h3 class="text-xl font-bold mb-4">Sekite mus</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-pink-500 transition duration-300">
                            <i class="fab fa-facebook text-2xl"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-pink-500 transition duration-300">
                            <i class="fab fa-instagram text-2xl"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Form submission handling
        const form = document.getElementById('orderForm');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Siunčiama...';

                try {
                    const formData = {
                        customerName: form.querySelector('[name="customerName"]').value,
                        customerAddress: form.querySelector('[name="customerAddress"]').value,
                        customerPhone: form.querySelector('[name="customerPhone"]').value,
                        phoneModel: form.querySelector('[name="phoneModel"]').value,
                        deliveryMethod: form.querySelector('[name="deliveryMethod"]').value
                    };

                    const response = await fetch('create-checkout-session.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();

                    if (data.error) {
                        throw new Error(data.error);
                    }

                    const stripe = Stripe('<?php echo $env['STRIPE_PUBLIC_KEY']; ?>');
                    const result = await stripe.redirectToCheckout({
                        sessionId: data.id
                    });

                    if (result.error) {
                        throw new Error(result.error.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Įvyko klaida: ' + error.message);
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
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
    });
    </script>
</body>
</html> 