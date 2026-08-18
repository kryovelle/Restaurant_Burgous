<?php
require_once __DIR__ . "/../Model/MenuModel.php";
require_once __DIR__ . "/../View/MenuView.php";
require_once __DIR__ . "/../Model/LoginModel.php";
require_once __DIR__. "/authentification.php";

class MenuController extends Authentification{

    function displayMenu(){
        $this->requireLogin();
        $model = new MenuModel();
        $items = $model->getItems();
        $categories = $model->getCategories();

        $modelL= new LoginModel();
        $mark= $modelL->getMark();
        $view = new MenuView();
        $view->displayMenuView($items, $categories,$mark);
    }

    function getItem($id){
         $this->requireLogin();
        $model = new MenuModel();
        $data = $model->getItemById($id);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function getCategory($id){
         $this->requireLogin();
        $model = new MenuModel();
        $data = $model->getCategoryById($id);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function handleAddItem($data){
         $this->requireLogin();
        $model = new MenuModel();
        $model->insertItem($data);
    }

    function handleEditItem($data){
         $this->requireLogin();
        $model = new MenuModel();
        $model->updateItem($data);
    }

    function handleDeleteItem($id){
         $this->requireLogin();
        $model = new MenuModel();
        $model->deleteItem($id);
    }

    function handleAddCategory($data){
         $this->requireLogin();
        $model = new MenuModel();
        $model->insertCategory($data);
    }

    function handleEditCategory($data){
         $this->requireLogin();
        $model = new MenuModel();
        $model->updateCategory($data);
    }

    function handleDeleteCategory($id){
         $this->requireLogin();
        $model = new MenuModel();
        $model->deleteCategory($id);
    }
}