<?php
require_once __DIR__. "/../Model/DashboardModel.php";
require_once __DIR__. "/../Model/LoginModel.php";
require_once __DIR__. "/../View/DashboardView.php";
require_once __DIR__. "/authentification.php";


Class DashboardController extends Authentification{
  function displayDashboard(){
      $this->requireLogin();

      $model= new DashboardModel();
      /*Card data*/
      $totalTab=$model->countTables();
      $bookedTab=$model->countBookedTablesToday();
      if($totalTab){
        $tablesPerc = round($bookedTab * 100 / $totalTab, 2);
    
      }
      else{
        $tablesPerc=0;
      }

  
      $cards=[
        'count_reservations_today'=>$model->countReservationsToday(),
        'count_reservations_week'=>$model->countReservationsThisWeek(),
        'count_pending_reservations'=>$model->countPendingReservations(),
        'count_overdue'=>$model->countOverdueTables(),
        'tables_perc'=>$tablesPerc
      ];

      $up_reservations=$model->getUpcomingReservations();
      $rec_contacts=$model->getRecentContactMessages();

     if (isset($_GET['format']) && $_GET['format'] === 'json'){
        header('Content-Type: application/json');
        echo json_encode([
          'cards'=>$cards,
          'up_reservations'=>$up_reservations,
          'rec_contacts'=>$rec_contacts
        ]
        );
         exit;
      };


      $view= new DashboardView();
      $modelL= new LoginModel();
      $mark=$modelL->getMark();
      $view->displayDashboardView($mark,$cards,$up_reservations,$rec_contacts);
  }


}
?>