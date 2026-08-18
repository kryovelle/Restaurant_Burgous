<?php
require_once __DIR__ . "/connexion.php";
Class MenuModel extends Connexion{
  function getMenuCategories(){
      $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT * FROM menu_categories";

    $stmt = $dataBase->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
  function getMenuItems(){
     $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT * FROM menu_items where is_available=true order by category";

    $stmt = $dataBase->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
}
?>