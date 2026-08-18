<?php
require_once __DIR__ . "/connexion.php";
Class ReservationModel extends Connexion{
    function getSlotSettings(){
     $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT * FROM slot_settings";

    $stmt = $dataBase->prepare($query);
    $stmt->execute( );
     $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }

function insertReservation($reservation){
    $dataBase = $this->connecterBDD(
        $this->name,
        $this->host,
        $this->user,
        $this->password
    );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    $query = "INSERT INTO reservations (
        first_name, 
        last_name, 
        email, 
        phone, 
        date, 
        time_slot, 
        guests, 
        note, 
        expected_end_time, 
        completed_at,
        table_number, 
        status, 
        created_at,
        cancel_token
    ) VALUES (
        :first_name,
        :last_name,
        :email,
        :phone,
        :date,
        :time_slot,
        :guests,
        :note,
        :expected_end_time,
        NULL,
        :table_number,
        :status,
        NOW(),
        :cancel_token
    )";

    $stmt = $dataBase->prepare($query);
    $stmt->execute([
        'first_name' => $reservation['first_name'],
        'last_name' => $reservation['last_name'],
        'email' => $reservation['email'],
        'phone' => $reservation['phone'],
        'date' => $reservation['date'],
        'time_slot' => $reservation['time_slot'],
        'guests' => $reservation['guests'],
        'note' => $reservation['note'] ?? null,
        'expected_end_time' => $reservation['expected_end_time'],
        'table_number' => $reservation['table_number'],
        'status' => $reservation['status'],
        'cancel_token'=>$reservation['cancel_token']
    ]);
    
    // Get the last inserted ID
    $lastId = $dataBase->lastInsertId();
    
    // Return the inserted reservation
    $query2 = "SELECT * FROM reservations WHERE id = :id";
    $stmt2 = $dataBase->prepare($query2);
    $stmt2->execute(['id' => $lastId]);
    $result = $stmt2->fetch(PDO::FETCH_ASSOC);
    
    return $result;
}

function getExpectedDuration($guests){
     $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT expected_duration_minutes FROM duration_buffer_settings where party_size=:guests";

    $stmt = $dataBase->prepare($query);
    $stmt->execute(['guests' => $guests]);
    $result = $stmt->fetchColumn();

    return $result;
}

function getCloseTime($day_of_week){
   $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT close_time from opening_hours where day_of_week=:day_of_week";

    $stmt = $dataBase->prepare($query);
    $stmt->execute(['day_of_week' => $day_of_week]);
    $result = $stmt->fetchColumn(); // returns "18:00:00" directly, not an array

    return $result;
}

function countReservationBy($slot,$date){
     $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $query = "SELECT COUNT(*) FROM reservations WHERE date =:date and time_slot=:time_slot and status in ('confirmed','pending','seated');";

    $stmt = $dataBase->prepare($query);
    $stmt->execute(['date' =>$date,
    'time_slot'=>$slot]);
    $result = $stmt->fetchColumn(); 
    
    return $result;
}

function findAvailableTable($reservation, $buffer_minutes){
    $dataBase = $this->connecterBDD(
        $this->name, $this->host, $this->user, $this->password
    );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $guests = $reservation['guests'];
    $guestsNumeric = ($guests === '6+') ? 6 : (int) $guests;

    // Calculate the actual start and end times with buffer
    $startTime = new DateTime($reservation['date'] . ' ' . $reservation['time_slot']);
    $endTime = new DateTime($reservation['date'] . ' ' . $reservation['expected_end_time']);
    
    // Apply buffer to both start and end
    $bufferedStart = $startTime->modify('-' . $buffer_minutes . ' minutes')->format('H:i:s');
    $bufferedEnd = $endTime->modify('+' . $buffer_minutes . ' minutes')->format('H:i:s');

    $query = "SELECT t.table_number
                FROM tables t
                WHERE t.seats >= :guests
                  AND t.is_active = 1
                  AND t.table_number NOT IN (
                      SELECT DISTINCT r.table_number
                      FROM reservations r
                      WHERE r.date = :date
                        AND r.status IN ('confirmed', 'seated', 'proposed')
                        AND r.table_number IS NOT NULL
                        AND r.time_slot < :buffered_end
                        AND r.expected_end_time > :buffered_start
                  )
                ORDER BY t.seats ASC
                LIMIT 1";

    $stmt = $dataBase->prepare($query);
    $stmt->execute([
        'guests'          => $guestsNumeric,
        'date'            => $reservation['date'],
        'buffered_start'  => $bufferedStart,
        'buffered_end'    => $bufferedEnd,
    ]);

    $result = $stmt->fetchColumn();

    return $result;
}




public function getReservationByToken($token){
      $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $stmt = $dataBase->prepare("SELECT * FROM reservations WHERE cancel_token =:token");
    $stmt->execute(['token'=>$token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function updateStatus($id, $status){
     $dataBase = $this->connecterBDD(
    $this->name,
    $this->host,
    $this->user,
    $this->password
  );

    $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

   $stmt = $dataBase->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $id]);
}

}