<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../components/header.php';
?>

<body>
    <main class="dashboard">
        <div id="root">
            <div class="min-h-screen bg-gray-50">
                <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 flex items-center justify-center">
                    <div class="w-full max-w-md">
                        <div class="text-center mb-6">
                            <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-orange-400 to-orange-600 rounded-3xl shadow-lg flex items-center justify-center">
                                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center">
                                    <div class="w-6 h-6 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg"></div>
                                </div>
                            </div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h1>
                            <p class="text-gray-500 text-lg">Sign in to continue.</p>
                        </div>
                        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100">
                            <form class="space-y-6">
                                <div class="space-y-2"><label class="text-sm font-medium text-gray-700 block">Email Address</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail h-5 w-5 text-gray-400">
                                                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                            </svg></div><input type="email" class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all duration-200" placeholder="Enter your email" value="">
                                    </div>
                                </div>
                                <div class="space-y-2"><label class="text-sm font-medium text-gray-700 block">Password</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock h-5 w-5 text-gray-400">
                                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                            </svg></div><input type="password" class="w-full pl-12 pr-12 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all duration-200" placeholder="Enter your password" value=""><button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg></button>
                                    </div>
                                </div>
                                <div class="text-right"><button type="button" class="text-orange-500 hover:text-orange-600 text-sm font-medium transition-colors duration-200">Forgot Password?</button></div><button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-4 rounded-2xl font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center space-x-2"><span>Sign In</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right w-5 h-5">
                                        <path d="M5 12h14"></path>
                                        <path d="m12 5 7 7-7 7"></path>
                                    </svg></button>
                            </form>
                            <div class="my-8 flex items-center">
                                <div class="flex-1 border-t border-gray-200"></div><span class="px-4 text-sm text-gray-500 bg-white">or continue with</span>
                                <div class="flex-1 border-t border-gray-200"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4"><button class="flex items-center justify-center space-x-2 py-3 px-4 bg-gray-50 hover:bg-gray-100 rounded-2xl transition-colors duration-200 border border-gray-200"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chrome w-5 h-5 text-gray-600">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <circle cx="12" cy="12" r="4"></circle>
                                        <line x1="21.17" x2="12" y1="8" y2="8"></line>
                                        <line x1="3.95" x2="8.54" y1="6.06" y2="14"></line>
                                        <line x1="10.88" x2="15.46" y1="21.94" y2="14"></line>
                                    </svg><span class="text-sm font-medium text-gray-700">Google</span></button><button class="flex items-center justify-center space-x-2 py-3 px-4 bg-gray-50 hover:bg-gray-100 rounded-2xl transition-colors duration-200 border border-gray-200"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-apple w-5 h-5 text-gray-600">
                                        <path d="M12 20.94c1.5 0 2.75 1.06 4 1.06 3 0 6-8 6-12.22A4.91 4.91 0 0 0 17 5c-2.22 0-4 1.44-5 2-1-.56-2.78-2-5-2a4.9 4.9 0 0 0-5 4.78C2 14 5 22 8 22c1.25 0 2.5-1.06 4-1.06Z"></path>
                                        <path d="M10 2c1 .5 2 2 2 5"></path>
                                    </svg><span class="text-sm font-medium text-gray-700">Apple</span></button></div>
                        </div>
                        <div class="text-center mt-8">
                            <p class="text-gray-600">Don't have an account? <a href="register.php" class="text-orange-500 hover:text-orange-600 font-semibold transition-colors duration-200">Create Account</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overlay -->
        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 hidden backdrop-blur-sm">
            <div class="text-white text-lg font-bold p-8 rounded-lg max-w-lg mx-4">
                <div class="text-center">
                    <!-- Simple Orange Spinner -->
                    <div class="relative mx-auto mb-4 w-12 h-12">
                        <div class="absolute inset-0 border-4 border-orange-200 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-transparent border-t-orange-500 rounded-full animate-spin"></div>
                    </div>
                    <div class="text-white text-lg font-bold">Processing...</div>
                    <div class="text-white text-sm opacity-75 mt-2">Please wait, this may take a moment</div>
                </div>
            </div>
        </div>
        <!-- End Overlay -->
         
    </main>
    <script src="../assets/js/toast.js"></script>
    <script src="../assets/js/toggle-password.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.querySelector("form");

            form.addEventListener("submit", async (e) => {
                e.preventDefault(); // Prevent default form submission

                // Updated selectors to target inputs more reliably
                const emailInput = form.querySelector('input[type="email"]');
                const passwordInput = form.querySelector('input[type="password"]');

                // Check if elements exist before accessing their values
                if (!emailInput || !passwordInput) {
                    showToasted("Form elements not found. Please refresh the page.", 'error');
                    return;
                }

                const email = emailInput.value.trim();
                const password = passwordInput.value.trim();

                // Basic validation
                if (!email || !password) {
                    showToasted("Please fill in all fields.", 'error');
                    return;
                }

                const overlay = document.getElementById("overlay");
                overlay.classList.remove("hidden"); // Show overlay

                try {
                    const response = await fetch("api/api_login.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            email,
                            password
                        }),
                    });

                    // Check if response is JSON
                    const contentType = response.headers.get("content-type");
                    let data;
                    if (contentType && contentType.includes("application/json")) {
                        data = await response.json();
                    } else {
                        const text = await response.text();
                        throw new Error(text);
                    }

                    overlay.classList.add("hidden"); // Hide overlay after response

                    if (data.success) {
                        showToasted(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = "dashboard.php";
                        }, 2000);
                    } else {
                        showToasted(data.message, 'error');
                    }
                } catch (error) {
                    overlay.classList.add("hidden");
                    showToasted("An error occurred. Please try again.", 'error');
                    console.error("Error:", error);
                }
            });

            // Enhanced Eye Toggle with proper element checking
            const passwordInput = form.querySelector('input[type="password"]');
            const eyeToggleBtn = passwordInput ? passwordInput.nextElementSibling : null;

            if (passwordInput && eyeToggleBtn) {
                eyeToggleBtn.addEventListener("click", () => {
                    const isPassword = passwordInput.type === "password";
                    passwordInput.type = isPassword ? "text" : "password";
                    eyeToggleBtn.innerHTML = isPassword ?
                        `<svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-eye-off h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M17.94 17.94a10.94 10.94 0 0 1-5.94 1.94C5 19.88 2 12 2 12a21.05 21.05 0 0 1 4.29-6.29"/>
                <path d="M1 1l22 22"/>
            </svg>` :
                        `<svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-eye h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7S2 12 2 12Z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>`;
                });
            }
        });
    </script>
</body>

</html>