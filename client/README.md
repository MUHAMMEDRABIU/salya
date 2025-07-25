# Frozen Foods - User Dashboard & Product Pages

A PHP-based web application for ordering and delivery of frozen food products across Nigeria.

## Features

### User Dashboard
- **Responsive Design**: Built with Tailwind CSS for mobile-first responsive design
- **Product Grid**: Display products in a clean, organized grid layout
- **Search Functionality**: Real-time search through products by name
- **Category Filtering**: Filter products by categories (All, Chicken, Fish, Turkey)
- **Sidebar Navigation**: Collapsible sidebar with smooth animations
- **Favorites**: Add/remove products from favorites with heart icon
- **User Profile**: Gradient profile icon with notification badges

### Product Description Page
- **Dynamic Product Display**: Shows product details based on URL ID parameter
- **Quantity Selector**: Increment/decrement quantity with validation
- **Action Buttons**: Order Now and Add to Cart functionality
- **Rating Display**: Shows product rating with star icon
- **Responsive Layout**: Optimized for mobile and desktop viewing
- **Navigation**: Back button and notification system

## Technology Stack

- **Frontend**: HTML5, Tailwind CSS (via CDN), Vanilla JavaScript
- **Backend**: PHP (procedural, no OOP)
- **Icons**: Font Awesome 6
- **Typography**: DM Sans font family
- **Images**: Pexels stock photos

## Color Palette

- **Accent**: #F97316 (Orange)
- **Gray**: #f6f7fc (Light Gray)
- **Dark**: #201f20 (Dark Gray/Black)
- **Secondary**: #ff7272 (Light Red)
- **Font**: 'DM Sans', sans-serif

## Project Structure

```
/
├── user/
│   ├── dashboard.php          # Main dashboard page
│   └── product.php           # Product description page
├── util/
│   ├── products.php          # Product data and utility functions
│   ├── cart.php             # Cart management functions
│   ├── orders.php           # Order processing functions
│   ├── dashboard.js         # Dashboard JavaScript functionality
│   └── product.js           # Product page JavaScript functionality
└── README.md
```

## Installation

1. Place all files in your web server directory
2. Ensure PHP is enabled on your server
3. Access the dashboard at `/user/dashboard.php`
4. Access individual products at `/user/product.php?id={product_id}`

## Key Features Implementation

### Dashboard Functionality
- **Sidebar Toggle**: Mobile-responsive sidebar with overlay
- **Product Filtering**: Real-time filtering by category and search
- **Favorites System**: Local storage-based favorites management
- **Responsive Grid**: Adapts to different screen sizes

### Product Page Functionality
- **Quantity Management**: Validated quantity controls (1-99)
- **Cart Integration**: Add to cart with local storage
- **Order Processing**: Simulated order placement
- **Notifications**: Toast-style success/error messages

### Utility Functions
- **Product Management**: CRUD operations for products
- **Cart Operations**: Add, remove, update cart items
- **Order Processing**: Create and manage orders
- **Data Validation**: Input sanitization and validation

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile browsers (iOS Safari, Chrome Mobile)
- Responsive design for all screen sizes

## Security Features

- Input sanitization for all user data
- XSS protection with htmlspecialchars
- Phone number validation for Nigerian formats
- Quantity validation and limits

## Performance Optimizations

- CDN-based CSS and fonts
- Optimized images from Pexels
- Minimal JavaScript footprint
- Efficient DOM manipulation

## Future Enhancements

- Database integration (MySQL/PostgreSQL)
- User authentication system
- Payment gateway integration
- Real-time order tracking
- Admin dashboard
- Email notifications
- SMS integration for order updates

## Usage

### Dashboard
1. Navigate to `/user/dashboard.php`
2. Use the search bar to find specific products
3. Click category tabs to filter products
4. Click heart icons to add/remove favorites
5. Click "View" to see product details

### Product Page
1. Access via `/user/product.php?id={product_id}`
2. Adjust quantity using +/- buttons
3. Click "Add to Cart" to add item to cart
4. Click "Order Now" to place immediate order
5. Use back button to return to dashboard

## Contributing

This project follows a modular, procedural PHP approach without OOP. When contributing:

1. Keep functions in appropriate utility files
2. Use consistent naming conventions
3. Follow the established color palette
4. Maintain responsive design principles
5. Test on multiple devices and browsers

## License

This project is developed for educational and commercial use for Frozen Foods delivery service.