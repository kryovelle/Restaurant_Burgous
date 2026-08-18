<?php
session_start();
require_once __DIR__ . "/Controller/LoginController.php";
require_once __DIR__ . "/Controller/ReservationsController.php";
require_once __DIR__ . "/Controller/ParametersController.php";
require_once __DIR__ . "/Controller/ContactController.php";
require_once __DIR__ . "/Controller/MenuController.php";


if(isset($_POST['LoginForm'])){

  $user=[
    'email'=>$_POST['email'],
    'password'=>$_POST['password']
  ];
  
  $controller= new LoginController();
  $controller->handleLogin($user);
  exit;
}




/* ============================================================
   RESERVATION FORM
   ============================================================ */
if (isset($_POST['reserveForm'])){
    $reservationForm = [
        'first_name' => $_POST['first_name'],
        'last_name'  => $_POST['last_name'],
        'email'      => $_POST['email'],
        'phone'      => $_POST['phone'],
        'date'       => $_POST['date'],
        'time_slot'  => $_POST['time_slot'],
        'guests'     => $_POST['guests'],
        'note'       => $_POST['note'] ?? '',
    ];

    $controller = new ReservationController();
    $controller->handleReservation($reservationForm);
    exit;
}

/* ============================================================
   CONTACT FORM
   ============================================================ */
if (isset($_POST['contactForm'])){
    $contactForm = [
        'name'         => $_POST['name'],
        'email'        => $_POST['email'],
        'phone_number' => $_POST['phone'],
        'message'      => $_POST['message'],
    ];

    $controller = new ContactController();
    $controller->insertContactMsg($contactForm);

    header("Location: /Client/Contact/?sent=1");
    exit;
}

/* ============================================================
   MENU ITEM — ADD
   ============================================================ */
if (isset($_POST['addMenuItem'])){

$photo_url = null;

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK){
    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('item_') . '.' . $ext;
    $destination = __DIR__ . '/../images/' . $filename; // adjust to your real images folder

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)){
        $photo_url = $filename;
    }
}

    $controller = new MenuController();
    $controller->handleAddItem([
        'name'          => $_POST['name'],
        'description'   => $_POST['description'],
        'price'         => $_POST['price'],
        'category'      => $_POST['category'],
        'photo_url'     =>$photo_url,
        'is_recommended'=>$_POST['is_recommended'],
        'is_available'  => $_POST['is_available'],
    ]);

    header("Location: /Admin/Menu/");
    exit;
}

/* ============================================================
   MENU ITEM — EDIT
   ============================================================ */
if (isset($_POST['editMenuItem'])){
$photo_url = null;

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK){
    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('item_') . '.' . $ext;
    $destination = __DIR__ . '/../images/' . $filename; 

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)){
        $photo_url = $filename;
    }
}

    $controller = new MenuController();
    $controller->handleEditItem([
        'id'            => $_POST['id'],
        'name'          => $_POST['name'],
        'description'   => $_POST['description'],
        'price'         => $_POST['price'],
        'photo_url'     =>$photo_url,
        'category'      => $_POST['category'],
        'is_recommended'=>$_POST['is_recommended'],
        'is_available'  => $_POST['is_available'],
    ]);

    header("Location: /Admin/Menu/");
    exit;
}

/* ============================================================
   MENU ITEM — DELETE
   ============================================================ */
if (isset($_POST['deleteMenuItem'])){
    $controller = new MenuController();
    $controller->handleDeleteItem($_POST['id']);

    header("Location: /Admin/Menu/");
    exit;
}

/* ============================================================
   CATEGORY — ADD
   ============================================================ */
if (isset($_POST['addCategory'])){
    $controller = new MenuController();
    $controller->handleAddCategory([
        'title'       => $_POST['title'],
        'description' => $_POST['description'],
        'icon'        => $_POST['icon'],
    ]);

    header("Location: /Admin/Menu/");
    exit;
}

/* ============================================================
   CATEGORY — EDIT
   ============================================================ */
if (isset($_POST['editCategory'])){
    $controller = new MenuController();
    $controller->handleEditCategory([
        'id'          => $_POST['id'],
        'title'       => $_POST['title'],
        'description' => $_POST['description'],
        'icon'        => $_POST['icon'],
    ]);

    header("Location: /Admin/Menu/");
    exit;
}

/* ============================================================
   CATEGORY — DELETE
   ============================================================ */
if (isset($_POST['deleteCategory'])){
    $controller = new MenuController();
    $controller->handleDeleteCategory($_POST['id']);

    header("Location: /Admin/Menu/");
    exit;
}

/* ============================================================
   RESERVATION — PROPOSE NEW TIME
   ============================================================ */
if (isset($_POST['proposeReservationTime'])){
    $controller = new ReservationsController();
    $controller->handleProposeTime(
        $_POST['id'],
        $_POST['time_slot'],
        $_POST['table_number']
    );
    // handleProposeTime() already outputs JSON and calls exit — nothing more to do here
    exit;
}



if (isset($_POST['saveTables'])){
    $controller = new ParametersController();
    $controller->handleSaveTables($_POST['tables'] ?? [], $_POST['newTable'] ?? []);
    header("Location: /Admin/Parameters/");
    exit;
}

if (isset($_POST['saveCapacityDuration'])){
    $controller = new ParametersController();
    $controller->handleSaveCapacityDuration($_POST['slot'], $_POST['durations'] ?? []);
    header("Location: /Admin/Parameters/");
    exit;
}

if (isset($_POST['saveHours'])){
    $controller = new ParametersController();
    $controller->handleSaveHours($_POST['hours'] ?? []);
    header("Location: /Admin/Parameters/");
    exit;
}



if (isset($_POST['deleteContactMessage'])){
    $controller = new ContactController();
    $controller->handleDelete($_POST['id']);
    header("Location: /Admin/Contact/");
    exit;
}

if (isset($_POST['forgotPassword'])){
    $controller = new LoginController();
    $controller->handleForgotPassword($_POST['email']);
}

if (isset($_POST['resetPassword'])){
    $controller = new LoginController();
    $controller->handleResetPassword(
        $_POST['token'],
        $_POST['password'],
        $_POST['confirmPassword']
    );
}
/* ============================================================
   FALLBACK — no matching action, nothing to do
   ============================================================ */
header("Location: /Admin/");
exit;


?>