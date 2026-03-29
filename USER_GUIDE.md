# Food Pre-Ordering System - User Guide

## Table of Contents
1. [Introduction](#introduction)
2. [Getting Started](#getting-started)
3. [User Features](#user-features)
4. [Admin Features](#admin-features)
5. [Troubleshooting](#troubleshooting)
6. [FAQ](#faq)

## Introduction

The Food Pre-Ordering System is a web-based application that allows users to browse menus, place food orders, and manage their accounts. Administrators can manage the menu, orders, users, and generate reports.

## Getting Started

### For Users

1. **Registration**
   - Visit the homepage and click "Register"
   - Fill in your details: username, email, password, full name, and phone
   - Click "Register" to create your account

2. **Login**
   - Visit the homepage and click "Login"
   - Enter your username and password
   - Click "Login" to access your account

3. **First Time Setup**
   - Complete your profile by adding your address
   - Browse the menu to familiarize yourself with available items

### For Administrators

1. **Admin Access**
   - Visit `your-domain.com/admin/`
   - Use admin credentials to login
   - Access the dashboard to begin managing the system

## User Features

### Browsing the Menu

1. **View Categories**
   - Menu items are organized by categories (e.g., Starters, Main Course, Desserts)
   - Click category tabs to filter items

2. **Search Functionality**
   - Use the search box to find specific items by name
   - Results update in real-time as you type

3. **Price Filtering**
   - Set minimum and maximum price ranges
   - Click "Apply" to filter items within your budget

4. **Sorting Options**
   - Sort by price: Low to High or High to Low
   - Changes apply immediately to the displayed items

### Personalized Recommendations

- **Smart Suggestions**: The system analyzes your preferences and popular choices
- **Highlighted Items**: Recommended items are marked with a "RECOMMENDED" badge
- **Personalized Reasons**: Each recommendation includes why it was suggested

### Shopping Cart Management

1. **Adding Items**
   - Select the desired quantity using +/- buttons or direct input
   - Click "Add to Cart" for each item
   - A success message confirms the addition

2. **Viewing Cart**
   - Click the cart icon in the navigation bar
   - View all items, quantities, and total cost

3. **Modifying Cart**
   - Update quantities directly in the cart
   - Remove items using the remove button
   - Cart total updates automatically

### Placing Orders

1. **Checkout Process**
   - Review items in your cart
   - Proceed to checkout
   - Select payment method (Cash, PayPal, or eSewa)

2. **Order Details**
   - Specify preferred pickup/delivery time
   - Add special instructions or comments
   - Confirm order details before submission

3. **Order Confirmation**
   - Receive confirmation message with order ID
   - Track order status in "My Orders"

### Order Management

1. **View Order History**
   - Access "My Orders" from the navigation menu
   - View all past and current orders
   - See order status, total amount, and date

2. **Order Status Tracking**
   - Real-time status updates: Pending, Confirmed, Preparing, Ready, Completed, Cancelled
   - Status changes are reflected immediately

3. **Order Cancellation**
   - Cancel orders that are still in "Pending" status
   - Contact admin for cancellations in other statuses

### Feedback System

1. **Leaving Comments**
   - Visit the comment section
   - Write your feedback about food quality, service, etc.
   - Submit your comment for moderation

2. **Rating System**
   - Rate your experience on a scale of 1-5 stars
   - Your ratings help improve service quality

## Admin Features

### Dashboard Overview

1. **System Statistics**
   - View total users, orders, and revenue
   - Monitor recent activity and trends
   - Quick access to key management functions

2. **Recent Orders**
   - View latest orders at a glance
   - Quick status updates for recent orders
   - Direct links to full order management

### Menu Management

1. **Adding Menu Items**
   - Navigate to "Add Menu"
   - Fill in item details: name, description, price, category
   - Upload high-quality images
   - Set item status (active/inactive)

2. **Managing Categories**
   - Add new categories (e.g., Vegetarian, Non-Vegetarian, Specials)
   - Edit existing category details
   - Activate or deactivate categories

3. **Editing Items**
   - View all menu items in "Manage Menu"
   - Edit item details, pricing, or descriptions
   - Update item images
   - Change item status

### Order Management

1. **Viewing Orders**
   - Access "Manage Orders" for complete order list
   - Filter orders by status, date, or user
   - View detailed order information

2. **Order Processing**
   - Update order status as they progress
   - Mark orders as confirmed, preparing, ready, or completed
   - Handle cancellations and refunds

3. **Order Details**
   - View complete order breakdown
   - See customer information and special instructions
   - Track payment status and method

### User Management

1. **User Overview**
   - View all registered users
   - See user activity and order history
   - Filter users by registration date or status

2. **User Actions**
   - View detailed user profiles
   - Block problematic users if necessary
   - Monitor user engagement

### Comment Management

1. **Moderation**
   - Review all user comments
   - Approve appropriate comments
   - Remove inappropriate content

2. **Comment Analytics**
   - View average ratings
   - Identify common feedback themes
   - Monitor customer satisfaction

### Sales Reporting

1. **Date Range Selection**
   - Select start and end dates for reports
   - Choose from today, this week, this month, or custom range
   - Generate reports for any historical period

2. **Report Details**
   - View total orders, completed vs cancelled
   - See detailed order breakdown
   - Analyze revenue by date

3. **Report Actions**
   - Print reports for record keeping
   - Export data for further analysis
   - Share reports with management

### Payment Management

1. **Payment Tracking**
   - Monitor payment status for all orders
   - View successful and failed transactions
   - Track payment methods used

2. **Transaction Details**
   - View PayPal and eSewa transaction IDs
   - Verify payment completion
   - Handle payment disputes

## Troubleshooting

### Common User Issues

1. **Login Problems**
   - **Issue**: Cannot login with correct credentials
   - **Solution**: Clear browser cache and cookies, try again
   - **Issue**: Account locked or blocked
   - **Solution**: Contact admin for assistance

2. **Cart Issues**
   - **Issue**: Items not adding to cart
   - **Solution**: Check internet connection, refresh page
   - **Issue**: Cart emptying unexpectedly
   - **Solution**: Ensure cookies are enabled in browser

3. **Order Problems**
   - **Issue**: Order not appearing in history
   - **Solution**: Check order confirmation email, contact support
   - **Issue**: Order status not updating
   - **Solution**: Refresh the page, check spam folder for updates

### Common Admin Issues

1. **Dashboard Problems**
   - **Issue**: Statistics not loading
   - **Solution**: Check database connection, refresh page
   - **Issue**: Recent orders not displaying
   - **Solution**: Verify order data in database

2. **Menu Management**
   - **Issue**: Images not uploading
   - **Solution**: Check file size and format, verify Uploads directory permissions
   - **Issue**: Items not appearing on menu
   - **Solution**: Ensure item status is set to active

3. **Report Generation**
   - **Issue**: Reports not generating
   - **Solution**: Check date range selection, verify database queries
   - **Issue**: Missing data in reports
   - **Solution**: Ensure orders have proper status and dates

### Technical Issues

1. **Database Connection**
   - **Issue**: Database connection errors
   - **Solution**: Check config/db.php credentials, verify MySQL server status

2. **File Permissions**
   - **Issue**: Upload failures
   - **Solution**: Set proper permissions on Uploads directory (755 or 777)

3. **Browser Compatibility**
   - **Issue**: Features not working in certain browsers
   - **Solution**: Use modern browsers (Chrome, Firefox, Safari, Edge)

## FAQ

### General Questions

**Q: Is my personal information secure?**
A: Yes, all user data is encrypted and stored securely. Passwords are hashed using industry-standard methods.

**Q: Can I modify my order after placing it?**
A: You can cancel pending orders. For modifications, contact our support team immediately.

**Q: How do I know my order was received?**
A: You'll receive an order confirmation email and can view your order in "My Orders."

### Payment Questions

**Q: What payment methods are accepted?**
A: We accept Cash on Delivery, PayPal, and eSewa.

**Q: Is online payment secure?**
A: Yes, we use secure payment gateways with encryption for all online transactions.

**Q: What if my payment fails?**
A: You'll be redirected to try again. If issues persist, contact support with your order details.

### Menu Questions

**Q: Are there vegetarian options available?**
A: Yes, we offer a variety of vegetarian dishes. Use the category filter to find them.

**Q: Can I customize my order?**
A: Yes, add special instructions during checkout for any customizations.

**Q: How often is the menu updated?**
A: Menu items are updated regularly. Check back frequently for new offerings.

### Admin Questions

**Q: How do I add a new admin user?**
A: Currently, admin accounts are created manually in the database. Contact your system administrator.

**Q: Can I backup the system data?**
A: Yes, use your database management tool to export the food_ordering database.

**Q: How do I handle high order volumes?**
A: Use the order filtering and batch processing features to manage multiple orders efficiently.

## Support Contact

For additional assistance:
- **Email**: support@foodordering.com
- **Phone**: +1-234-567-8900
- **Admin Portal**: Use the internal messaging system

**Business Hours**: Monday-Sunday, 8:00 AM - 10:00 PM

---

*This user guide is designed to help you make the most of the Food Pre-Ordering System. For the most up-to-date information, please refer to the online documentation.*