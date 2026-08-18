<?php
require_once __DIR__ . "/connexion.php";
class DashboardModel extends Connexion {

    function countReservationsToday(){
       $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT COUNT(*) FROM reservations where date=current_date()";

    $stmt = $dataBase->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchColumn();
    
   return $result;
     }
    

    function countPendingReservations(){
      $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT COUNT(*) FROM reservations where status='pending'and date=current_date() ";

    $stmt = $dataBase->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchColumn();
    
   return $result;
    }

    function countReservationsThisWeek(){

     $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT COUNT(*) FROM reservations WHERE YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1)";

    $stmt = $dataBase->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchColumn();
    
   return $result;
     }

    function countBookedTablesToday(){
      
     $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT  Count( distinct table_number) from reservations where date=current_date() and status in('confirmed','seated','completed');";

    $stmt = $dataBase->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchColumn();
    
   return $result;
     }
    function countOverdueTables(){ 
        $dataBase = $this->connecterBDD(
        $this->name,
        $this->host,
        $this->user,
        $this->password
      );

        $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $query = "SELECT COUNT(*) FROM reservations where date=current_date() and status='seated' and expected_end_time<current_time();";

        $stmt = $dataBase->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchColumn();
        
        return $result;
    }

    function getUpcomingReservations($hoursAhead = 3,$limit=3){
    $dataBase = $this->connecterBDD(
        $this->name,
        $this->host,
        $this->user,
        $this->password
    );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $query = "SELECT * FROM reservations
               WHERE date = CURDATE()
                 AND time_slot BETWEEN CURTIME() AND ADDTIME(CURTIME(), SEC_TO_TIME(:hoursAhead * 3600))
               ORDER BY time_slot 
               LIMIT :limit";

    $stmt = $dataBase->prepare($query);
    $stmt->execute([
      'hoursAhead' => $hoursAhead ,
      'limit'=>$limit
    ]);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
}

    function getRecentContactMessages($limit = 5){ 
        $dataBase = $this->connecterBDD(
                $this->name,
                $this->host,
                $this->user,
                $this->password
            );

            $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $query = "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT :limit";

            $stmt = $dataBase->prepare($query);
            $stmt->execute([
              'limit'=>$limit
            ]);

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;

    }

    function countTables(){
       $dataBase = $this->connecterBDD(
                $this->name,
                $this->host,
                $this->user,
                $this->password
            );

            $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $query = "SELECT COUNT(*) from tables";

            $stmt = $dataBase->prepare($query);
            $stmt->execute();

            $result = $stmt->fetchColumn();

            return $result;
    }
}
?>