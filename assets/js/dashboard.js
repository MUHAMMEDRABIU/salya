// Sample product data with categories
const products = [
    {
        id: 1,
        name: "Grilled Chicken Breast",
        category: "Chicken",
        description: "Tender grilled chicken breast",
        price: 12.99,
        image: "https://images.pexels.com/photos/106343/pexels-photo-106343.jpeg?auto=compress&cs=tinysrgb&w=400"
    },
    {
        id: 2,
        name: "Chicken Wings",
        category: "Chicken", 
        description: "Crispy buffalo chicken wings",
        price: 9.99,
        image: "https://images.pexels.com/photos/60616/fried-chicken-chicken-fried-crunchy-60616.jpeg?auto=compress&cs=tinysrgb&w=400"
    },
    {
        id: 3,
        name: "Chicken Caesar Salad",
        category: "Chicken",
        description: "Fresh salad with grilled chicken",
        price: 11.99,
        image: "https://images.pexels.com/photos/1059905/pexels-photo-1059905.jpeg?auto=compress&cs=tinysrgb&w=400"
    },
    {
        id: 4,
        name: "Grilled Salmon",
        category: "Fish",
        description: "Fresh Atlantic salmon fillet",
        price: 18.99,
        image: "https://images.pexels.com/photos/725997/pexels-photo-725997.jpeg?auto=compress&cs=tinysrgb&w=400"
    },
    {
        id: 5,
        name: "Fish Tacos",
        category: "Fish",
        description: "Crispy fish tacos with salsa",
        price: 13.99,
        image: "https://images.pexels.com/photos/2762942/pexels-photo-2762942.jpeg?auto=compress&cs=tinysrgb&w=400"
    },
    {
        id: 6,
        name: "Tuna Sashimi",
        category: "Fish",
        description: "Fresh tuna sashimi slices",
        price: 16.99,
        image: "https://images.pexels.com/photos/357756/pexels-photo-357756.jpeg?auto=compress&cs=tinysrgb&w=400"
    },
    {
        id: 7,
        name: "Roasted Turkey",
        category: "Turkey",
        description: "Herb-roasted turkey breast",
        price: 15.99,
        image: "https://images.pexels.com/photos/6210747/pexels-photo-6210747.jpeg?auto=compress&cs=tinysrgb&w=400"
    },
    {
        id: 8,
        name: "Turkey Sandwich",
        category: "Turkey",
        description: "Deli turkey with fresh vegetables",
        price: 8.99,
        image: "https://images.pexels.com/photos/1600727/pexels-photo-1600727.jpeg?auto=compress&cs=tinysrgb&w=400"
    },
    {
        id: 9,
        name: "Turkey Meatballs",
        category: "Turkey",
        description: "Homemade turkey meatballs",
        price: 10.99,
        image: "https://images.pexels.com/photos/1633525/pexels-photo-1633525.jpeg?auto=compress&cs=tinysrgb&w=400"
    }
];

class DashboardManager {
    constructor() {
        this.currentCategory = 'All';
        this.searchQuery = '';
        this.favoriteItems = new Set();
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.renderProducts();
    }

