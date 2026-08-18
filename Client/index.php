<?php

require_once __DIR__ . "/Controller/HomePageController.php";
require_once __DIR__ . "/Controller/MenuController.php";
require_once __DIR__ . "/Controller/ReservationController.php";
require_once __DIR__ . "/Controller/ContactController.php";

$request = $_SERVER['REQUEST_URI'];

$request = explode('?', $request)[0];


switch ($request) {
  case "/":
  case "/Client/":
  case"Restaurant/Client/HomePage/":
   $controller= new HomePageController();
   $controller->displayHomePage();
    break;

  case "/Client/Menu/":
    $controller=new MenuController();
    $controller->displayMenu();

    break;

  case "/Client/Reservation/":
    $controller=new ReservationController();
    $controller->displayReservation();

  break;

  case "/Client/Contact/":
    $controller=new ContactController();
    $controller->displayContact();

  break;

  case "/Admin/Reservations/cancel":
    $controller = new ReservationController();
    $controller->cancelReservation();
    break;

    

}

?>