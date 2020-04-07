<?php 

session_start();
$errorCount = 0;

$email = $_POST['email'] != "" ? $_POST['email'] : $errorCount++;

$password = $_POST['password'] != "" ? $_POST['password'] : $errorCount++;

$_SESSION['email']= $email;

if($errorCount > 0){
    //redirect back and display error

    $session_error = "You have " . $errorCount ." error";
    if($errorCount > 1){
        $session_error .= "s";

    };

    $session_error .= " in your form submission";
    $_SESSION['error'] = $session_error;
     
   header("Location: login.php");
}else{
    $allUsers = scandir("db/users/");
    $countAllUsers = count($allUsers);
    for ($counter =0; $counter <= $countAllUsers; $counter++){
        $currentUser = $allUsers[$counter];
        if($currentUser == $email. ".json"){
        //Check for password
            $userString = file_get_contents("db/users/".$currentUser);
            $userObject = json_decode($userString);
            $passwordFromDB = $userObject->password;

            $passwordFromUser = password_verify($password, $passwordFromDB);

            if($passwordFromDB == $passwordFromUser){
                //entering dashboard
                $_SESSION['loggedIn']=$userObject->id;
                $_SESSION['fullname']=$userObject->first_name . " " . $userObject->last_name;
                $_SESSION['role']=$userObject->designation;
                header("Location: dashboard.php");
                die();
            }
            
        }    
    }
    $_SESSION['error'] = 'Invalid Email or Password';
    header("Location: login.php");
    die();
}

?>