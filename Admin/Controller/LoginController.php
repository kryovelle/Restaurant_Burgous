<?php
require_once __DIR__. "/../Model/LoginModel.php";
require_once __DIR__. "/../View/LoginView.php";

Class LoginController {
  function handleLogin($user){

    $model= new LoginModel();
    $admin=$model->getAdmin();
    

    if($user and 
    $user['email']==$admin['email'] and  $user['password']===$admin['password']){

      
      session_regenerate_id(true);
      $_SESSION['admin']=true;
      header('Location: /Admin/Dashboard/');
      exit;

    }

    else{
      header('Location: /Admin/Login?success=0');
      exit;
    }
  }

  function displayLogin(){
    $model= new LoginModel();
    $mark=$model->getMark();
    $view = new LoginView();
    $view->displayLoginView($mark);
  }


  function displayForgotPassword(){
    $view = new LoginView();
    $view->displayForgotPasswordView();
}

function handleForgotPassword($email){
    $model = new LoginModel();
    $admin = $model->getAdminByEmail($email);

    // Always show the same message, whether or not the email exists —
    // prevents attackers from using this form to discover valid admin emails
    if ($admin){
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $model->setResetToken($email, $token, $expires);

        $resetLink = "http://localhost/Admin/ResetPassword/?token={$token}";

        $subject = "Reset Your Password";
        $message = "
            <p>We received a request to reset your password.</p>
            <p><a href=\"{$resetLink}\">Click here to reset your password</a></p>
            <p>This link expires in 1 hour. If you didn't request this, you can ignore this email.</p>
        ";

        $this->sendEmail($email, $subject, $message);
    }

    header("Location: /Admin/ForgotPassword/?sent=1");
    exit;
}

function displayResetPassword($token){
    $model = new LoginModel();
    $admin = $model->getAdminByResetToken($token);

    $view = new LoginView();

    if (!$admin){
        $view->displayResetPasswordInvalidView();
        return;
    }

    $view->displayResetPasswordView($token);
}

function handleResetPassword($token, $password, $confirmPassword){
    $model = new LoginModel();
    $admin = $model->getAdminByResetToken($token);

    if (!$admin){
        header("Location: /Admin/ResetPassword/?error=invalid");
        exit;
    }

    if ($password !== $confirmPassword){
        header("Location: /Admin/ResetPassword/?token={$token}&error=mismatch");
        exit;
    }

    if (strlen($password) < 8){
        header("Location: /Admin/ResetPassword/?token={$token}&error=tooshort");
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $model->updatePassword($admin['id'], $hashed);

    header("Location: /Admin/?reset=success");
    exit;
}

private function sendEmail($recipient, $subject, $message){
    require __DIR__ . '/../../PHPMailer-master/src/PHPMailer.php';
    require __DIR__ . '/../../PHPMailer-master/src/SMTP.php';
    require __DIR__ . '/../../PHPMailer-master/src/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->isSMTP();
    $mail->Host       = 'mail.burgous.com';
    $mail->Username   = 'kryovelle@gmail.com';
    $mail->Password   = 'xxxx';
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom('kryovelle@gmail.com', 'Restaurant Burgous');
    $mail->addAddress($recipient);
    $mail->Subject = $subject;
    $mail->isHTML(true);
    $mail->Body = $message;

    if (!$mail->send()){
        error_log("Failed to send reset email: " . $mail->ErrorInfo);
    }
}
}




?>