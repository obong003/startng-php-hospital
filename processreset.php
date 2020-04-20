<?php  
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('functions/users.php');
require_once('functions/alert.php');
require_once('functions/redirect.php');
require_once('functions/email.php');

$errorCount = 0;


if(!is_user_loggedIn()){
    $token = $_POST['token'] != "" ? $_POST['token'] : $errorCount++;
    $_SESSION['token']= $token;
}


$email = $_POST['email'] != "" ? $_POST['email'] : $errorCount++;
$password = $_POST['password'] != "" ? $_POST['password'] : $errorCount++;


$_SESSION['email']= $email;

if($errorCount > 0){
    $session_error = "You have " .$errorCount . " error";

    if($errorCount > 1){
        $session_error .= "s";
    }
    $session_error .= "  in your form submission";

    set_alert(error, $session_error);
    redirect_to("reset.php");

}else{

    $checkToken = is_user_loggedIn() ? true : find_token($email);

    if($checkToken){
        $userExists = find_user($email);
        if($userExists){
            $userObject = find_user($email);
            $userObject->password = password_hash($password, PASSWORD_DEFAULT);
            $currentUser = $userObject->email . ".json";
            unlink("db/users/".$currentUser);
            unlink("db/tokens/".$currentUser);
            
            save_user_reset($userObject);

            set_alert('message',"Password Reset Successful, you can now login");

            $subject = "Password Reset Successful";
            $message = "Your account on snh has just been updated, your password has changed. if you did not initiate the password change, please visit snh.org and reset your password immediatly";
            send_mail($subject,$message,$email);
                       
            redirect_to("login.php");
            return;

        }
            

    }
    set_alert('error',"Password Reset Failed, token/email invalid or expired");
    redirect_to("login.php");

}

?>