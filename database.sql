
CREATE DATABASE IF NOT EXISTS food_ordering;
USE food_ordering;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Menu categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    status BOOLEAN DEFAULT TRUE
);

-- Menu items table
CREATE TABLE IF NOT EXISTS menu_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    status BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_id VARCHAR(50) UNIQUE,
    total_amount DECIMAL(10,2) NOT NULL,
    paid_amount DECIMAL(10,2) NOT NULL,
    payment_type ENUM('partial', 'full') NOT NULL,
    payment_method ENUM('esewa', 'fonepay') NOT NULL,
    payment_ref VARCHAR(100),
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    delivery_address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    delivery_time VARCHAR(50),
    special_instructions TEXT,
    order_status ENUM('pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Momo', 'Delicious dumplings'),
('Pizza', 'Fresh baked pizzas'),
('Burger', 'Juicy burgers'),
('Drinks', 'Refreshing beverages');

-- Insert sample menu items
INSERT INTO menu_items (category_id, name, description, price, image) VALUES
(1, 'Chicken Momo', 'Steamed chicken dumplings', 150.00, 'chicken_momo.jpg'),
(1, 'Veg Momo', 'Steamed vegetable dumplings', 120.00, 'veg_momo.jpg'),
(2, 'Margherita Pizza', 'Classic cheese and tomato', 350.00, 'margherita.jpg'),
(2, 'Pepperoni Pizza', 'Spicy pepperoni with cheese', 450.00, 'pepperoni.jpg'),
(3, 'Classic Burger', 'Beef patty with cheese', 250.00, 'classic_burger.jpg'),
(3, 'Chicken Burger', 'Grilled chicken patty', 220.00, 'chicken_burger.jpg'),
(4, 'Coca Cola', 'Refreshing cola drink', 60.00, 'coke.jpg'),
(4, 'Mango Lassi', 'Sweet mango yogurt drink', 120.00, 'mango_lassi.jpg');

-- Create admin user
INSERT INTO users (username, password, email, full_name, phone, address) VALUES
('admin', '$2y$10$YourHashedPasswordHere', 'admin@example.com', 'Admin User', '9800000000', 'Admin Address');

-- Create indexes for better performance
CREATE INDEX idx_user_id ON orders(user_id);
CREATE INDEX idx_order_id ON order_items(order_id);
CREATE INDEX idx_menu_item_id ON order_items(menu_item_id);
CREATE INDEX idx_category_id ON menu_items(category_id);
CREATE INDEX idx_payment_status ON orders(payment_status);
CREATE INDEX idx_order_status ON orders(order_status);

-- Create view for order summary
CREATE VIEW order_summary AS
SELECT 
    o.id AS order_id,
    o.order_id AS reference_id,
    u.full_name AS customer_name,
    u.phone AS customer_phone,
    o.total_amount,
    o.paid_amount,
    o.payment_status,
    o.order_status,
    o.created_at AS order_date
FROM orders o
JOIN users u ON o.user_id = u.id;

-- Create view for menu items with categories
CREATE VIEW menu_with_categories AS
SELECT 
    m.id,
    m.name AS item_name,
    m.description,
    m.price,
    m.image,
    c.name AS category_name
FROM menu_items m
JOIN categories c ON m.category_id = c.id
WHERE m.status = TRUE;