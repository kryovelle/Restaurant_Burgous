<?php
require_once __DIR__ . "/../Model/ReportModel.php";
require_once __DIR__ . "/../Model/LoginModel.php";
require_once __DIR__ . "/../View/ReportView.php";
 require_once __DIR__. "/authentification.php"; 

class ReportController extends Authentification {

function displayReport(){
   $this->requireLogin();
    $modelL = new LoginModel();
    $mark = $modelL->getMark();

    $from = $_GET['from'] ?? date('Y-m-d', strtotime('-6 days'));
    $to = $_GET['to'] ?? date('Y-m-d');

    $model = new ReportModel();
    $summary = $model->getSummary($from, $to);
    $daily = $this->fillDailyGaps($model->getDailyBreakdown($from, $to), $from, $to);
    $hourly = $this->fillHourlyGaps($model->getHourlyBreakdown($from, $to));


    if (isset($_GET['format']) && $_GET['format'] === 'json'){
        header('Content-Type: application/json');
        echo json_encode([
            'summary' => $summary,
            'daily' => $daily,
            'hourly' => $hourly,
        ]);
        exit;
    }

    $view = new ReportView();
    $view->displayReportView($mark, $from, $to, $summary, $daily, $hourly);
}

private function fillDailyGaps($dailyData, $from, $to){
   $this->requireLogin();
    $byDate = [];
    foreach ($dailyData as $row){
        $byDate[$row['date']] = $row;
    }

    $result = [];
    $current = strtotime($from);
    $end = strtotime($to);

    while ($current <= $end){
        $dateStr = date('Y-m-d', $current);
        if (isset($byDate[$dateStr])){
            $result[] = $byDate[$dateStr];
        } else {
            $result[] = ['date' => $dateStr, 'confirmed' => 0, 'completed' => 0, 'overdue' => 0];
        }
        $current = strtotime('+1 day', $current);
    }

    return $result;
}

private function fillHourlyGaps($hourlyData){
   $this->requireLogin();
    $byHour = [];
    foreach ($hourlyData as $row){
        $byHour[(int) $row['hour']] = $row;
    }

    $result = [];
    for ($h = 0; $h < 24; $h++){
        if (isset($byHour[$h])){
            $result[] = $byHour[$h];
        } else {
            $result[] = ['hour' => $h, 'confirmed' => 0, 'completed' => 0, 'seated' => 0, 'overdue' => 0];
        }
    }
    return $result;
}
}