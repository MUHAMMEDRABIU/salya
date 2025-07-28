<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frozen Food - Fresh Frozen Foods Delivered</title>
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="index.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'accent': '#F97316',
                        'gray-custom': '#f6f7fc',
                        'dark-custom': '#201f20',
                        'secondary': '#ff7272',
                    },
                    fontFamily: {
                        'dm-sans': ['DM Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>

<body class="font-dm-sans">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white shadow-lg z-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="text-2xl font-bold text-accent">
                    <img src="assets/img/favicon.png" class="inline-block w-8 h-8 mr-2">
                    <span>Frozen Food</span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#home" class="text-dark-custom hover:text-accent transition-colors">Home</a>
                    <a href="pages/categories.php" class="text-dark-custom hover:text-accent transition-colors">Categories</a>
                    <a href="#delivery" class="text-dark-custom hover:text-accent transition-colors">Delivery</a>
                    <a href="#testimonials" class="text-dark-custom hover:text-accent transition-colors">Reviews</a>
                    <a href="#contact" class="text-dark-custom hover:text-accent transition-colors">Contact</a>
                    <a href="pages/about.php" class="text-dark-custom hover:text-accent transition-colors">About</a>

                    <a href="client/index.php" class="text-accent font-semibold hover:underline">Login</a>
                    <a href="client/register.php" class="text-accent font-semibold hover:underline">Register</a>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-dark-custom">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="flex flex-col space-y-2">
                    <a href="#home" class="text-dark-custom hover:text-accent transition-colors py-2">Home</a>
                    <a href="pages/categories.php" class="text-dark-custom hover:text-accent transition-colors py-2">Categories</a>
                    <a href="#delivery" class="text-dark-custom hover:text-accent transition-colors py-2">Delivery</a>
                    <a href="#testimonials" class="text-dark-custom hover:text-accent transition-colors py-2">Reviews</a>
                    <a href="#contact" class="text-dark-custom hover:text-accent transition-colors py-2">Contact</a>
                    <a href="pages/about.php" class="text-accent font-semibold py-2">About</a>
                    <a href="client/index.php" class="text-accent font-semibold py-2">Login</a>
                    <a href="client/register.php" class="text-accent font-semibold py-2">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative h-screen flex items-center justify-center bg-gradient-to-br from-accent to-secondary">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="absolute inset-0 bg-cover bg-center hero-bg"></div>
        <div class="relative z-10 text-center text-white px-4 max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in-up">
                Fresh Frozen Foods Delivered to Your Doorstep
            </h1>
            <p class="text-xl md:text-2xl mb-8 animate-fade-in-up-delay-1">
                From all regions of Nigeria, to your kitchen.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up-delay-2">
                <button onclick="document.getElementById('categories').scrollIntoView({ behavior: 'smooth' });"
                    class="bg-white text-accent px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 btn-custom">
                    Shop Now
                </button>
                <button class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-accent btn-custom">
                    Learn More
                </button>
            </div>
        </div>
    </section>

    <!-- Product Categories Section -->
    <section id="categories" class="py-20 bg-gray-custom">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-dark-custom mb-4">Shop by Category</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Discover our premium selection of frozen foods from across Nigeria
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Premium Meats -->
                <div class="category-card bg-white rounded-xl overflow-hidden shadow-lg">
                    <div class="relative h-48 overflow-hidden">
                        <img src="https://images.pexels.com/photos/2338407/pexels-photo-2338407.jpeg?auto=compress&cs=tinysrgb&w=400"
                            alt="Premium Meats" class="w-full h-full object-cover">
                        <div class="category-overlay absolute inset-0 bg-accent bg-opacity-80 flex items-center justify-center opacity-0 hover:opacity-100">
                            <i class="fas fa-eye text-white text-3xl"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-dark-custom mb-2">Premium Meats</h3>
                        <p class="text-gray-600 mb-4">Quality beef, goat meat, and lamb from northern Nigeria</p>
                        <a href="pages/categories.php" class="border-2 border-accent text-accent px-6 py-2 rounded-lg hover:bg-accent hover:text-white transition-all inline-block text-center">
                            Explore
                        </a>
                    </div>
                </div>

                <!-- Fresh Fish -->
                <div class="category-card bg-white rounded-xl overflow-hidden shadow-lg">
                    <div class="relative h-48 overflow-hidden">
                        <img src="https://images.pexels.com/photos/725991/pexels-photo-725991.jpeg?auto=compress&cs=tinysrgb&w=400"
                            alt="Fresh Fish" class="w-full h-full object-cover">
                        <div class="category-overlay absolute inset-0 bg-accent bg-opacity-80 flex items-center justify-center opacity-0 hover:opacity-100">
                            <i class="fas fa-eye text-white text-3xl"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-dark-custom mb-2">Fresh Fish</h3>
                        <p class="text-gray-600 mb-4">Ocean-fresh fish from coastal regions</p>
                        <a href="pages/categories.php" class="border-2 border-accent text-accent px-6 py-2 rounded-lg hover:bg-accent hover:text-white transition-all inline-block text-center">
                            Explore
                        </a>
                    </div>
                </div>

                <!-- Poultry -->
                <div class="category-card bg-white rounded-xl overflow-hidden shadow-lg">
                    <div class="relative h-48 overflow-hidden">
                        <img src="https://images.pexels.com/photos/33239/chickens-hahn-hen-animal.jpg?auto=compress&cs=tinysrgb&w=400"
                            alt="Poultry" class="w-full h-full object-cover">
                        <div class="category-overlay absolute inset-0 bg-accent bg-opacity-80 flex items-center justify-center opacity-0 hover:opacity-100">
                            <i class="fas fa-eye text-white text-3xl"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-dark-custom mb-2">Poultry</h3>
                        <p class="text-gray-600 mb-4">Farm-fresh chicken, turkey, and duck</p>
                        <a href="pages/categories.php" class="border-2 border-accent text-accent px-6 py-2 rounded-lg hover:bg-accent hover:text-white transition-all inline-block text-center">
                            Explore
                        </a>
                    </div>
                </div>

                <!-- Vegetables -->
                <div class="category-card bg-white rounded-xl overflow-hidden shadow-lg">
                    <div class="relative h-48 overflow-hidden">
                        <img src="https://images.pexels.com/photos/1327838/pexels-photo-1327838.jpeg?auto=compress&cs=tinysrgb&w=400"
                            alt="Vegetables" class="w-full h-full object-cover">
                        <div class="category-overlay absolute inset-0 bg-accent bg-opacity-80 flex items-center justify-center opacity-0 hover:opacity-100">
                            <i class="fas fa-eye text-white text-3xl"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-dark-custom mb-2">Vegetables</h3>
                        <p class="text-gray-600 mb-4">Fresh vegetables and local produce</p>
                        <a href="pages/categories.php" class="border-2 border-accent text-accent px-6 py-2 rounded-lg hover:bg-accent hover:text-white transition-all inline-block text-center">
                            Explore
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Regional Delivery Section -->
    <section id="delivery" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-dark-custom mb-6">Nationwide Delivery</h2>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        We deliver fresh frozen foods across Nigeria with our advanced cold-chain logistics system.
                        From Lagos to Abuja, Port Harcourt to Kano, we ensure your food arrives fresh and safe.
                    </p>
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-accent rounded-full flex items-center justify-center">
                                <i class="fas fa-truck text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-dark-custom">Cold Chain Delivery</h4>
                                <p class="text-gray-600">Temperature-controlled vehicles</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-accent rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-dark-custom">24-48 Hour Delivery</h4>
                                <p class="text-gray-600">Fast delivery across major cities</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-accent rounded-full flex items-center justify-center">
                                <i class="fas fa-shield-alt text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-dark-custom">Quality Guarantee</h4>
                                <p class="text-gray-600">Fresh or full refund promise</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-2xl font-semibold text-dark-custom mb-6 text-center">Delivery Zones</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gray-custom p-4 rounded-lg text-center font-medium text-dark-custom hover:bg-accent hover:text-white transition-all cursor-pointer">
                            Lagos
                        </div>
                        <div class="bg-gray-custom p-4 rounded-lg text-center font-medium text-dark-custom hover:bg-accent hover:text-white transition-all cursor-pointer">
                            Abuja
                        </div>
                        <div class="bg-gray-custom p-4 rounded-lg text-center font-medium text-dark-custom hover:bg-accent hover:text-white transition-all cursor-pointer">
                            Kano
                        </div>
                        <div class="bg-gray-custom p-4 rounded-lg text-center font-medium text-dark-custom hover:bg-accent hover:text-white transition-all cursor-pointer">
                            Port Harcourt
                        </div>
                        <div class="bg-gray-custom p-4 rounded-lg text-center font-medium text-dark-custom hover:bg-accent hover:text-white transition-all cursor-pointer">
                            Ibadan
                        </div>
                        <div class="bg-gray-custom p-4 rounded-lg text-center font-medium text-dark-custom hover:bg-accent hover:text-white transition-all cursor-pointer">
                            Kaduna
                        </div>
                        <div class="bg-gray-custom p-4 rounded-lg text-center font-medium text-dark-custom hover:bg-accent hover:text-white transition-all cursor-pointer">
                            kogi
                        </div>
                        <div class="bg-gray-custom p-4 rounded-lg text-center font-medium text-dark-custom hover:bg-accent hover:text-white transition-all cursor-pointer">
                            Jos
                        </div>
                        <div class="bg-gray-custom p-4 rounded-lg text-center font-medium text-dark-custom hover:bg-accent hover:text-white transition-all cursor-pointer">
                            Enugu
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Customer Testimonials Section -->
    <section id="testimonials" class="py-20 bg-gray-custom">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-dark-custom mb-4">What Our Customers Say</h2>
                <p class="text-xl text-gray-600">Join thousands of satisfied customers across Nigeria</p>
            </div>
            <div class="relative max-w-4xl mx-auto">
                <div class="overflow-hidden">
                    <div id="testimonial-track" class="testimonial-track flex">
                        <!-- Testimonial 1 -->
                        <div class="w-full flex-shrink-0 px-4">
                            <div class="bg-white rounded-xl p-8 shadow-lg">
                                <div class="flex text-yellow-400 mb-4">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p class="text-lg text-gray-600 mb-6 italic">
                                    "Amazing quality fish from Port Harcourt! Delivered fresh to my doorstep in Lagos. Will definitely order again."
                                </p>
                                <div class="flex items-center">
                                    <img src="https://images.pexels.com/photos/1239291/pexels-photo-1239291.jpeg?auto=compress&cs=tinysrgb&w=100"
                                        alt="Adaora" class="w-12 h-12 rounded-full object-cover mr-4">
                                    <div>
                                        <h4 class="font-semibold text-dark-custom">Adaora Okafor</h4>
                                        <span class="text-gray-600">Lagos</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 2 -->
                        <div class="w-full flex-shrink-0 px-4">
                            <div class="bg-white rounded-xl p-8 shadow-lg">
                                <div class="flex text-yellow-400 mb-4">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p class="text-lg text-gray-600 mb-6 italic">
                                    "The best meat delivery service in Nigeria. Premium quality and excellent customer service. Highly recommended!"
                                </p>
                                <div class="flex items-center">
                                    <img src="https://images.pexels.com/photos/2379004/pexels-photo-2379004.jpeg?auto=compress&cs=tinysrgb&w=100"
                                        alt="Ibrahim" class="w-12 h-12 rounded-full object-cover mr-4">
                                    <div>
                                        <h4 class="font-semibold text-dark-custom">Ibrahim Musa</h4>
                                        <span class="text-gray-600">Abuja</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 3 -->
                        <div class="w-full flex-shrink-0 px-4">
                            <div class="bg-white rounded-xl p-8 shadow-lg">
                                <div class="flex text-yellow-400 mb-4">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p class="text-lg text-gray-600 mb-6 italic">
                                    "Fast delivery and fresh products. FreshFreeze has made cooking so much easier for my family. Thank you!"
                                </p>
                                <div class="flex items-center">
                                    <img src="https://images.pexels.com/photos/3763188/pexels-photo-3763188.jpeg?auto=compress&cs=tinysrgb&w=100"
                                        alt="Chioma" class="w-12 h-12 rounded-full object-cover mr-4">
                                    <div>
                                        <h4 class="font-semibold text-dark-custom">Chioma Ekwegh</h4>
                                        <span class="text-gray-600">Port Harcourt</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center mt-8 space-x-2">
                    <button class="testimonial-dot w-3 h-3 rounded-full bg-accent" data-slide="0"></button>
                    <button class="testimonial-dot w-3 h-3 rounded-full bg-gray-400" data-slide="1"></button>
                    <button class="testimonial-dot w-3 h-3 rounded-full bg-gray-400" data-slide="2"></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Call-to-Action Section -->
    <section class="py-20 bg-gradient-to-r from-accent to-secondary text-white text-center">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-4xl font-bold mb-6">Ready to Start Shopping?</h2>
            <p class="text-xl mb-8">
                Join thousands of satisfied customers and get fresh frozen foods delivered to your doorstep today!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button class="bg-white text-accent px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 btn-custom">
                    Get Started
                </button>
                <button class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-accent btn-custom">
                    Browse Products
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-dark-custom text-white py-16">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-2xl font-bold text-accent mb-4">Frozen Food</h3>
                    <p class="text-gray-300 mb-4 leading-relaxed">
                        Nigeria's leading frozen food delivery platform, bringing fresh quality from farm to your table.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-accent rounded-full flex items-center justify-center hover:bg-secondary transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-accent rounded-full flex items-center justify-center hover:bg-secondary transition-colors">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-accent rounded-full flex items-center justify-center hover:bg-secondary transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-accent rounded-full flex items-center justify-center hover:bg-secondary transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-300 hover:text-accent transition-colors">Home</a></li>
                        <li><a href="#categories" class="text-gray-300 hover:text-accent transition-colors">Browse Products</a></li>
                        <li><a href="#delivery" class="text-gray-300 hover:text-accent transition-colors">Delivery Info</a></li>
                        <li><a href="#testimonials" class="text-gray-300 hover:text-accent transition-colors">Reviews</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Customer Service</h4>
                    <ul class="space-y-2">
                        <li><a href="pages/about.php"
                                class="<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'text-accent font-semibold' : 'text-gray-300 hover:text-accent transition-colors'; ?>">
                                About Us
                            </a>
                        </li>
                        <li><a href="client/index.php" class="text-gray-300 hover:text-accent transition-colors">Login</a></li>
                        <li><a href="client/register.php" class="text-gray-300 hover:text-accent transition-colors">Register</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-accent transition-colors">Contact Support</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-accent transition-colors">FAQ</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-accent transition-colors">Delivery Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Info</h4>
                    <div class="space-y-3">
                        <p class="flex items-center text-gray-300">
                            <i class="fas fa-phone text-accent mr-3"></i>
                            +234 800 FRESH (37374)
                        </p>
                        <p class="flex items-center text-gray-300">
                            <i class="fas fa-envelope text-accent mr-3"></i>
                            hello@freshfreeze.ng
                        </p>
                        <p class="flex items-center text-gray-300">
                            <i class="fas fa-map-marker-alt text-accent mr-3"></i>
                            Lagos, Nigeria
                        </p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 pt-8 text-center">
                <p class="text-gray-400">&copy; 2025 Frozen Food. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="index.js"></script>
</body>

</html>