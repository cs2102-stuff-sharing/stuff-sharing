<?php

    session_start();
    include('db.php');
    if(!isset($_SESSION['key']))
    {
          header("Location: /stuff-sharing/login.php?error=NOT_LOGIN");
    }
    else
    {
    $email = pg_escape_string($connection,$_SESSION['key']);
    $query = "SELECT firstName, lastName FROM users where email='".$email."'";
    $result = pg_query($connection,$query) or die('Query failed:'.pg_last_error());
    $row = pg_fetch_row($result);
    echo "<h1>Welcome, " .$row[0]. " " .$row[1]. "</h1>";
    }
?>

<a href = "/stuff-sharing/logout.php">Logout</p>
