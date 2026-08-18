<?php
require_once __DIR__ . '/connexion.php';

Class LoginModel extends Connexion{

 private function db(){
        $db = $this->connecterBDD($this->name, $this->host, $this->user, $this->password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $db;
    }
  function getAdmin(){
     $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT * FROM admin";

    $stmt = $dataBase->prepare($query);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
   return $result;

  }

  function getAdminByEmail($email){
    $db = $this->db();
    $stmt = $db->prepare("SELECT * FROM admin WHERE email = :email");
    $stmt->execute(['email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function setResetToken($email, $token, $expires){
    $db = $this->db();
    $stmt = $db->prepare("UPDATE admin SET reset_token = :token, reset_token_expires = :expires WHERE email = :email");
    $stmt->execute(['token' => $token, 'expires' => $expires, 'email' => $email]);
}

function getAdminByResetToken($token){
    $db = $this->db();
    $stmt = $db->prepare("SELECT * FROM admin WHERE reset_token = :token AND reset_token_expires > NOW()");
    $stmt->execute(['token' => $token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updatePassword($id, $hashedPassword){
    $db = $this->db();
    $stmt = $db->prepare("UPDATE admin SET password = :password, reset_token = NULL, reset_token_expires = NULL WHERE id = :id");
    $stmt->execute(['password' => $hashedPassword, 'id' => $id]);
}

  function getMark(){
    $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT mark FROM contact_details";

    $stmt = $dataBase->prepare($query);
    $stmt->execute();

   $result = $stmt->fetchColumn();
return $result;
  }
}
?>