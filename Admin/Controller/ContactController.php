<?php
require_once __DIR__ . "/../Model/ContactModel.php";
require_once __DIR__ . "/../Model/LoginModel.php";
require_once __DIR__ . "/../View/ContactView.php";
require_once __DIR__. "/authentification.php";

class ContactController extends Authentification{

    function insertContactMsg($data){
         $this->requireLogin();
        $model = new ContactModel();
        $model->insertContactMsg($data);
    }
function displayContactMessages(){
     $this->requireLogin();
    $modelL = new LoginModel();
    $mark = $modelL->getMark();

    $model = new ContactModel();
    $messages = $model->getMessages();

    if (isset($_GET['format']) && $_GET['format'] === 'json'){
        header('Content-Type: application/json');
        echo json_encode(['messages' => $messages]);
        exit;
    }

    $view = new ContactView();
    $view->displayContactView($mark, $messages);
}

    function getMessage($id){
      $this->requireLogin();
        $model = new ContactModel();
        $message = $model->getMessageById($id);
        $model->markAsRead($id); // Option A — viewing marks it read

        header('Content-Type: application/json');
        echo json_encode($message);
        exit;
    }

    function handleDelete($id){
      $this->requireLogin();
        $model = new ContactModel();
        $model->deleteMessage($id);
    }
}