<?php $current_page = basename($_SERVER['PHP_SELF'], '.php'); ?>
<!-- Premium Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 z-50 px-4 pb-5">
    <div class="nav-container rounded-3xl py-3 px-2 flex justify-around items-center max-w-md mx-auto">

        <!-- Home -->
        <a href="dashboard.php" class="bottom-nav-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?> relative flex flex-col items-center py-2 px-4 rounded-2xl min-w-[60px]" data-target="home">
            <div class="bottom-nav-icon mb-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="nav-label text-xs font-medium whitespace-nowrap">Home</span>
            <div class="bottom-nav-indicator absolute -bottom-2 left-1/2 w-1.5 h-1.5 bg-orange-500 rounded-full"></div>
        </a>

        <!-- Orders -->
        <a href="orders.php" class="bottom-nav-item <?php echo $current_page === 'orders' ? 'active' : ''; ?> relative flex flex-col items-center py-2 px-4 rounded-2xl min-w-[60px]" data-target="orders">
            <div class="bottom-nav-icon mb-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <span class="nav-label text-xs font-medium whitespace-nowrap">Orders</span>
            <div class="bottom-nav-indicator absolute -bottom-2 left-1/2 w-1.5 h-1.5 bg-orange-500 rounded-full"></div>
        </a>

        <!-- Notifications -->
        <a href="notifications.php" class="bottom-nav-item <?php echo $current_page === 'notifications' ? 'active' : ''; ?> relative flex flex-col items-center py-2 px-4 rounded-2xl min-w-[60px]" data-target="notifications">
            <div class="bottom-nav-icon mb-1 relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <div class="bottom-notification-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs font-semibold rounded-full w-4 h-4 flex items-center justify-center">3</div>
            </div>
            <span class="nav-label text-xs font-medium whitespace-nowrap">Notifications</span>
            <div class="bottom-nav-indicator absolute -bottom-2 left-1/2 w-1.5 h-1.5 bg-orange-500 rounded-full"></div>
        </a>
        <!-- Profile -->
        <a href="profile.php" class="bottom-nav-item <?php echo $current_page === 'profile' ? 'active' : ''; ?> relative flex flex-col items-center py-2 px-4 rounded-2xl min-w-[60px]" data-target="profile">
            <div class="bottom-nav-icon mb-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <span class="nav-label text-xs font-medium whitespace-nowrap">Profile</span>
            <div class="bottom-nav-indicator absolute -bottom-2 left-1/2 w-1.5 h-1.5 bg-orange-500 rounded-full"></div>
        </a>

    </div>
</nav>