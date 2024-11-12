<?php
class MenuController {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getCategories() {
        $sql = "SELECT * FROM categories WHERE status = 1";
        return $this->conn->query($sql);
    }
    
    public function getMenuItems() {
        $sql = "SELECT m.*, c.name as category_name 
                FROM menu_items m 
                JOIN categories c ON m.category_id = c.id 
                WHERE m.status = 1 
                ORDER BY c.id, m.name";
        $result = $this->conn->query($sql);
        
        $menu_by_category = [];
        while($item = $result->fetch_assoc()) {
            $menu_by_category[$item['category_name']][] = $item;
        }
        return $menu_by_category;
    }
} 