<?php
/**
 * Simple Food Recommendation System
 * Shows popular items for new users and basic suggestions for returning users
 */

class SimpleRecommendationSystem {
    private $conn;
    // init database connection
    public function __construct($database_connection) {
        $this->conn = $database_connection;
    }
    
    public function getRecommendations($user_id, $limit = 4) {
        // Check if user has any completed orders
        $has_orders = $this->userHasOrders($user_id);
        
        if (!$has_orders) {
            // New user: show popular items
            return $this->getPopularItems($limit);
        } else {
            // Returning user: show popular items + some variety
            $popular = $this->getPopularItems($limit);
            $categories = $this->getPopularByCategory($limit);
            
            // Mix popular and category items
            $all_items = array_merge($popular, $categories);
            
            // Remove duplicates 
            $unique_items = [];
            foreach ($all_items as $item) {
                if (!isset($unique_items[$item['id']])) {
                    $unique_items[$item['id']] = $item;
                }
            }
            
            return array_slice($unique_items, 0, $limit);
        }
    }
    
    /**
     * Check if user has completed orders
     */
    private function userHasOrders($user_id) {
        $sql = "SELECT COUNT(*) as count FROM orders WHERE user_id = $user_id AND status = 'completed'";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }
    
    /**
     * Get most popular items kati xoti order vako xa 
     */
    public function getPopularItems($limit = 4) {
        $sql = "
            SELECT mi.*, COUNT(oi.item_id) as order_count
            FROM menu_items mi
            JOIN order_items oi ON mi.id = oi.item_id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'completed' AND mi.status = 1
            GROUP BY mi.id
            ORDER BY order_count DESC
            LIMIT $limit
        ";
        
        $result = $this->conn->query($sql);
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $row['reason'] = 'Most popular dishes';
            $items[] = $row;
        }
        
        return $items;
    }
    
    /**
     * Get popular items by category
     */
    private function getPopularByCategory($limit = 4) {
        $sql = "
            SELECT mi.*, c.name as category_name, COUNT(oi.item_id) as order_count
            FROM menu_items mi
            JOIN categories c ON mi.category_id = c.id
            JOIN order_items oi ON mi.id = oi.item_id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'completed' AND mi.status = 1
            GROUP BY mi.id, c.name
            ORDER BY order_count DESC
            LIMIT $limit
        ";
        
        $result = $this->conn->query($sql);
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $row['reason'] = 'Popular in ' . $row['category_name'];
            $items[] = $row;
        }
        
        return $items;
    }
}
?>