
<?php

require_once __DIR__ . "/connexion.php";
class ReservationsModel extends Connexion {

  private function db(){
        $db = $this->connecterBDD($this->name, $this->host, $this->user, $this->password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $db;
    }

function getReservations(){
        $db = $this->db();
        $stmt = $db->prepare("SELECT * FROM reservations ORDER BY date desc , time_slot desc ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTables(){
        $db=$this->db();
        $stmt=$db->prepare("SELECT * FROM tables order by table_number ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function changeStatusById($id,$status){
    $id=(int)($id);
    $db=$this->db();
    $stmt=$db->prepare("UPDATE reservations SET status=:status WHERE id=:id");
    $stmt->execute([      
        'id'=>$id,
        'status'=>$status
    ]);
    
    // Return the number of affected rows instead
    return $stmt->rowCount();
}

function countReservationsStatus($phone, $email, $status){
    $db = $this->db();
    $stmt = $db->prepare("SELECT count(*) FROM reservations
                           WHERE REPLACE(phone, '+', '') = REPLACE(:phone, '+', '')
                             AND email = :email
                             AND status = :status");
    $stmt->execute([
        'phone' => $phone,
        'email' => $email,
        'status' => $status,
    ]);
    return $stmt->fetchColumn();
}
function getReservationsHistory($phone,$email){
     $db=$this->db();
    $stmt=$db->prepare("SELECT * FROM reservations WHERE REPLACE(phone, '+', '') = :phone  and email=:email order by date desc , time_slot desc");
    $stmt->execute([      
        'phone'=>$phone,
        'email'=>$email
    ]);
     return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function countOverdue($phone,$email){
    $db=$this->db();
    $stmt=$db->prepare("SELECT COUNT(*) AS overdue_count
        FROM reservations
        WHERE (phone = :phone OR email = :email)
        AND (
                (status = 'seated' AND NOW() > CONCAT(date, ' ', expected_end_time))
                OR
                (status = 'completed' AND completed_at > CONCAT(date, ' ', expected_end_time))
            )");
    $stmt->execute([      
        'phone'=>$phone,
        'email'=>$email
    ]);
    return $stmt->fetchColumn();
}

function markCompleted($id){
     $db=$this->db();
    $stmt=$db->prepare("UPDATE reservations SET completed_at=NOW() where id=:id");
    $stmt->execute(['id'=>$id]);
    return $stmt->fetchColumn();
}

function getReservationById($id){
    $db = $this->db();
    $stmt = $db->prepare("SELECT * FROM reservations where id=:id");
    $stmt->execute([
        'id'=>$id
    ]);
    return $stmt->fetch();
}

 function getSlotSettings(){
    $db = $this->db();
    $query = "SELECT * FROM slot_settings";

    $stmt = $db->prepare($query);
    $stmt->execute( );
     $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }

function findAvailableTable($reservation, $buffer_minutes){
    $db = $this->db();

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

    $stmt = $db->prepare($query);
    $stmt->execute([
        'guests'          => $guestsNumeric,
        'date'            => $reservation['date'],
        'buffered_start'  => $bufferedStart,
        'buffered_end'    => $bufferedEnd,
    ]);

    $result = $stmt->fetchColumn();

    return $result;
}

function confirmReservation($id,$table_number){

    $db = $this->db();
    $query = "UPDATE reservations set status='confirmed',table_number=:table_number where id=:id";

    $stmt = $db->prepare($query);
    $stmt->execute( [
        'id'=>$id,
        'table_number'=>$table_number
    ]);
     $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

function getOpeningHoursByDay($day_of_week){
    $db = $this->db();
    $stmt = $db->prepare("SELECT * FROM opening_hours WHERE day_of_week = :day");
    $stmt->execute(['day' => $day_of_week]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getExpectedDuration($guests){
    $db = $this->db();
    $stmt = $db->prepare("SELECT expected_duration_minutes FROM duration_buffer_settings WHERE party_size = :guests");
    $stmt->execute(['guests' => $guests]);
    return $stmt->fetchColumn();
}
function proposeTime($id, $time_slot, $expected_end_time, $table_number){
    $db = $this->db();
    $stmt = $db->prepare("UPDATE reservations SET
                            status = 'proposed',
                            time_slot = :time_slot,
                            expected_end_time = :expected_end_time,
                            table_number = :table_number,
                            proposed_at = NOW()
                           WHERE id = :id");
    $stmt->execute([
        'id' => $id,
        'time_slot' => $time_slot,
        'expected_end_time' => $expected_end_time,
        'table_number' => $table_number,
    ]);
}
function autoCancelReservation($id){
    $db = $this->db();
    $stmt = $db->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

function getStaleReservations(){
    $db = $this->db();
    $stmt = $db->prepare("SELECT * FROM reservations
                           WHERE status = 'proposed'
                             AND proposed_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}

