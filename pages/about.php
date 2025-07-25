
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
    <main class="pt-24 max-w-4xl mx-auto px-4">
        <h1 class="text-4xl font-bold text-dark-custom mb-6">About Frozen Food</h1>
        <p class="text-lg text-gray-700 mb-4">
            <strong>Frozen Food</strong> is Nigeria’s leading frozen food delivery platform, dedicated to bringing fresh, quality products from farm to your table. 
            Our mission is to make healthy eating easy and accessible for every household. We believe that everyone deserves access to nutritious food, no matter where they live.
        </p>
        <p class="text-lg text-gray-700 mb-4">
            We offer a wide range of products including premium meats, fish, poultry, and vegetables, all sourced from trusted local farmers and fisheries. 
            Our advanced cold-chain logistics ensure that every item arrives at your doorstep fresh, safe, and ready to cook.
        </p>
        <h2 class="text-2xl font-semibold text-accent mb-4">Our Story</h2>
        <p class="text-gray-700 mb-4">
            Founded in 2025, Frozen Food started with a simple goal: to connect Nigerian families with the best local produce, delivered conveniently and reliably. 
            Over the years, we have grown into a nationwide service, partnering with hundreds of suppliers and serving thousands of happy customers.
        </p>
        <h2 class="text-2xl font-semibold text-accent mb-4">Why Choose Us?</h2>
        <ul class="list-disc pl-6 text-gray-700 mb-8">
            <li>Nationwide delivery</li>
            <li>Quality guarantee</li>
            <li>Fast and reliable service</li>
            <li>Customer-focused support</li>
            <li>Commitment to food safety and freshness</li>
        </ul>
        <h2 class="text-2xl font-semibold text-accent mb-4">Connect With Us</h2>
        <p class="text-gray-700 mb-8">
            We are passionate about serving you. If you have any questions, feedback, or partnership inquiries, please reach out via our contact page or follow us on social media.
        </p>
    </main>
</body>
</html>