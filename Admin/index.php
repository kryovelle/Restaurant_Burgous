<?php
ini_set('session.gc_maxlifetime', 14400); // 4 hours
session_set_cookie_params(14400);
session_start();
require_once __DIR__ . "/Controller/LoginController.php";
require_once __DIR__ . "/Controller/DashboardController.php";
require_once __DIR__ . "/Controller/MenuController.php";
require_once __DIR__ . "/Controller/ReservationsController.php";
require_once __DIR__ . "/Controller/ParametersController.php";
require_once __DIR__ . "/Controller/ContactController.php";
require_once __DIR__ . "/Controller/ReportController.php";
$request = $_SERVER['REQUEST_URI'];

$request = explode('?', $request)[0];


switch ($request) {
  case '/Admin/':
  case '/Admin/Login/':
    $controller=new LoginController();
    $controller->displayLogin();
  break;
  
  case '/Admin/Dashboard/':
    $controller= new DashboardController();
    $controller->displayDashboard();
  break;

  case '/Admin/Menu/':
    $controller = new MenuController();
    $controller->displayMenu();
    break;
  


case '/Admin/Menu/getItem':
    $controller = new MenuController();
    $controller->getItem($_GET['id']);
    break;

case '/Admin/Menu/getCategory':
    $controller = new MenuController();
    $controller->getCategory($_GET['id']);
    break;

case '/Admin/Reservations/':
    $controller= new ReservationsController();
    $controller->displayReservations();
    break;

case '/Admin/Reservations/changeStatus':

  $id=$_GET['id'];
  $status=$_GET['status'];

  $controller= new ReservationsController();
  $controller->changeStatusById($id,$status);
    break;

case '/Admin/Reservations/viewReservation':

  $phone=$_GET['phone'];
  $phone=trim($phone);
  $email=$_GET['email'];
  $controller= new ReservationsController();
  $controller->viewReservation($phone,$email);
   break;

  case '/Admin/Reservations/confirmReservation/':

    $id=$_GET['id'];
    $controller= new ReservationsController();
    $controller->handleConfirmation($id);
   break;

   case '/Admin/Reservations/proposeNewTime':
    $controller = new ReservationsController();
    $controller->proposeNewTime($_GET['id']);
    break;

    case '/Admin/Reservations/getAvailableTableForTime':
    $controller = new ReservationsController();
    $controller->getAvailableTableForTime($_GET['id'], $_GET['time']);
    break;

    case '/Admin/Parameters/':
    $controller = new ParametersController();
    $controller->displayParameters();
    break;

    case '/Admin/Contact/':
    $controller = new ContactController();
    $controller->displayContactMessages();
    break;

   case '/Admin/Contact/getMessage':
    $controller = new ContactController();
    $controller->getMessage($_GET['id']);
    break;

    case '/Admin/Report/':
    $controller = new ReportController();
    $controller->displayReport();
    break;

    case '/Admin/ForgotPassword/':
    $controller = new LoginController();
    $controller->displayForgotPassword();
    break;

   case '/Admin/ResetPassword/':
    $controller = new LoginController();
    $controller->displayResetPassword($_GET['token'] ?? '');
    break;

  default:
     $controller=new LoginController();
    $controller->displayLogin();
    break;
}
?>