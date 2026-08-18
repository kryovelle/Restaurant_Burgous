<?php
require_once __DIR__ . "/../Model/ReservationsModel.php";
require_once __DIR__ . "/../View/ReservationsView.php";
require_once __DIR__ . "/../Model/LoginModel.php";
 require_once __DIR__. "/authentification.php"; 

class ReservationsController  extends Authentification{

function displayReservations(){
  $this->requireLogin();
  $modelL= new LoginModel();
  $mark= $modelL->getMark();

  $model= new ReservationsModel();
  $reservations=$model->getReservations();
  $tables=$model->getTables();

  if(isset($_GET['format'] ) && $_GET['format']=='json'){
    header('Content-Type: application/json');
    echo json_encode([
      'reservations'=>$reservations,
      'tables'=>$tables
    ]);
    exit;
  }

  $view = new ReservationsView();
  $view->displayReservationsView($mark,$reservations,$tables);
}

function changeStatusById($id,$status){
    $this->requireLogin();
  $model= new ReservationsModel();
  $model->changeStatusById($id,$status);
  if($status=='completed'){
    $model->markCompleted($id);
  };
   header("Location: /Admin/Reservations/");
  exit;
}

function viewReservation($phone,$email){
    $this->requireLogin();

$model= new ReservationsModel();
$historyReservations=$model->getReservationsHistory($phone,$email);
$client=[
  'first_name'=>$historyReservations[0]['first_name'],
  'last_name'=>$historyReservations[0]['last_name'],
  'phone'=>$phone,
  'email'=>$email
];
$historyCount = [
    'confirmed' => $model->countReservationsStatus($phone, $email, 'confirmed'),
    'completed' => $model->countReservationsStatus($phone, $email, 'completed'),
    'cancelled' => $model->countReservationsStatus($phone, $email, 'cancelled'),
    'declined'  => $model->countReservationsStatus($phone, $email, 'declined'),
    'overdue'    => $model->countOverdue($phone, $email)
];
 header('Content-Type: application/json');
echo json_encode(
  [
    'historyReservations'=>$historyReservations,
    'client'=>$client,
    'historyCount'=>$historyCount
  ]
);
exit();
}

function handleConfirmation($id){
    $this->requireLogin();
$id= (int)($id);
  $model= new ReservationsModel();
  $reservation=$model->getReservationById($id);

  $buffer_minutes=$model->getSlotSettings()['buffer_minutes'];

  $table_number=$model->findAvailableTable($reservation,$buffer_minutes);

  if($table_number){
    $model->confirmReservation($id,$table_number);
  }
  header('Content-Type: application/json');
  echo json_encode(
    [
      'table_number'=>$table_number,
      'reservation'=>$reservation
    ]
  );
  exit;
}

function timeToMinutes($timeStr){
    $this->requireLogin();
    list($h, $m, $s) = array_map('intval', explode(':', $timeStr));
    return ($h * 60) + $m;
}

function proposeNewTime($id){
    $this->requireLogin();
    $id = (int) $id;
    $model = new ReservationsModel();
    $reservation = $model->getReservationById($id);

    $dayOfWeek = date('l', strtotime($reservation['date']));
    $openingHours = $model->getOpeningHoursByDay($dayOfWeek);
    $slotSettings = $model->getSlotSettings();
    $interval = $this->timeToMinutes($slotSettings['slot_interval']);

    $timeSlots = [];

    if ($openingHours && !$openingHours['is_closed'] && $interval > 0){
        $current = $this->timeToMinutes($openingHours['open_time']);
        $close = $this->timeToMinutes($openingHours['close_time']);

        while ($current < $close){
            $h = str_pad(intdiv($current, 60), 2, '0', STR_PAD_LEFT);
            $m = str_pad($current % 60, 2, '0', STR_PAD_LEFT);
            $timeSlots[] = "$h:$m:00";
            $current += $interval;
        }
    }

    header('Content-Type: application/json');
    echo json_encode([
        'time_slots' => $timeSlots,
        'reservation' => $reservation,
    ]);
    exit;
}

function getAvailableTableForTime($id, $time){
    $this->requireLogin();
    $id = (int) $id;
    $model = new ReservationsModel();
    $reservation = $model->getReservationById($id);

    $duration_minutes = $model->getExpectedDuration($reservation['guests']);
    $openingHours = $model->getOpeningHoursByDay(date('l', strtotime($reservation['date'])));

    $start = strtotime($time);
    $computedEnd = strtotime("+{$duration_minutes} minutes", $start);
    $close = strtotime($openingHours['close_time']);
    $expected_end_time = date('H:i:s', min($computedEnd, $close));

    $buffer_minutes = $model->getSlotSettings()['buffer_minutes'];

    $tempReservation = [
        'date' => $reservation['date'],
        'time_slot' => $time,
        'expected_end_time' => $expected_end_time,
        'guests' => $reservation['guests'],
    ];

    $table_number = $model->findAvailableTable($tempReservation, $buffer_minutes);

    header('Content-Type: application/json');
    echo json_encode([
        'table_number' => $table_number,
        'time_slot' => $time,
        'expected_end_time' => $expected_end_time,
    ]);
    exit;
}

function handleProposeTime($id, $time_slot, $table_number){
    $this->requireLogin();
    $id = (int) $id;
    $table_number = (int) $table_number;

    $model = new ReservationsModel();
    $reservation = $model->getReservationById($id);

    $duration_minutes = $model->getExpectedDuration($reservation['guests']);
    $openingHours = $model->getOpeningHoursByDay(date('l', strtotime($reservation['date'])));

    $start = strtotime($time_slot);
    $computedEnd = strtotime("+{$duration_minutes} minutes", $start);
    $close = strtotime($openingHours['close_time']);
    $expected_end_time = date('H:i:s', min($computedEnd, $close));

    $buffer_minutes = $model->getSlotSettings()['buffer_minutes'];

    $tempReservation = [
        'date' => $reservation['date'],
        'time_slot' => $time_slot,
        'expected_end_time' => $expected_end_time,
        'guests' => $reservation['guests'],
    ];

    $stillAvailable = $model->findAvailableTable($tempReservation, $buffer_minutes);

    if (!$stillAvailable){
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'That table was just taken — please pick a time faster.',
        ]);
        exit;
    }

   $model->proposeTime($id, $time_slot, $expected_end_time, $stillAvailable);

      // Send email notifying the customer of the proposed new time
      $formattedDate = date('F j, Y', strtotime($reservation['date']));
      $formattedTime = date('H:i', strtotime($time_slot));

      $subject = "New Time Proposed for Your Reservation";
      $message = "
          <p>Hi {$reservation['first_name']},</p>
          <p>We couldn't confirm your original request, but we'd like to propose a new time for your reservation:</p>
          <p><strong>{$formattedDate} at {$formattedTime}</strong> — Party of {$reservation['guests']}</p>
          <p>Please reply to this email or contact us to confirm this new time works for you.</p>
          <p>Thank you,<br>Restaurant Burgous</p>
      ";

      $this->sendEmail($reservation['email'], $subject, $message);

      header('Content-Type: application/json');
      echo json_encode(['success' => true]);
      exit;
}

