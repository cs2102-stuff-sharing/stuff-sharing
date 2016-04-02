<?php

    session_start();
    include('db.php');
    include('header.php');
?>
<?php
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
    }

    		
	//get users information
	$usersResult = pg_query($connection,"SELECT * from Users;") 
	or die ('Query failed: '.pg_last_error());
	
	//get all items
	$itemResult = pg_query($connection,"SELECT * from itemlist;") 
	or die ('Query failed: '.pg_last_error());
		
	if(isset($_POST['userid']))
	{
	$userid = $_POST['userid'];
	$_SESSION['userid'] = $userid;
	header("Location: /stuff-sharing/edituser.php");
	}
	
	if(isset($_POST['itemid']))
	{
	$itemid = $_POST['itemid'];
	$_SESSION['itemid'] = $itemid;
	header("Location: /stuff-sharing/admin_edititem.php");
	}
		
		
?>

<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="/stuff-sharing/admin.php"><?php echo $row[0]. " " .$row[1] ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
					
          <ul class="nav navbar-nav navbar-right">

            

            <li><a href="/stuff-sharing/logout.php/">Logout</a></li>
          </ul> 
        </div>
      </div>
    </nav>

    <?php
    if(isset($_GET['msg']))
    {
        if($_GET['msg'] = "ITEM_ADD_SUCCESS")
        {
            echo "<div class='container'>
            <div class='alert alert-success alert-dismissible' role='alert'>
  <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
  <strong>Success!</strong> Item added!
</div></div>";
        }
        else if ($_GET['msg'] = "ADV_SUCCESS")
        {
        	 echo "<div class='container'>
            <div class='alert alert-success alert-dismissible' role='alert'>
  <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
  <strong>Success!</strong> Item advertised!
</div></div>";
        }
    }
    ?>

    <div class="container">
        <div class="starter-template">
            <?php
                echo "<p>Welcome, " .$row[0]. " " .$row[1]. "</p>";
            ?>
        </div>
    </div>

    <div class="container">
		<div class="accordionSection" id="editUsers"><h3>Edit Users</h3>				
			<div class="table-responsive">
				<table class="table table-striped table-bordered table-list">
					<thead>
						<tr>
							<th>First Name</th> <th>Last Name</th> <th>DOB</th> <th>Email</th> <th>User Point</th><th>#Black List</th> <th></th>
						</tr>
					</thead>
					<tbody>
						<?php
							while($row = pg_fetch_row($usersResult)){
								echo "\t<tr>\n";
								echo "\t\t<td>$row[0]</td>\n";
								echo "\t\t<td>$row[1]</td>\n";
								echo "\t\t<td>$row[3]</td>\n";
								echo "\t\t<td>$row[4]</td>\n";									
								echo "\t\t<td>$row[6]</td>\n";
								echo "\t\t<td>$row[7]</td>\n";	
								echo "\t\t<td><form action=\"admin.php\" method=\"post\">";
								echo "<input type=\"hidden\" name=\"userid\" value=\"".$row[4]."\"/>";
								echo "<button type=\"submit\" class=\"btn btn-success\">Edit</button></form></td>\n";
								echo "\t</tr>\n";
							}											
						?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="accordionSection" id="editItem"><h3>Edit Items</h3>				
			<div class="table-responsive">
				<table class="table table-striped table-bordered table-list">
					<thead>
						<tr>
							<th>Item ID</th> <th>Owner Email</th> <th>Item Name</th> <th>Item Description</th> <th>Item Deleted</th><th>Item Category</th> <th></th>
						</tr>
					</thead>
					<tbody>
						<?php
							while($row = pg_fetch_row($itemResult)){
								echo "\t<tr>\n";
								echo "\t\t<td>$row[0]</td>\n";
								echo "\t\t<td>$row[1]</td>\n";
								echo "\t\t<td>$row[2]</td>\n";
								echo "\t\t<td>$row[3]</td>\n";
								echo "\t\t<td>$row[4]</td>\n";									
								echo "\t\t<td>$row[5]</td>\n";								
								echo "\t\t<td><form action=\"admin.php\" method=\"post\">";
								echo "<input type=\"hidden\" name=\"itemid\" value=\"".$row[0]."\"/>";
								echo "<button type=\"submit\" class=\"btn btn-success\">Edit</button></form></td>\n";
								echo "\t</tr>\n";
							}											
						?>
					</tbody>
				</table>
			</div>
		</div>
        
        
    </div>
</body>