    setupEventListeners() {
        // Tab navigation
        const tabButtons = document.querySelectorAll('.tabs button');
        tabButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                this.handleTabClick(e.target);
            });
        });

        // Search functionality
        const searchInput = document.querySelector('input[type="text"]');
        searchInput.addEventListener('input', (e) => {
            this.handleSearch(e.target.value);
        });

        // Handle mobile menu toggle
        const menuButton = document.querySelector('button[class*="hover:bg-gray-100"]');
        if (menuButton) {
            menuButton.addEventListener('click', this.toggleMobileMenu);
        }
    }

    handleTabClick(button) {
        // Update active tab styling
        const allTabs = document.querySelectorAll('.tabs button');
        allTabs.forEach(tab => {
            tab.classList.remove('bg-gray-900', 'text-white', 'shadow');
            tab.classList.add('bg-gray-100', 'text-gray-600');
        });

        button.classList.remove('bg-gray-100', 'text-gray-600');
        button.classList.add('bg-gray-900', 'text-white', 'shadow');

        // Update current category
        this.currentCategory = button.textContent.trim();
        this.renderProducts();
    }

    handleSearch(query) {
        this.searchQuery = query.toLowerCase();
        this.renderProducts();
    }

    filterProducts() {
        let filtered = products;

        // Filter by category
        if (this.currentCategory !== 'All') {
            filtered = filtered.filter(product => 
                product.category === this.currentCategory
            );
        }

        // Filter by search query
        if (this.searchQuery) {
            filtered = filtered.filter(product => 
                product.name.toLowerCase().includes(this.searchQuery) ||
                product.description.toLowerCase().includes(this.searchQuery) ||
                product.category.toLowerCase().includes(this.searchQuery)
            );
        }

        return filtered;
    }

    toggleFavorite(productId) {
        if (this.favoriteItems.has(productId)) {
            this.favoriteItems.delete(productId);
        } else {
            this.favoriteItems.add(productId);
        }
        this.renderProducts();
    }

    createProductCard(product) {
        const isFavorite = this.favoriteItems.has(product.id);
        const heartClass = isFavorite ? 'text-red-500' : 'text-gray-400';
        const heartFill = isFavorite ? 'fill-current' : '';

        return `
            <div class="cursor-pointer product-card" data-product-id="${product.id}">
                <div class="bg-white rounded-3xl p-4 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                    <div class="relative mb-4">
                        <img src="${product.image}" alt="${product.name}" class="w-full h-40 object-cover rounded-2xl">
                        <button class="favorite-btn absolute top-3 right-3 p-2 rounded-full transition-all duration-200 bg-white hover:text-red-500 ${heartClass}" data-product-id="${product.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart w-5 h-5 ${heartFill}">
                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-2">
                        <h3 class="font-bold text-lg text-gray-900">${product.name}</h3>
                        <p class="text-gray-500 text-sm">${product.description}</p>
                        <p class="font-bold text-xl text-gray-900">$${product.price}</p>
                    </div>
                </div>
            </div>
        `;
    }

    renderProducts() {
        const filteredProducts = this.filterProducts();
        const productsGrid = document.querySelector('.grid.grid-cols-2.gap-4');
        
        if (filteredProducts.length === 0) {
            productsGrid.innerHTML = `
                <div class="col-span-2 text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No items found</h3>
                    <p class="text-gray-500 text-sm">Try adjusting your search or filter criteria</p>
                </div>
            `;
            return;
        }

        productsGrid.innerHTML = filteredProducts
            .map(product => this.createProductCard(product))
            .join('');

        // Add event listeners to favorite buttons
        const favoriteButtons = document.querySelectorAll('.favorite-btn');
        favoriteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const productId = parseInt(button.dataset.productId);
                this.toggleFavorite(productId);
            });
        });

        // Add event listeners to product cards for navigation
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.closest('.favorite-btn')) {
                    const productId = parseInt(card.dataset.productId);
                    this.viewProduct(productId);
                }
            });
        });
    }

    viewProduct(productId) {
        const product = products.find(p => p.id === productId);
        if (product) {
            // You can implement product detail view here
            console.log('Viewing product:', product);
            // For now, just show an alert
            alert(`Viewing ${product.name} - $${product.price}`);
        }
    }

    toggleMobileMenu() {
        // Add mobile menu toggle functionality if needed
        console.log('Mobile menu toggled');
    }

    // Method to add animation effects
    addLoadingAnimation() {
        const productsGrid = document.querySelector('.grid.grid-cols-2.gap-4');
        productsGrid.innerHTML = `
            <div class="col-span-2 text-center py-12">
                <div class="animate-spin w-8 h-8 border-4 border-orange-400 border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="text-gray-500">Loading products...</p>
            </div>
        `;
    }

    // Method to get category counts
    getCategoryCounts() {
        const counts = {
            All: products.length,
            Chicken: products.filter(p => p.category === 'Chicken').length,
            Fish: products.filter(p => p.category === 'Fish').length,
            Turkey: products.filter(p => p.category === 'Turkey').length
        };
        return counts;
    }

    // Method to update tab counts (optional enhancement)
    updateTabCounts() {
        const counts = this.getCategoryCounts();
        const tabs = document.querySelectorAll('.tabs button');
        
        tabs.forEach(tab => {
            const category = tab.textContent.trim();
            if (counts[category]) {
                tab.innerHTML = `${category} <span class="ml-1 text-xs opacity-75">(${counts[category]})</span>`;
            }
        });
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const dashboard = new DashboardManager();
    
    // Optional: Add keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        // Focus search on Ctrl+K or Cmd+K
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('input[type="text"]');
            searchInput.focus();
        }
    });
});

// Export for potential use in other modules
window.DashboardManager = DashboardManager;