<?php
  session_start();
  include('db.php');
  include('header.php');
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
        $message = "The email(" . $email . ") has already been taken";
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
            $message = "Something seems to be wrong, please try later";
        }
    }
  }
?>

<div class="container">
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Welcome to Stuff-Sharing</h3>
        </div>
        <div class="panel-body">
          <?php
            if(isset($message))
            {
              echo '<div class="alert alert-danger" role="alert">
                      <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                      <span class="sr-only">Error:</span>' . 
                      $message .
                    '</div>';
            }
          ?>
          <form action="signup.php" method="post">
            <fieldset>
              <div class="form-group">
                <input class="form-control" id="first-name" name="firstName" type="text" placeholder="First Name" autofocus>
              </div>
              <div class="form-group">
                <input class="form-control" id="last-name" name="lastName" type="text" placeholder="Last Name">
              </div>
              <div class="form-group">
                <input class="form-control" id="dob" name="dob" type="date">
              </div>
              <div class="form-group">
                <input class="form-control" id="email" name="email" type="text" placeholder="Email">
              </div>
              <div class="form-group">
                <input class="form-control" id="password" name="password" type="password" placeholder="Password">
              </div>
              <div class="form-group">
                <input name="action" type="hidden" value="signup" /></p>
              </div>
              <button type="submit" class="btn btn-success btn-block">Sign up</button>
              <p>Already have an account? <a href="/stuff-sharing/login.php">Log in</a></p>
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>