<?php
  session_start();

  include('db.php');
  include('header.php');
  if(!isset($_SESSION['key']))
  {
  	header("Location: /stuff-sharing/login.php?error=NOT_LOGIN");
  }
  else
  {
	$email = pg_escape_string($connection,$_SESSION['key']);
    $query = "SELECT firstname, lastname, userpoint FROM users where email='".$email."'";
    $result = pg_query($connection,$query) or die('Query failed:'.pg_last_error());
    $row = pg_fetch_row($result);
  }
	
	if(isset($_SESSION['updateid']))
	{
		$updateid = pg_escape_string($connection,$_SESSION['updateid']);
		$updateResult = pg_query($connection,"SELECT u.firstname, u.lastname, u.userpoint, u.dob, u.email 
		FROM users u WHERE u.email='".$email."'");
		if(!$updateResult)
		{
		$row = pg_fetch_row($updateResult);			
			$message = "Something seems to be wrong, please try later ";
		}
		else
		{
			$row = pg_fetch_row($updateResult);
			$userFName = $row[0];
			$userLName = $row[1];
			$userDOB = $row[3];			
			$userEmail = $row[4];
		}
	}

  if(isset($_POST['action']) && $_POST['action'] == "updateid")
  {  
  	$newFName = pg_escape_string($connection,$_POST['userFName']);
		$newLName = pg_escape_string($connection,$_POST['userLName']);
		if (!empty($_POST['userPassword']))
		{
			$newPassword = pg_escape_string($connection,$_POST['userPassword']);
		}
		$newDOB = pg_escape_string($connection,$_POST['userDOB']);
	
		if(isset($newPassword))
		{
			$updateIDQuery = "UPDATE users SET firstname='".$newFName."', lastname='".$newLName."', password='".md5($newPassword)."', dob='".$newDOB."'
			WHERE email = '".$userEmail."'";
		}
		else
		{
			$updateIDQuery = "UPDATE users SET firstname='".$newFName."', lastname='".$newLName."', dob='".$newDOB."'
			WHERE email = '".$userEmail."'";			
		}
	  $updateIDResult = pg_query($connection, $updateIDQuery);

		if($updateIDResult){
			echo ("
			<div class=\"alert alert-info\">
				<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>
				particulars updated!
			</div>
					");
		
		}
				
		//header("Refresh: 10; Location: /stuff-sharing/updateParticular.php");
		//header(Location: /stuff-sharing/updateParticular.php");
  }

?>

<body>
    <?php
      include('navbar.php');
    ?>

<div class="container">
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Update Particulars Page</h3>
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
          <form action="updateParticular.php" method="post">
            <fieldset>
              <div class="form-group">
                <input class="form-control" id="user-fname" name="userFName" type="text" placeholder="User FName" value = "<?php echo $userFName;?>" autofocus>
              </div>
              <div class="form-group">
                <input class="form-control" id="user-lname" name="userLName" type="text" placeholder="User LName" value = "<?php echo $userLName;?>">
              </div>
							<div class="form-group">
                <input class="form-control" id="user-password" name="userPassword" type="text" placeholder="New Password">
              </div>
							<div class="form-group">
                <input class="form-control" id="user-dob" name="userDOB" type="text" placeholder="User DOB" value = "<?php echo $userDOB;?>">
              </div>
			                
              <div class="form-group">
                <input class="form-control" name="action" type="hidden" value="updateid" />
              </div>
              <button type="submit" class="btn btn-success btn-block">Update</button>

            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>