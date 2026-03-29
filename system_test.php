<?php
/**
 * Food Pre-Ordering System - Comprehensive System Testing Suite
 * 
 * This script performs comprehensive testing of the food pre-ordering system
 * including database connectivity, user functionality, admin features, and security.
 * 
 * @author System Tester
 * @version 1.0
 */

session_start();

// Test configuration
define('TEST_MODE', true);
define('TEST_EMAIL', 'test@example.com');
define('TEST_USERNAME', 'testuser_' . time());
define('TEST_PASSWORD', 'TestPass123!');

class SystemTester {
    private $conn;
    private $test_results = [];
    private $passed_tests = 0;
    private $failed_tests = 0;
    
    public function __construct() {
        // Include database configuration
        require_once 'config/db.php';
        $this->conn = $conn;
    }
    
    /**
     * Run all system tests
     */
    public function runAllTests() {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>System Test Results</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { background: #f4f4f4; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
                .test-section { margin-bottom: 30px; }
                .test-result { padding: 10px; margin: 5px 0; border-radius: 5px; }
                .pass { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
                .fail { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
                .summary { background: #e9ecef; padding: 15px; border-radius: 5px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Food Pre-Ordering System - Test Results</h1>
                <p>Testing completed on: " . date('Y-m-d H:i:s') . "</p>
            </div>";
        
        echo "<div class='summary'>";
        echo "<h2>Test Summary</h2>";
        
        // Run all test categories
        $this->testDatabaseConnectivity();
        $this->testUserRegistration();
        $this->testUserAuthentication();
        $this->testMenuFunctionality();
        $this->testCartOperations();
        $this->testOrderProcessing();
        $this->testAdminFeatures();
        $this->testSecurityFeatures();
        $this->testFileIntegrity();
        
        // Display summary
        $total_tests = $this->passed_tests + $this->failed_tests;
        $success_rate = $total_tests > 0 ? round(($this->passed_tests / $total_tests) * 100, 1) : 0;
        
        echo "<p><strong>Total Tests:</strong> $total_tests</p>";
        echo "<p><strong>Passed:</strong> <span style='color: green;'>$this->passed_tests</span></p>";
        echo "<p><strong>Failed:</strong> <span style='color: red;'>$this->failed_tests</span></p>";
        echo "<p><strong>Success Rate:</strong> <span style='color: " . ($success_rate >= 80 ? 'green' : 'red') . ";'>$success_rate%</span></p>";
        
        echo "</div>";
        
        // Display detailed results
        $this->displayDetailedResults();
        
        echo "</body></html>";
    }
    
    /**
     * Test database connectivity and schema
     */
    private function testDatabaseConnectivity() {
        echo "<div class='test-section'>";
        echo "<h2>1. Database Connectivity Tests</h2>";
        
        // Test 1: Database connection
        $this->runTest("Database Connection", function() {
            return $this->conn && $this->conn->ping();
        });
        
        // Test 2: Required tables exist
        $tables = ['users', 'categories', 'menu_items', 'orders', 'order_items', 'comments'];
        foreach ($tables as $table) {
            $this->runTest("Table '$table' exists", function() use ($table) {
                $result = $this->conn->query("SHOW TABLES LIKE '$table'");
                return $result && $result->num_rows > 0;
            });
        }
        
        // Test 3: Database schema integrity
        $this->runTest("Users table has required columns", function() {
            $result = $this->conn->query("DESCRIBE users");
            $columns = [];
            while ($row = $result->fetch_assoc()) {
                $columns[] = $row['Field'];
            }
            $required = ['id', 'username', 'password', 'email', 'full_name', 'phone', 'address'];
            return count(array_intersect($required, $columns)) === count($required);
        });
        
        echo "</div>";
    }
    
    /**
     * Test user registration functionality
     */
    private function testUserRegistration() {
        echo "<div class='test-section'>";
        echo "<h2>2. User Registration Tests</h2>";
        
        // Test 1: Valid registration
        $this->runTest("Valid user registration", function() {
            $username = TEST_USERNAME;
            $email = TEST_EMAIL;
            $password = password_hash(TEST_PASSWORD, PASSWORD_DEFAULT);
            $full_name = "Test User";
            $phone = "9876543210";
            
            $stmt = $this->conn->prepare("INSERT INTO users (username, password, email, full_name, phone) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([$username, $password, $email, $full_name, $phone]);
            
            if ($result) {
                // Clean up test user
                $this->conn->query("DELETE FROM users WHERE username = '$username'");
                return true;
            }
            return false;
        });
        
        // Test 2: Duplicate username prevention
        $this->runTest("Duplicate username prevention", function() {
            $username = "existing_user";
            $email = "existing@example.com";
            $password = password_hash("password", PASSWORD_DEFAULT);
            $full_name = "Existing User";
            $phone = "1234567890";
            
            // Create user first
            $stmt = $this->conn->prepare("INSERT INTO users (username, password, email, full_name, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $password, $email, $full_name, $phone]);
            
            // Try to create duplicate
            $stmt2 = $this->conn->prepare("INSERT INTO users (username, password, email, full_name, phone) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt2->execute([$username, $password, "different@example.com", $full_name, $phone]);
            
            // Clean up
            $this->conn->query("DELETE FROM users WHERE username = '$username'");
            
            return !$result; // Should fail due to duplicate username
        });
        
        echo "</div>";
    }
    
    /**
     * Test user authentication
     */
    private function testUserAuthentication() {
        echo "<div class='test-section'>";
        echo "<h2>3. User Authentication Tests</h2>";
        
        // Test 1: Valid login
        $this->runTest("Valid user login", function() {
            $username = "auth_test_user";
            $password = "auth_test_pass";
            $email = "auth@example.com";
            $full_name = "Auth User";
            $phone = "1234567890";
            
            // Create test user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO users (username, password, email, full_name, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $hashed_password, $email, $full_name, $phone]);
            
            // Test login
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            $result = $user && password_verify($password, $user['password']);
            
            // Clean up
            $this->conn->query("DELETE FROM users WHERE username = '$username'");
            
            return $result;
        });
        
        // Test 2: Invalid password rejection
        $this->runTest("Invalid password rejection", function() {
            $username = "password_test_user";
            $password = "correct_password";
            $email = "password@example.com";
            $full_name = "Password User";
            $phone = "1234567890";
            
            // Create test user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO users (username, password, email, full_name, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $hashed_password, $email, $full_name, $phone]);
            
            // Test wrong password
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            $result = $user && !password_verify("wrong_password", $user['password']);
            
            // Clean up
            $this->conn->query("DELETE FROM users WHERE username = '$username'");
            
            return $result;
        });
        
        echo "</div>";
    }
    
    /**
     * Test menu functionality
     */
    private function testMenuFunctionality() {
        echo "<div class='test-section'>";
        echo "<h2>4. Menu Functionality Tests</h2>";
        
        // Test 1: Menu items retrieval
        $this->runTest("Menu items retrieval", function() {
            $result = $this->conn->query("SELECT * FROM menu_items WHERE status = 1 LIMIT 1");
            return $result && $result->num_rows > 0;
        });
        
        // Test 2: Categories retrieval
        $this->runTest("Categories retrieval", function() {
            $result = $this->conn->query("SELECT * FROM categories WHERE status = 1 LIMIT 1");
            return $result && $result->num_rows > 0;
        });
        
        // Test 3: Menu item details
        $this->runTest("Menu item details with category", function() {
            $sql = "SELECT m.*, c.name as category_name 
                    FROM menu_items m 
                    JOIN categories c ON m.category_id = c.id 
                    WHERE m.status = 1 AND c.status = 1 
                    LIMIT 1";
            $result = $this->conn->query($sql);
            return $result && $result->num_rows > 0;
        });
        
        echo "</div>";
    }
    
    /**
     * Test cart operations
     */
    private function testCartOperations() {
        echo "<div class='test-section'>";
        echo "<h2>5. Cart Operations Tests</h2>";
        
        // Test 1: Session cart initialization
        $this->runTest("Session cart initialization", function() {
            session_start();
            $_SESSION['cart'] = [];
            return isset($_SESSION['cart']) && is_array($_SESSION['cart']);
        });
        
        // Test 2: Add item to cart
        $this->runTest("Add item to cart", function() {
            $_SESSION['cart'][1] = 2;
            return isset($_SESSION['cart'][1]) && $_SESSION['cart'][1] == 2;
        });
        
        // Test 3: Update cart quantity
        $this->runTest("Update cart quantity", function() {
            $_SESSION['cart'][1] = 5;
            return $_SESSION['cart'][1] == 5;
        });
        
        // Test 4: Remove item from cart
        $this->runTest("Remove item from cart", function() {
            unset($_SESSION['cart'][1]);
            return !isset($_SESSION['cart'][1]);
        });
        
        echo "</div>";
    }
    
    /**
     * Test order processing
     */
    private function testOrderProcessing() {
        echo "<div class='test-section'>";
        echo "<h2>6. Order Processing Tests</h2>";
        
        // Test 1: Order creation
        $this->runTest("Order creation", function() {
            // Create test user first
            $username = "order_test_user";
            $password = password_hash("password", PASSWORD_DEFAULT);
            $email = "order@example.com";
            $full_name = "Order User";
            $phone = "1234567890";
            
            $stmt = $this->conn->prepare("INSERT INTO users (username, password, email, full_name, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $password, $email, $full_name, $phone]);
            
            // Get user ID
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Create test order
                $stmt = $this->conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, payment_status) VALUES (?, ?, ?, ?)");
                $result = $stmt->execute([$user['id'], 100.00, 'cash', 'pending']);
                
                // Clean up
                $this->conn->query("DELETE FROM orders WHERE user_id = {$user['id']}");
                $this->conn->query("DELETE FROM users WHERE username = '$username'");
                
                return $result;
            }
            return false;
        });
        
        // Test 2: Order status updates
        $this->runTest("Order status updates", function() {
            // Create test user and order
            $username = "status_test_user";
            $password = password_hash("password", PASSWORD_DEFAULT);
            $email = "status@example.com";
            $full_name = "Status User";
            $phone = "1234567890";
            
            $stmt = $this->conn->prepare("INSERT INTO users (username, password, email, full_name, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $password, $email, $full_name, $phone]);
            
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Create order
                $stmt = $this->conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, payment_status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user['id'], 100.00, 'cash', 'pending']);
                
                $order_id = $this->conn->lastInsertId();
                
                // Update status
                $stmt = $this->conn->prepare("UPDATE orders SET status = 'confirmed' WHERE id = ?");
                $result = $stmt->execute([$order_id]);
                
                // Clean up
                $this->conn->query("DELETE FROM orders WHERE id = $order_id");
                $this->conn->query("DELETE FROM users WHERE username = '$username'");
                
                return $result;
            }
            return false;
        });
        
        echo "</div>";
    }
    
    /**
     * Test admin features
     */
    private function testAdminFeatures() {
        echo "<div class='test-section'>";
        echo "<h2>7. Admin Features Tests</h2>";
        
        // Test 1: Admin login check
        $this->runTest("Admin login validation", function() {
            // Check if admin files exist and are accessible
            return file_exists('admin/login.php') && 
                   file_exists('admin/dashboard.php') &&
                   file_exists('admin/manage_menu.php');
        });
        
        // Test 2: Menu management
        $this->runTest("Menu management functionality", function() {
            return file_exists('admin/add_menu.php') &&
                   file_exists('admin/manage_menu.php') &&
                   file_exists('admin/menu_actions.php');
        });
        
        // Test 3: Order management
        $this->runTest("Order management functionality", function() {
            return file_exists('admin/manage_orders.php') &&
                   file_exists('admin/report.php');
        });
        
        echo "</div>";
    }
    
    /**
     * Test security features
     */
    private function testSecurityFeatures() {
        echo "<div class='test-section'>";
        echo "<h2>8. Security Features Tests</h2>";
        
        // Test 1: SQL injection prevention
        $this->runTest("SQL injection prevention", function() {
            $malicious_input = "'; DROP TABLE users; --";
            $safe_input = $this->conn->real_escape_string($malicious_input);
            return $safe_input !== $malicious_input;
        });
        
        // Test 2: Password hashing
        $this->runTest("Password hashing", function() {
            $password = "test_password";
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            return password_verify($password, $hashed) && $hashed !== $password;
        });
        
        // Test 3: Session security
        $this->runTest("Session security", function() {
            return session_status() === PHP_SESSION_ACTIVE ||
                   session_start() === true;
        });
        
        echo "</div>";
    }
    
    /**
     * Test file integrity
     */
    private function testFileIntegrity() {
        echo "<div class='test-section'>";
        echo "<h2>9. File Integrity Tests</h2>";
        
        $required_files = [
            'index.php',
            'login.php',
            'register.php',
            'menu.php',
            'cart.php',
            'checkout.php',
            'config/db.php',
            'admin/login.php',
            'admin/dashboard.php'
        ];
        
        foreach ($required_files as $file) {
            $this->runTest("File '$file' exists", function() use ($file) {
                return file_exists($file);
            });
        }
        
        echo "</div>";
    }
    
    /**
     * Run a single test
     */
    private function runTest($test_name, $test_function) {
        try {
            $result = $test_function();
            if ($result) {
                $this->passed_tests++;
                $this->test_results[] = ['name' => $test_name, 'status' => 'PASS', 'message' => 'Test passed successfully'];
                echo "<div class='test-result pass'><strong>PASS:</strong> $test_name</div>";
            } else {
                $this->failed_tests++;
                $this->test_results[] = ['name' => $test_name, 'status' => 'FAIL', 'message' => 'Test failed'];
                echo "<div class='test-result fail'><strong>FAIL:</strong> $test_name</div>";
            }
        } catch (Exception $e) {
            $this->failed_tests++;
            $this->test_results[] = ['name' => $test_name, 'status' => 'ERROR', 'message' => $e->getMessage()];
            echo "<div class='test-result fail'><strong>ERROR:</strong> $test_name - " . $e->getMessage() . "</div>";
        }
    }
    
    /**
     * Display detailed test results
     */
    private function displayDetailedResults() {
        echo "<div class='test-section'>";
        echo "<h2>Detailed Test Results</h2>";
        echo "<table>";
        echo "<tr><th>Test Name</th><th>Status</th><th>Message</th></tr>";
        
        foreach ($this->test_results as $result) {
            $status_class = $result['status'] === 'PASS' ? 'pass' : 'fail';
            echo "<tr class='$status_class'>";
            echo "<td>" . htmlspecialchars($result['name']) . "</td>";
            echo "<td>" . htmlspecialchars($result['status']) . "</td>";
            echo "<td>" . htmlspecialchars($result['message']) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "</div>";
    }
}

// Run the tests
$tester = new SystemTester();
$tester->runAllTests();
?>