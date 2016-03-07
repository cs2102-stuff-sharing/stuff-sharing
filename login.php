<?php
  session_start();
  include('db.php');
  if(isset($_POST['action']))
  {          
      if($_POST['action']=="login")
      {
          $email = pg_escape_string($connection,$_POST['email']);
          $password = pg_escape_string($connection,$_POST['password']);
          $query = "SELECT email FROM users where email='".$email."' and password='".md5($password)."'";
          $result = pg_query($connection,$query);
          $numResults = pg_num_rows($result);
          if ($numResults < 1)
          {
              $message = "wrong email or wrong password!";
          }
          else
          {
			  header("Location: /stuff-sharing/welcome.php");
			  $_SESSION['key'] = $email;
          }
      }
  }
?>
<?php 
  if(isset($message))
  {
    echo $message;
  }
?>


<p>This is the login page</p>

<div id="tabs-2">
  <form action="login.php" method="post">
  <p><input id="email" name="email" type="text" placeholder="Email"></p>
  <p><input id="password" name="password" type="password" placeholder="Password">
  <input name="action" type="hidden" value="login" /></p>
  <p><input type="submit" value="login" /></p>
</form>
</div>
