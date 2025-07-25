<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Categories - Frozen Food</title>
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
                <a href="categories.php" class="text-accent font-semibold underline">Categories</a>
                <a href="about.php" class="text-dark-custom hover:text-accent">About</a>
                <a href="../client/index.php" class="text-dark-custom hover:text-accent">Login</a>
                <a href="../client/register.php" class="text-dark-custom hover:text-accent">Register</a>
            </div>
        </div>
    </nav>
    <main class="pt-24 max-w-4xl mx-auto px-4">
        <h1 class="text-4xl font-bold text-dark-custom mb-8">Browse Categories</h1>
        <div class="space-y-8">
            <!-- Chicken Category -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-2xl font-semibold text-accent mb-4">Chicken</h2>
                <ul class="list-disc pl-6 text-gray-700 space-y-2">
                    <li>WHOLE CHICKEN (all sizes)</li>
                    <li>PORTIONS</li>
                    <li>CUT4</li>
                    <li>CHICKEN LAPS</li>
                    <li>CHICKEN WINGS</li>
                    <li>CHICKEN BREAST</li>
                    <li>OROBO HARD</li>
                </ul>
            </div>
            <!-- Turkey Category -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-2xl font-semibold text-accent mb-4">Turkey</h2>
                <ul class="list-disc pl-6 text-gray-700 space-y-2">
                    <li>TURKEY PORTIONS</li>
                    <li>TURKEY 305</li>
                    <li>TURKEY 306</li>
                    <li>TURKEY WINGS</li>
                    <li>TURKEY BLANKETR</li>
                    <li>TURKEY GIZZARD</li>
                </ul>
            </div>
            <!-- Fish Category -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-2xl font-semibold text-accent mb-4">Fish</h2>
                <ul class="list-disc pl-6 text-gray-700 space-y-2">
                    <li>FISH PORTIONS</li>
                    <li>SIMON FISH</li>
                    <li>TITUS FISH (SQUARE) PACIFIC</li>
                    <li>TITUS FISH (KAMPALA)</li>
                    <li>HORSE MACKEREL (KOTE)</li>
                    <li>SHAWA FISH</li>
                    <li>CROAKER FISH</li>
                    <li>HAKE STOCK FISH (WEWE)</li>
                    <li>HAKE STOCK FISH (KAMPALA)</li>
                    <li>GIWARUWA FISH</li>
                    <li>TILAPIA FISH</li>
                </ul>
            </div>
            <!-- Seafood Category -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-2xl font-semibold text-accent mb-4">Seafood</h2>
                <ul class="list-disc pl-6 text-gray-700 space-y-2">
                    <li>SHRIMPS</li>
                    <li>CRABS</li>
                    <li>PRAWNS</li>
                    <li>CALAMARIS</li>
                    <li>SAUSAGE</li>
                </ul>
            </div>
        </div>
    </main>
</body>

</html>