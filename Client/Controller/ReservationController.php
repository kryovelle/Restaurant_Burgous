<?php

require_once __DIR__ ."/../View/ReservationView.php";
require_once __DIR__. "/../Model/ReservationModel.php";
require_once __DIR__. "/../Model/HomePageModel.php";
require_once __DIR__. "/../Controller/HomePageController.php";

Class ReservationController{




  function displayReservation(){
    $modelH= new HomePageModel();
    $contact_details= $modelH->getContactDetails();

    $controllerH= new HomePageController();
    $opening_hours=$modelH->getOpeningHours();
    $opening_hours_org=$controllerH->organizeDaysByTime($opening_hours);

    $model= new ReservationModel();
    $slot= $model->getSlotSettings();



    $view= new ReservationView();
    $view->displayReservationView($contact_details,$opening_hours_org,$opening_hours,$slot);

  }

 function getEndTime($time_slot, $day, $guests){
    $model = new ReservationModel();
    $duration_minutes = $model->getExpectedDuration($guests);
    //echo 'duration'.$duration_minutes . '\n';
    //echo $day;
    $close_time = $model->getCloseTime($day);
 //var_dump($close_time);die();
    // Add duration to the start time
    $start = strtotime($time_slot);
    $computed_end = strtotime("+{$duration_minutes} minutes", $start);

    // Cap it at closing time — whichever comes first
    $close = strtotime($close_time);

    $end = min($computed_end, $close);

    return date('H:i:s', $end);
}

function insertReservation($reservationForm){
    
    $reservationForm['cancel_token'] = bin2hex(random_bytes(16));

    $model = new ReservationModel();
    $model->insertReservation($reservationForm);
    
    // Send confirmation email
    $this->sendConfirmationEmail($reservationForm);
}


function tableIsAvaialable($reservationForm)
{
    //1.check max reservations 
  $model= new ReservationModel();
  $count=$model->countReservationBy($reservationForm['time_slot'],$reservationForm['date']);
  $slot_settings=$model->getSlotSettings();
  $max=$slot_settings['max_reservations'];
  
  if($count>=$max) return false;

  return $model->findAvailableTable($reservationForm,$slot_settings['buffer_minutes']);


}

  function handleReservation($reservationForm){


    /* *********************************/
    $modelH = new HomePageModel();
    $contact_details = $modelH->getContactDetails();
    $controllerH = new HomePageController();
    $opening_hours = $modelH->getOpeningHours();
    $opening_hours_org = $controllerH->organizeDaysByTime($opening_hours);
    $view = new ReservationView();
    /******************************/
    
    $dayOfWeek = date('l', strtotime($reservationForm['date']));

    $expected_end_time = $this->getEndTime(
        $reservationForm['time_slot'],
        $dayOfWeek,
        $reservationForm['guests']
    );

    $reservationForm['expected_end_time'] = $expected_end_time;
    
   
    $table_number=$this->tableIsAvaialable($reservationForm);

    if ($table_number){
        $reservationForm['table_number'] = $table_number;
        $reservationForm['status'] = 'confirmed';

        $this->insertReservation($reservationForm);

        $view->displayConfirmedReservationView($contact_details, $opening_hours_org, $reservationForm);
    } else {
        $reservationForm['table_number'] =NULL;
        $reservationForm['status'] = 'pending';

        $this->insertReservation($reservationForm);
        $view->displayPendingReservationView($contact_details, $opening_hours_org);
    }
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



private function sendConfirmationEmail($reservation){
    $cancelLink = "/Admin/Reservations/cancel?token=" . $reservation['cancel_token'];
    
    $message = "
    <h2>Reservation Confirmed</h2>
    <p>Dear <strong>{$reservation['first_name']} {$reservation['last_name']}</strong>,</p>
    <p>Your reservation has been confirmed.</p>
    
    <p><strong>Date:</strong> {$reservation['date']}</p>
    <p><strong>Time:</strong> {$reservation['time_slot']}</p>
    
    <p>To cancel your reservation, click the link below:</p>
    <p><a href='$cancelLink'>Cancel Reservation</a></p>
    
    <p>Thank you for choosing us!</p>
    ";
    
    $this->sendEmail($reservation['email'], 'Reservation Confirmed', $message);
}

public function cancelReservation(){
    $token = $_GET['token'] ?? '';
    
    if (empty($token)) {
        echo "Invalid cancellation link.";
        return;
    }
    
    $model = new ReservationModel();
    $reservation = $model->getReservationByToken($token);
    
    if (!$reservation) {
        echo "Reservation not found or already cancelled.";
        return;
    }
    
    // Check if reservation can be cancelled
    if (in_array($reservation['status'], ['completed', 'seated', 'declined'])) {
        echo "This reservation cannot be cancelled.";
        return;
    }
    
    // Update status to 'cancelled'
    $model->updateStatus($reservation['id'], 'cancelled');
    
    // Show success message
    echo "<h2>Reservation Cancelled</h2>";
    echo "<p>Your reservation has been successfully cancelled.</p>";
}



}




?>
