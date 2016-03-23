<?php
  session_start();
  
  include('db.php');
  include('header.php');
  if(isset($_SESSION['key']))
  {
      header("Location: /stuff-sharing/welcome.php");
  }
  elseif (isset($_GET['error'])) {
    if ($_GET['error'] == "NOT_LOGIN")
    {
        $message = "Please login before proceed";
    }
  }
  elseif(isset($_POST['action']))
  {          
      if($_POST['action']=="login")
      {
          $email = pg_escape_string($connection,$_POST['email']);
          $password = pg_escape_string($connection,$_POST['password']);
          $query = "SELECT email, isadmin FROM users where email='".$email."' and password='".md5($password)."'";
          $result = pg_query($connection,$query);
          $numResults = pg_num_rows($result);
			$row = pg_fetch_row($result);
			
          if ($numResults < 1)
          {
              $message = "Wrong email or wrong password!";
          }
          else
          {		
		  echo $row[0];
		  echo $row[1];
			if($row[1] == 'f')
			{
				header("Location: /stuff-sharing/welcome.php");
				$_SESSION['key'] = $email;
			}
			else if($row[1] == 't')
			{				
				header("Location: /stuff-sharing/admin.php");
				$_SESSION['key'] = $email;
			}
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
          <form action="login.php" method="post">
            <fieldset>
              <div class="form-group">
                <input class="form-control" id="email" name="email" type="text" placeholder="Email" autofocus>
              </div>
              <div class="form-group">
                <input class="form-control" id="password" name="password" type="password" placeholder="Password">
              </div>
              <div class="form-group">
                <input class="form-control" name="action" type="hidden" value="login" />
              </div>
              <button type="submit" class="btn btn-success btn-block">Login</button>
              <p>New Member? <a href="/stuff-sharing/signup.php">Sign up</a></p>
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>