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
    $query = "SELECT firstName, lastName FROM users where email='".$email."'";
    $result = pg_query($connection,$query) or die('Query failed:'.pg_last_error());
    $cRow = pg_fetch_row($result);
  }
	
	if(isset($_SESSION['userid']))
	{
		$userid = pg_escape_string($connection,$_SESSION['userid']);
		$updateResult = pg_query($connection,"SELECT u.firstname, u.lastname, u.dob, u.email, u.userpoint, u.blacklistcount
		FROM users u WHERE u.email='".$userid."'");
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
			$userDOB = $row[2];			
			$userEmail = $row[3];
			$userPoint = $row[4];
			$userBCount = $row[5];
			
		}
	}

  if(isset($_POST['action']) && $_POST['action'] == "userid")
  {  
		$newFName = pg_escape_string($connection,$_POST['userFName']);
		$newLName = pg_escape_string($connection,$_POST['userLName']);
		if (!empty($_POST['userPassword']))
		{
			$newPassword = pg_escape_string($connection,$_POST['userPassword']);
		}
		$newDOB = pg_escape_string($connection,$_POST['userDOB']);
		$newEmail = pg_escape_string($connection, $_POST['userEmail']);
		$newUserPoint = pg_escape_string($connection, $_POST['userPoint']);
		$newBLCount = pg_escape_string($connection, $_POST['userBCount']);
		if(isset($newPassword))
		{
			$updateIDQuery = "UPDATE users SET firstname='".$newFName."', lastname='".$newLName."', password='".md5($newPassword)."', dob='".$newDOB."', userpoint =".$newUserPoint.", blacklistcount='".$newBLCount."'
			WHERE email = '".$userEmail."'";
		}
		else
		{
			$updateIDQuery = "UPDATE users SET firstname='".$newFName."', lastname='".$newLName."', dob='".$newDOB."',email='".$newEmail."', userpoint =".$newUserPoint.", blacklistcount='".$newBLCount."'
			WHERE email = '".$userEmail."'";			
		}
	  $updateIDResult = pg_query($connection, $updateIDQuery);

		if($updateIDResult){
			echo ("
			<div class=\"alert alert-info\">
				<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>
				User updated!
			</div>
					");
		
		}
				
		//header("Refresh: 10; Location: /stuff-sharing/updateParticular.php");
		//header(Location: /stuff-sharing/updateParticular.php");
  }

?>

<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="/stuff-sharing/admin.php"><?php echo $cRow[0]. " ".$cRow[1] ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
					
          <ul class="nav navbar-nav navbar-right">
		  
            <li><a href="/stuff-sharing/logout.php/">Logout</a></li>				
					
          </ul> 
        </div>
      </div>
    </nav>

<div class="container">
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Edit User Page</h3>
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
          <form action="edituser.php" method="post">
            <fieldset>
              <div class="form-group">
                <input class="form-control" id="user-fname" name="userFName" type="text" placeholder="User First Name" value = "<?php echo $userFName;?>" autofocus>
              </div>
			  <div class="form-group">
                <input class="form-control" id="user-lname" name="userLName" type="text" placeholder="User Last Name" value = "<?php echo $userLName;?>" autofocus>
              </div>
			  <div class="form-group">
                <input class="form-control" id="user-password" name="userPassword" type="text" placeholder="User Password">
              </div>
			  <div class="form-group">
                <input class="form-control" id="user-dob" name="userDOB" type="text" placeholder="User DOB" value = "<?php echo $userDOB;?>" autofocus>
              </div>
			  <div class="form-group">
                <input class="form-control" id="user-email" name="userEmail" type="text" placeholder="User Email" value = "<?php echo $userEmail;?>" autofocus>
              </div>
			  <div class="form-group">
                <input class="form-control" id="user-point" name="userPoint" type="text" placeholder="User Point" value = "<?php echo $userPoint;?>" autofocus>
              </div>
			  <div class="form-group">
                <input class="form-control" id="user-bcount" name="userBCount" type="text" placeholder="User Black List Count" value = "<?php echo $userBCount;?>" autofocus>
              </div>
			  
			  
              
              <div class="form-group">
                <input class="form-control" name="action" type="hidden" value="userid" />
              </div>
              <button type="submit" class="btn btn-success btn-block">edit user</button>

            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>