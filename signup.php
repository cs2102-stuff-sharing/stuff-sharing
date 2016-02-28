<?php
  include('db.php');
  if(isset($_POST['action']))
  {          
      if($_POST['action']=="signup")
      {
          $firstName = pg_escape_string($connection,$_POST['firstName']);
          $lastName = pg_escape_string($connection,$_POST['lastName']);
          $dob = pg_escape_string($connection,$_POST['dob']);
          $email = pg_escape_string($connection,$_POST['email']);
          $password = pg_escape_string($connection,$_POST['password']);
          $query = "SELECT email FROM users where email='".$email."'";
          $result = pg_query($connection,$query);
          $numResults = pg_num_rows($result);
          if (!filter_var($email, FILTER_VALIDATE_EMAIL)) // Validate email address
          {
              $message =  "Invalid email address please type a valid email!!";
          }
          elseif($numResults>=1)
          {
              $message = $email."Email already exist!!";
          }
          else
          {
              pg_query($connection,"insert into users(firstName,lastName,dob,email,password) values('".$firstName."','".$lastName."','".$dob."','".$email."','".md5($password)."')");
              $message = "Signup Sucessfully!!";
          }
      }
  }
?>
<?php 
  if(isset($message))
  {
    echo $message;
  }
  else
  {
    echo "<p>Sign up here by filling up the form</p>";
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