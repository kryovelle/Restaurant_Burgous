<?php
require_once __DIR__ . "/connexion.php";

class ContactModel extends Connexion {

    private function db(){
        $db = $this->connecterBDD($this->name, $this->host, $this->user, $this->password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $db;
    }

    function insertContactMsg($data){
        $db = $this->db();
        $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone_number, message)
                               VALUES (:name, :email, :phone_number, :message)");
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'message' => $data['message'],
        ]);
    }

    function getMessages(){
        $db = $this->db();
        $stmt = $db->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getMessageById($id){
        $db = $this->db();
        $stmt = $db->prepare("SELECT * FROM contact_messages WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function markAsRead($id){
        $db = $this->db();
        $stmt = $db->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    function deleteMessage($id){
        $db = $this->db();
        $stmt = $db->prepare("DELETE FROM contact_messages WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    function countUnread(){
        $db = $this->db();
        $stmt = $db->prepare("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}