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
        $query = "SELECT firstName, lastName, userpoint FROM users WHERE email='".$email."'";
        $result = pg_query($connection,$query) or die('Query failed:'.pg_last_error());
        $row = pg_fetch_row($result);
	}
?>
<body>
	<?php
	  include('navbar.php');
	?>
	<div class="container">
		<p>Oops! Something seems wrong!</p>
		<p>Click <a href="/stuff-sharing/welcome.php">here</a> to return to the main page</p>
	</div>
</body>