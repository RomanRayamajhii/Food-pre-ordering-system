# Food Pre-Ordering System

A comprehensive web-based food pre-ordering system built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features

### User Features
- **User Registration & Login**: Secure user authentication with password hashing
- **Menu Browsing**: View all available food items organized by categories
- **Search & Filter**: Search items by name and filter by price range
- **Personalized Recommendations**: AI-powered food recommendations based on user preferences
- **Shopping Cart**: Add/remove items and manage quantities
- **Order Placement**: Place orders with preferred timing and special instructions
- **Order History**: View past orders and their status
- **Comments & Ratings**: Leave feedback on food items

### Admin Features
- **Admin Dashboard**: Overview of system statistics and recent activity
- **Menu Management**: Add, edit, and manage food items and categories
- **Order Management**: View, update, and manage customer orders
- **User Management**: View and manage registered users
- **Comment Management**: Moderate user comments and ratings
- **Sales Reports**: Generate detailed sales reports with date filtering
- **Payment Tracking**: Monitor payment status and transaction IDs

### Technical Features
- **Database-Driven**: MySQL database with proper relationships and constraints
- **Security**: SQL injection prevention, password hashing, session management
- **Responsive Design**: Mobile-friendly interface
- **Payment Integration**: Support for PayPal and eSewa payment gateways
- **Real-time Updates**: Dynamic cart updates and order status tracking

## System Architecture

### Frontend
- **HTML5**: Semantic markup for better SEO and accessibility
- **CSS3**: Modern styling with flexbox and grid layouts
- **JavaScript**: Interactive features including search, filtering, and cart management
- **jQuery**: Simplified DOM manipulation and AJAX requests

### Backend
- **PHP 7+**: Server-side scripting with proper error handling
- **MySQL**: Relational database with optimized queries
- **Session Management**: Secure user session handling
- **File Upload**: Image upload and management for menu items

### Database Schema
The system uses the following main tables:

- **users**: User registration and authentication data
- **categories**: Food category definitions
- **menu_items**: Food item details with pricing and images
- **orders**: Customer order information
- **order_items**: Individual items within each order
- **comments**: User feedback and ratings

## Installation

### Prerequisites
- Apache/Nginx web server
- PHP 7.0 or higher
- MySQL 5.7 or higher
- Composer (for dependency management)

### Setup Instructions

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd Food-pre-ordering-system
   ```

2. **Set up the database**:
   - Import `database.sql` into your MySQL server
   - Create database: `food_ordering`
   - Run the SQL script to create tables and sample data

3. **Configure database connection**:
   - Edit `config/db.php` with your database credentials
   ```php
   $host = "localhost";
   $username = "your_username";
   $password = "your_password";
   $database = "food_ordering";
   ```

4. **Set up file permissions**:
   - Ensure the `Uploads/` directory is writable by the web server
   - Set proper permissions for image uploads

5. **Configure web server**:
   - Point your web server document root to the project directory
   - Ensure URL rewriting is enabled for clean URLs

6. **Test the installation**:
   - Visit `http://localhost/your-project/` in your browser
   - Access admin panel at `http://localhost/your-project/admin/`

## Usage

### For Users
1. **Registration**: Visit the registration page to create an account
2. **Login**: Use your credentials to access the menu
3. **Browse Menu**: View available items by category
4. **Search & Filter**: Use search box and price filters to find specific items
5. **Add to Cart**: Select items and add them to your cart
6. **Checkout**: Review your order and complete the purchase
7. **Track Orders**: View order status in your account

### For Administrators
1. **Admin Login**: Access the admin panel with admin credentials
2. **Dashboard**: View system overview and recent activity
3. **Menu Management**: Add new items, edit existing ones, manage categories
4. **Order Management**: View and update order statuses
5. **User Management**: View registered users and their activity
6. **Reports**: Generate sales reports and analyze data

## Testing

The system includes a comprehensive testing suite:

### Running System Tests
1. Access `system_test.php` in your browser
2. The test suite will automatically run all tests
3. View detailed results including:
   - Database connectivity tests
   - User registration and authentication tests
   - Menu functionality tests
   - Cart operations tests
   - Order processing tests
   - Admin features tests
   - Security feature tests
   - File integrity tests

### Test Categories
- **Database Tests**: Connection, schema validation, table existence
- **User Tests**: Registration, login, password security
- **Functionality Tests**: Menu browsing, cart operations, order processing
- **Security Tests**: SQL injection prevention, password hashing, session security
- **Integration Tests**: File existence, admin panel accessibility

## Security Features

- **Password Hashing**: Uses PHP's `password_hash()` for secure password storage
- **SQL Injection Prevention**: Prepared statements and input sanitization
- **Session Security**: Proper session management and validation
- **File Upload Security**: Image validation and secure file handling
- **Input Validation**: Client and server-side validation for all forms

## Payment Integration

### PayPal Integration
- PayPal payment gateway for online payments
- Transaction tracking and status updates
- Success and failure handling

### eSewa Integration
- Nepalese payment gateway integration
- Local payment processing
- Transaction verification

## Performance Optimization

- **Database Indexing**: Optimized queries with proper indexing
- **Image Optimization**: Compressed images for faster loading
- **Caching**: Session-based caching for improved performance
- **Minification**: CSS and JavaScript optimization

## Troubleshooting

### Common Issues

1. **Database Connection Errors**
   - Check database credentials in `config/db.php`
   - Ensure MySQL server is running
   - Verify database name and table structure

2. **File Upload Issues**
   - Check `Uploads/` directory permissions
   - Verify PHP file upload settings
   - Ensure sufficient disk space

3. **Session Problems**
   - Check PHP session configuration
   - Verify session storage directory permissions
   - Clear browser cookies if needed

4. **Payment Gateway Issues**
   - Verify API credentials for PayPal/eSewa
   - Check callback URL configurations
   - Test in sandbox mode first

### Getting Help
- Check the system logs for error details
- Run the system test suite for diagnostics
- Review browser developer console for JavaScript errors
- Consult the database error logs

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For support and questions:
- Create an issue in the repository
- Check the troubleshooting section above
- Run the system test suite for diagnostics

## Version History

- **v1.0**: Initial release with core functionality
- **v1.1**: Added recommendation system and improved UI
- **v1.2**: Enhanced security features and payment integration
- **v1.3**: Added comprehensive testing suite and documentation

## Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript, jQuery
- **Backend**: PHP 7+, MySQL
- **Security**: Password hashing, prepared statements, session management
- **Payment**: PayPal API, eSewa API
- **Testing**: Custom PHP testing framework
- **Deployment**: Apache/Nginx, PHP-FPM

---

**Note**: This system is designed for educational and small business use. For production deployment, ensure proper security measures and performance optimization based on your specific requirements.