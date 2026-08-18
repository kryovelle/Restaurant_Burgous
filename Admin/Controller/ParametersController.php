<?php
require_once __DIR__ . "/../Model/ParametersModel.php";
require_once __DIR__ . "/../Model/LoginModel.php";
require_once __DIR__ . "/../View/ParametersView.php";
 require_once __DIR__. "/authentification.php"; 
class ParametersController extends Authentification {

    function displayParameters(){ 
         $this->requireLogin();
        $modelL = new LoginModel();
        $mark = $modelL->getMark();

        $model = new ParametersModel();
        $tables = $model->getTables();
        $slotSettings = $model->getSlotSettings();
        $durationSettings = $model->getDurationSettings();
        $openingHours = $model->getOpeningHours();

        $view = new ParametersView();
        $view->displayParametersView($mark, $tables, $slotSettings, $durationSettings, $openingHours);
    }

    function handleSaveTables($tables, $newTable){
         $this->requireLogin();
        $model = new ParametersModel();

        foreach ($tables as $id => $seats){
            $model->updateTableSeats((int) $id, (int) $seats);
        }

        if (!empty($newTable['seats'])){
            $nextNumber = $model->countTables() + 1;
            $model->insertTable($nextNumber, (int) $newTable['seats']);
        }
    }

    function handleSaveCapacityDuration($slot, $durations){
         $this->requireLogin();
        $model = new ParametersModel();

        $model->updateSlotSettings(
            $slot['id'],
            $slot['slot_interval'],
            $slot['max_reservations'],
            $slot['buffer_minutes']
        );

        foreach ($durations as $id => $minutes){
            $model->updateDurationSetting((int) $id, (int) $minutes);
        }
    }

   function handleSaveHours($hours){
     $this->requireLogin();
    $model = new ParametersModel();
    foreach ($hours as $id => $data){
        $model->updateOpeningHour(
            (int) $id,
            (int) $data['is_closed'],
            $data['open_time'],
            $data['close_time']
        );
    }
}
}