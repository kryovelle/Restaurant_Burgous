<?php
require_once __DIR__ . "/../Model/ReservationsModel.php";

class CronController {

    function autoCancelStale(){
        $model = new ReservationsModel();
        $stale = $model->getStaleReservations();

        foreach ($stale as $reservation){
            $model->autoCancelReservation($reservation['id']);
        }

        return count($stale);
    }
}