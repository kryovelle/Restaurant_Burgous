<?php
require_once __DIR__ . "/connexion.php";

class MenuModel extends Connexion {

    private function db(){
        $db = $this->connecterBDD($this->name, $this->host, $this->user, $this->password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $db;
    }

    function getItems(){
        $db = $this->db();
        $stmt = $db->prepare("SELECT * FROM menu_items ORDER BY category, name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getItemById($id){
        $db = $this->db();
        $stmt = $db->prepare("SELECT * FROM menu_items WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function insertItem($item){
        $db = $this->db();
        $stmt = $db->prepare("INSERT INTO menu_items (name, description, price, category, photo_url,is_recommended, is_available)
                               VALUES (:name, :description, :price, :category, :photo_url,:is_recommended, :is_available)");
        $stmt->execute([
            'name' => $item['name'],
            'description' => $item['description'],
            'price' => $item['price'],
            'category' => $item['category'],
            'is_recommended'=>$item['is_recommended'],
            'photo_url' => $item['photo_url'] ?? null,
            'is_available' => $item['is_available'],
        ]);
        return $db->lastInsertId();
    }

    function updateItem($item){
    $db = $this->db();

    $stmt = $db->prepare("UPDATE menu_items SET
                            name = :name,
                            description = :description,
                            price = :price,
                            category = :category,
                            is_recommended=:is_recommended,
                            is_available = :is_available,
                            photo_url = COALESCE(:photo_url, photo_url)
                           WHERE id = :id");

    $stmt->execute([
        'id' => $item['id'],
        'name' => $item['name'],
        'description' => $item['description'],
        'price' => $item['price'],
        'category' => $item['category'],
        'is_recommended'=>$item['is_recommended'],
        'is_available' => $item['is_available'],
        'photo_url' => $item['photo_url'] ?? null,
    ]);
}

    function deleteItem($id){
        $db = $this->db();
        $stmt = $db->prepare("DELETE FROM menu_items WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    function getCategories(){
        $db = $this->db();
        $stmt = $db->prepare("SELECT * FROM menu_categories ORDER BY title");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getCategoryById($id){
        $db = $this->db();
        $stmt = $db->prepare("SELECT * FROM menu_categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function insertCategory($cat){
        $db = $this->db();
        $stmt = $db->prepare("INSERT INTO menu_categories (title, description, icon)
                               VALUES (:title, :description, :icon)");
        $stmt->execute([
            'title' => $cat['title'],
            'description' => $cat['description'],
            'icon' => $cat['icon'],
        ]);
        return $db->lastInsertId();
    }

    function updateCategory($cat){
        $db = $this->db();
        $stmt = $db->prepare("UPDATE menu_categories SET title = :title, description = :description, icon = :icon
                               WHERE id = :id");
        $stmt->execute([
            'id' => $cat['id'],
            'title' => $cat['title'],
            'description' => $cat['description'],
            'icon' => $cat['icon'],
        ]);
    }

    function deleteCategory($id){
        $db = $this->db();
        $stmt = $db->prepare("DELETE FROM menu_categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}