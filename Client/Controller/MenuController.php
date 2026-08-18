<?php

require_once __DIR__ ."/../View/MenuView.php";
require_once __DIR__. "/../Model/MenuModel.php";
require_once __DIR__. "/../Model/HomePageModel.php";
require_once __DIR__. "/../Controller/HomePageController.php";

Class MenuController{
  function displayMenu(){
    $modelH= new HomePageModel();
    $contact_details= $modelH->getContactDetails();

    $controllerH= new HomePageController();
    $opening_hours=$controllerH->organizeDaysByTime($modelH->getOpeningHours());

    $model=new MenuModel();
    $categories= $model->getMenuCategories();
    $items=$model->getMenuItems();
 

    $view= new MenuView();
    $view->displayMenuView($contact_details,$opening_hours,$categories,$items
    );

  }
}


?>