private function sendEmail($recipient, $subject, $message)
{
    // Include the email library files
  require __DIR__ . '/../../PHPMailer-master/src/PHPMailer.php';
   require __DIR__ . '/../../PHPMailer-master/src/SMTP.php';
   require __DIR__ . '/../../PHPMailer-master/src/Exception.php';

    // Create a new email object
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    
    // Email server settings (ask your hosting provider for these)
    $mail->isSMTP();
    $mail->Host       = 'mail.burgous.com';  // Your email server
    $mail->Username   = 'kryovelle@gmail.com';       // Your email address
    $mail->Password   = 'xxxxx';         // Your email password
    $mail->SMTPSecure = 'ssl';                      // Security type
    $mail->Port       = 465;                        // Connection port
    
    // Email content settings
    $mail->CharSet    = 'UTF-8';                    // Allow special characters
    $mail->setFrom('kryovelle@gmail.com', 'Restaurant Burgous'); // Who it's from
    $mail->addAddress($recipient);                  // Who it's going to
   
     // Email subject and body
    $mail->Subject = $subject;
    $mail->isHTML(true);                            // Allow HTML formatting
    $mail->Body    = $message;                      // The email content

    // Send the email
    if (!$mail->send()) {
        error_log("Failed to send email: " . $mail->ErrorInfo);
    }
}


}?>