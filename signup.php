<?php
include('db.php');
if(isset($_POST['action']))
{          
    if($_POST['action']=="signup")
    {
        $name       = pg_escape_string($connection,$_POST['name']);
        $email      = pg_escape_string($connection,$_POST['email']);
        $password   = pg_escape_string($connection,$_POST['password']);
        $query = "SELECT email FROM users where email='".$email."'";
        $result = pg_query($connection,$query);
        $numResults = pg_num_rows($result);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) // Validate email address
        {
            $message =  "Invalid email address please type a valid email!!";
        }
        elseif($numResults>=1)
        {
            $message = $email." Email already exist!!";
        }
        else
        {
            pg_query("insert into users(name,email,password) values('".$name."','".$email."','".md5($password)."')");
            $message = "Signup Sucessfully!!";
        }
    }
}

?>
<!-- Login and Signup forms -->
<?php 
	if(isset($message))
	{
		echo $message;
	}
	else
	{
		echo "message is unset";
	} 
?>

<div id="tabs-2">
  <form action="signup.php" method="post">
  <p><input id="name" name="name" type="text" placeholder="Name"></p>
  <p><input id="email" name="email" type="text" placeholder="Email"></p>
  <p><input id="password" name="password" type="password" placeholder="Password">
  <input name="action" type="hidden" value="signup" /></p>
  <p><input type="submit" value="Signup" /></p>
</form>
</div>