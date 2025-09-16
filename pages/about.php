<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>About Us - Frozen Food</title>
    <link rel="stylesheet" href="../index.css">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
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

<body class="font-dm-sans bg-gray-custom">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white shadow-lg z-50">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold text-accent">
                <img src="../assets/img/favicon.png" class="inline-block w-8 h-8 mr-2">
                <span>Frozen Food</span>
            </div>
            <div class="hidden md:flex space-x-8">
                <a href="../index.php" class="text-dark-custom hover:text-accent">Home</a>
                <a href="about.php" class="text-accent font-semibold underline">About</a>
                <a href="../login.php" class="text-dark-custom hover:text-accent">Login</a>
                <a href="../register.php" class="text-dark-custom hover:text-accent">Register</a>
            </div>
        </div>
    </nav>
    <main class="pt-28 max-w-6xl mx-auto px-6">
        <!-- Hero Section -->
        <section class="text-center mb-16">
            <h1 class="text-5xl font-extrabold text-dark-custom mb-4">About <span class="text-accent">Frozen Food</span></h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Nigeria’s leading frozen food delivery platform, committed to making fresh and nutritious food easily accessible to every household.
                We bridge the gap between trusted farmers and families, ensuring quality from farm to table.
            </p>
        </section>

        <!-- Mission & Vision -->
        <!-- Mission & Vision -->
        <section class="grid md:grid-cols-2 gap-12 mb-20 text-center md:text-left">
            <div>
                <h2 class="text-3xl font-semibold text-dark-custom mb-4">Our Mission</h2>
                <p class="text-gray-700 leading-relaxed">
                    To transform food delivery in Nigeria by making quality frozen products affordable, reliable, and accessible to all,
                    while empowering local farmers and ensuring food safety through world-class logistics.
                </p>
            </div>
            <div>
                <h2 class="text-3xl font-semibold text-dark-custom mb-4">Our Vision</h2>
                <p class="text-gray-700 leading-relaxed">
                    To become Africa’s most trusted frozen food delivery company,
                    setting new standards for freshness, reliability, and customer satisfaction.
                </p>
            </div>
        </section>

        <!-- Why Choose Us -->
        <section class="mb-20">
            <h2 class="text-3xl font-semibold text-dark-custom text-center mb-10">Why Choose Us?</h2>
            <div class="grid md:grid-cols-4 gap-8">
                <div class="bg-white p-6 rounded-2xl shadow-md text-center">
                    <div class="text-accent text-4xl mb-3">🚚</div>
                    <h3 class="font-semibold text-lg mb-2">Nationwide Delivery</h3>
                    <p class="text-gray-600 text-sm">Fast and reliable delivery to every state in Nigeria.</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-md text-center">
                    <div class="text-accent text-4xl mb-3">✅</div>
                    <h3 class="font-semibold text-lg mb-2">Quality Guarantee</h3>
                    <p class="text-gray-600 text-sm">Every item undergoes strict quality and freshness checks.</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-md text-center">
                    <div class="text-accent text-4xl mb-3">⚡</div>
                    <h3 class="font-semibold text-lg mb-2">Fast & Efficient</h3>
                    <p class="text-gray-600 text-sm">We combine cold-chain logistics with cutting-edge delivery systems.</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-md text-center">
                    <div class="text-accent text-4xl mb-3">📞</div>
                    <h3 class="font-semibold text-lg mb-2">Customer Support</h3>
                    <p class="text-gray-600 text-sm">A friendly support team ready to assist anytime you need us.</p>
                </div>
            </div>
        </section>
        <!-- Impact Section -->
        <section class="mb-20 text-center">
            <h2 class="text-3xl font-semibold text-dark-custom mb-6">Our Growth & Impact</h2>
            <p class="text-gray-700 max-w-3xl mx-auto mb-10">
                Since our founding in 2025, we have expanded into a nationwide service,
                delivering fresh products to thousands of households, supporting hundreds of local farmers,
                and building a community committed to healthier lifestyles.
            </p>
        </section>

        <!-- Call to Action -->
        <section class="text-center bg-accent text-white py-12 rounded-2xl shadow-lg">
            <h2 class="text-3xl font-bold mb-4">Let’s Connect</h2>
            <p class="max-w-2xl mx-auto mb-6 text-lg">
                Have questions, feedback, or want to partner with us?
                We’re passionate about serving you better.
            </p>
            <a href="/#" class="bg-white text-accent px-6 py-3 rounded-xl font-semibold shadow hover:bg-gray-100 transition">
                Contact Us
                
        </a>
        </section>
    </main>


</body>

</html>