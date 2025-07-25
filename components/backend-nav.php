<nav class="fixed bottom-0 left-0 right-0 bg-gray-900 rounded-t-3xl px-6 py-4 shadow-2xl">
    <div class="flex justify-around items-center">
        <!-- Home Button -->
        <a href="dashboard.php" class="flex flex-col items-center space-y-1 p-2 rounded-xl transition-all duration-200 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-home w-6 h-6">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span class="text-xs font-medium">Home</span>
        </a>

        <!-- Cart Button -->
        <a href="cart.php" class="flex flex-col items-center space-y-1 p-2 rounded-xl transition-all duration-200 text-gray-400 hover:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart w-6 h-6">
                <circle cx="8" cy="21" r="1"></circle>
                <circle cx="19" cy="21" r="1"></circle>
                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            <span class="text-xs font-medium">Cart</span>
        </a>

        <!-- Profile Button -->
        <a href="profile.php" class="flex flex-col items-center space-y-1 p-2 rounded-xl transition-all duration-200 text-gray-400 hover:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user w-6 h-6">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span class="text-xs font-medium">Profile</span>
        </a>
    </div>
</nav>