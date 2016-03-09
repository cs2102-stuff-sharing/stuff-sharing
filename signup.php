<?php
  include('db.php');
  if(isset($_SESSION['key']))
  {
      header("Location: /stuff-sharing/welcome.php");
  }
  elseif(isset($_POST['action']) && $_POST['action'] == "signup")
  {
    $firstName = pg_escape_string($connection,$_POST['firstName']);
    $lastName = pg_escape_string($connection,$_POST['lastName']);
    $dob = pg_escape_string($connection,$_POST['dob']);
    $email = pg_escape_string($connection,$_POST['email']);
    $password = pg_escape_string($connection,$_POST['password']);
    
    $query = "SELECT email FROM users where email='".$email."'";
    $result = pg_query($connection,$query);
    $numResults = pg_num_rows($result);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $message = "Please provide a valid email";
    }
    elseif ($numResults >= 1)
    {
        $message = "The email(" . $email . ") you requested is already taken";
    }
    else
    {
        $signUpQuery = "insert into users( firstName, lastName, dob, email, password) values('"
          . $firstName . "','" .$lastName ."','" . $dob . "','" . $email . "','" . md5($password) . "')";
        $signUpResult = pg_query($connection, $signUpQuery);
        
        if($signUpResult)
        {
            //sign up successfully
            header("Location: /stuff-sharing/welcome.php");
            $_SESSION['key'] = $email;
        }
        else
        {
            //something is wrong with the sign up
        }
    }
  }
  
  if (isset($message))
  {
      echo $message;
  }
?>

<div id="tabs-2">
  <form action="signup.php" method="post">
  <p><input id="first-name" name="firstName" type="text" placeholder="First Name"></p>
  <p><input id="last-name" name="lastName" type="text" placeholder="Last Name"></p>
  <p><input id="dob" name="dob" type="date"></p>
  <p><input id="email" name="email" type="text" placeholder="Email"></p>
  <p><input id="password" name="password" type="password" placeholder="Password">
  <input name="action" type="hidden" value="signup" /></p>
  <p><input type="submit" value="Signup" /></p>
</form>
</div>

<a href = "/stuff-sharing/login.php">Already have an account? Click here to login</a>