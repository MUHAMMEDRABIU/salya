// Get all navigation items
      const navItems = document.querySelectorAll('.nav-item');
      
      // Handle navigation click
      function handleNavClick(clickedItem) {
        // Add activation animation
        clickedItem.classList.add('activating');
        setTimeout(() => {
          clickedItem.classList.remove('activating');
        }, 300);
        
        // Remove active class from all items
        navItems.forEach(item => {
          item.classList.remove('active');
        });
        
        // Add active class to clicked item
        clickedItem.classList.add('active');
        
        // Get target and log for debugging
        const target = clickedItem.dataset.target;
        console.log('Navigation clicked:', target);
        
        // Add haptic feedback if supported
        if ('vibrate' in navigator) {
          navigator.vibrate(10);
        }
      }
      
      // Add click event listeners
      navItems.forEach(item => {
        item.addEventListener('click', (e) => {
          e.preventDefault();
          handleNavClick(item);
        });
      });
      
      // Keyboard navigation
      document.addEventListener('keydown', (e) => {
        const currentActive = document.querySelector('.nav-item.active');
        const currentIndex = Array.from(navItems).indexOf(currentActive);
        
        let newIndex = currentIndex;
        
        if (e.key === 'ArrowLeft') {
          e.preventDefault();
          newIndex = currentIndex > 0 ? currentIndex - 1 : navItems.length - 1;
        } else if (e.key === 'ArrowRight') {
          e.preventDefault();
          newIndex = currentIndex < navItems.length - 1 ? currentIndex + 1 : 0;
        } else if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          handleNavClick(currentActive);
          return;
        }
        
        if (newIndex !== currentIndex) {
          handleNavClick(navItems[newIndex]);
        }
      });
      
      // Update notification badge function
      function updateNotificationBadge(count) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
          if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count.toString();
            badge.style.display = 'flex';
          } else {
            badge.style.display = 'none';
          }
        }
      }
      
      // Enhanced scroll effect
      let lastScrollY = window.scrollY;
      window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        const navContainer = document.querySelector('.nav-container');
        
        if (scrollY > 50) {
          navContainer.style.boxShadow = '0 30px 60px rgba(0, 0, 0, 0.2), 0 15px 35px rgba(0, 0, 0, 0.15)';
        } else {
          navContainer.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15), 0 8px 24px rgba(0, 0, 0, 0.1)';
        }
        
        lastScrollY = scrollY;
      });
      
      // Touch gestures for mobile
      let startX = 0;
      const navContainer = document.querySelector('.nav-container');
      
      navContainer.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
      });
      
      navContainer.addEventListener('touchend', (e) => {
        const endX = e.changedTouches[0].clientX;
        const deltaX = endX - startX;
        
        if (Math.abs(deltaX) > 50) {
          const currentActive = document.querySelector('.nav-item.active');
          const currentIndex = Array.from(navItems).indexOf(currentActive);
          
          let newIndex;
          if (deltaX > 0) {
            // Swipe right - go to previous
            newIndex = currentIndex > 0 ? currentIndex - 1 : navItems.length - 1;
          } else {
            // Swipe left - go to next
            newIndex = currentIndex < navItems.length - 1 ? currentIndex + 1 : 0;
          }
          
          handleNavClick(navItems[newIndex]);
        }
      });
      
      // Initialize
      console.log('Bottom navigation initialized');