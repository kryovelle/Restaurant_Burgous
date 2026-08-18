<?php
require_once __DIR__ . "/connexion.php";
Class HomePageModel extends Connexion{
  function getContactDetails(){
    $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT * FROM contact_details";

    $stmt = $dataBase->prepare($query);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
   return $result;
  }
  function getOpeningHours(){
     $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT * FROM opening_hours where is_closed=false";

    $stmt = $dataBase->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
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
  function getRecommendedMenuItems(){
     $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT * FROM menu_items where is_available=true and is_recommended=true limit 8";

    $stmt = $dataBase->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

}
?>