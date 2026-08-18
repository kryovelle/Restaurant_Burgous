<?php

require_once __DIR__ ."/../View/ContactView.php";
require_once __DIR__. "/../Model/ContactModel.php";
require_once __DIR__. "/../Model/HomePageModel.php";
require_once __DIR__. "/../Controller/HomePageController.php";

Class ContactController{
 
  function displayContact(){
    $modelH= new HomePageModel();
    $contact_details= $modelH->getContactDetails();

    $controllerH= new HomePageController();
    $opening_hours=$modelH->getOpeningHours();
    $opening_hours_org=$controllerH->organizeDaysByTime($opening_hours);

    $view= new ContactView();
    $view->displayContactView($contact_details,$opening_hours_org);

  }
  
  function insertContactMsg($user){
    $model=new ContactModel();
    $model->insertContactMsg($user);
  }
}


?>
