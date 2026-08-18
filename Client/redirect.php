<?php
session_start(); 
require_once __DIR__ . "/Controller/ContactController.php";
require_once __DIR__ . "/Controller/ReservationController.php";

if (isset($_POST['contactForm'])) {
    $user = [
        'name'         => $_POST['name'],
        'email'        => $_POST['email'],
        'phone_number' => $_POST['phone'],
        'message'      => $_POST['message'],
    ];

    $controller = new ContactController();
    $controller->insertContactMsg($user);

    header("Location: /Client/Contact/?success=1");
    exit;
}
if(isset($_POST['reserveForm'])){
   $reservation = [
    'first_name' => $_POST['first_name'],
    'last_name' => $_POST['last_name'],
    'email' => $_POST['email'],
    'phone' => $_POST['phone'],
    'date' => $_POST['date'],
    'time_slot' => $_POST['time_slot'],
    'guests' => $_POST['guests'],
    'note' => $_POST['note'] ?? null
];
    $controller= new ReservationController();
    $controller->handleReservation($reservation);
}

exit;