<?php
require_once __DIR__ . "/connexion.php";
Class ContactModel extends Connexion{
  function insertContactMsg($user){
  $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "insert into contact_messages(name,email,phone_number,message) values (:name,:email,:phone_number,:message)";

    $stmt = $dataBase->prepare($query);
    $stmt->execute([
      'name'=>$user['name'],
      'email'=>$user['email'],
      'phone_number'=>$user['phone_number'],
      'message'=>$user['message']
      ]
    );
  }


}
?>