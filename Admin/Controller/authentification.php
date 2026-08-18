<?php
Class Authentification{
  
  function requireLogin(){
    if (session_status() === PHP_SESSION_NONE) session_start();
     if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
        header('Location: /Admin/Login/');
        exit;
    }
  }



}
?>