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
        $cRow = pg_fetch_row($result);
    }

    		
	//get users information
	$usersResult = pg_query($connection,"SELECT * from Users;") 
	or die ('Query failed: '.pg_last_error());
	
	//get all items
	$itemResult = pg_query($connection,"SELECT * from itemlist;") 
	or die ('Query failed: '.pg_last_error());
	
	//get all bids
	$bidResult = pg_query($connection,"SELECT bl.itemid, il.itemname, bl.bidderid, CONCAT(u.firstname, u.lastname), u.email, bl.bidamount from biddinglist bl INNER JOIN itemlist il ON bl.itemid = il.itemid INNER JOIN users u ON bl.bidderid = u.email;") 
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
	
	if(isset($_POST['deletebidid']))
	{
		$deletebidid = $_POST['deletebidid'];
		$uresult = pg_query($connection, "SELECT userpoint FROM users WHERE email = '".$deletebidid[1]."'");
		$oldbidAmount = pg_fetch_row($uresult);		
		$newbidAmount = $oldbidAmount[0] + $deletebidid[2];		
		$updateResult = pg_query($connection, "UPDATE users SET userpoint = '".$newbidAmount."' WHERE email = '".$deletebidid[1]."';");
		$deleteResult = pg_query($connection, "DELETE FROM biddinglist WHERE itemid = '".$deletebidid[0]."' AND bidderid = '".$deletebidid[1]."';");
		if($deleteResult){
		echo ("
		<div class=\"alert alert-info\">
			<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>
			Bid Deleted!
		</div>
        ");
	}
	}
		
		
?>

<body>
    <?php
      include('admin_navbar.php');
    ?>

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
                echo "<p>Welcome, " .$cRow[0]. " " .$cRow[1]. "</p>";
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
        <div class="accordionSection" id="editItem"><h3>Drop Bids</h3>				
			<div class="table-responsive">
				<table class="table table-striped table-bordered table-list">
					<thead>
						<tr>
							<th>Item Name</th> <th>Bidder Name</th> <th>Bid Amount</th> <th></th>
						</tr>
					</thead>
					<tbody>
						<?php
							while($row = pg_fetch_row($bidResult)){
								echo "\t<tr>\n";
								echo "\t\t<td>$row[1]</td>\n";
								echo "\t\t<td>$row[3]</td>\n";
								echo "\t\t<td>$row[5]</td>\n";														
								echo "\t\t<td><form action=\"admin.php\" method=\"post\">";
								$postvalue=array($row[0],$row[2],$row[5]);
								foreach($postvalue as $value)
								{
									echo "<input type=\"hidden\" name=\"deletebidid[]\" value=\"".$value."\"/>";
								}								
								echo "<button type=\"submit\" class=\"btn btn-sm\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span></button></form></td>\n";								
								echo "\t</tr>\n";
							}											
						?>
					</tbody>
				</table>
			</div>
		</div>
        
    </div>
</body>