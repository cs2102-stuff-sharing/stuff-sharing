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
        $query = "SELECT firstName, lastName, userpoint FROM users where email='".$email."'";
        $result = pg_query($connection,$query) or die('Query failed:'.pg_last_error());
        $row = pg_fetch_row($result);
    }

    //get archived items
    $email = pg_escape_string($connection,$_SESSION['key']);
    $archivedItems = pg_query($connection,"SELECT l.itemName,l.itemId,l.itemCategory,l.itemDescription FROM ItemList l WHERE l.itemId NOT IN (SELECT a.itemId FROM Advertise a) ORDER BY itemName ASC") or die('Query failed:'.pg_last_error());
	//get advertised items
	$advertisements = pg_query($connection,"SELECT i.itemName,i.itemCategory,u.firstName,u.lastName,a.minimumBidPoint,i.itemId from Advertise a, ItemList i, Users u where 
	a.itemid = i.itemid and i.owneremail = u.email and i.owneremail <> '".$email."'") or die('Query failed:'.pg_last_error());		
	//get particulars
	$particulars = pg_query($connection,"SELECT u.firstname, u.lastname, u.dob, u.email FROM users u WHERE u.email = '".$email."'") 
	or die ('Query failed: '.pg_last_error());
	//get online transaction
	$onlineT = pg_query($connection,"SELECT l.itemname, l.itemcategory, u.firstname, u.lastname, l.itemid FROM itemlist l INNER JOIN record r ON l.itemid = r.itemid INNER JOIN users u ON l.owneremail = u.email WHERE r.bidderid = '".$email."'")
	or die ('Query failed: '.pg_last_error());
	
		if(isset($_POST['itemid']))
		{
		$itemid = pg_escape_string($connection,$_POST['itemid']);
		$minbid = pg_escape_string($connection,$_POST['minbid']);
		$AddAdQuery = "insert into Advertise(itemId,minimumBidPoint) values('". $itemid . "','" .$minbid ."')";
		$AddAdResult = pg_query($connection, $AddAdQuery);
			if($AddAdResult)
			{
				//add ad successfully
				header("Location: /stuff-sharing/welcome.php?msg=ADV_SUCCESS");
			}
			else
			{

			}
		}
		if(isset($_POST['onlineid']))
		{
		$onlineid = $_POST['onlineid'];
		$_SESSION['onlineid'] = $onlineid;
		header("Location: /stuff-sharing/bid.php");
		}
		
		if(isset($_POST['updateid']))
		{
		$updateid = $_POST['updateid'];
		$_SESSION['updateid'] = $updateid;
		header("Location: /stuff-sharing/updateParticular.php");
		}
		
		if(isset($_POST['biditemid']))
		{
		$biditemid = $_POST['biditemid'];
		$_SESSION['biditemid'] = $biditemid;
		header("Location: /stuff-sharing/bid.php");
		}
?>

<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="/stuff-sharing/welcome.php"><?php echo $row[0]. " " .$row[1] ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li><a href="myitem.php">My Items</a></li>
						<li><a href="additem.php">Add Item</a></li>
					</ul>
          <ul class="nav navbar-nav navbar-right">

            
<li><a class="navbar-brand"><?php echo " Your Points: ".$row[2]  ?></a></li>
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
	<div class="accordionSection" id="updateParticulars"><h3>Update Particulars</h3>				
					<div class="table-responsive">
					<table class="table table-striped table-bordered table-list">
					<thead>
						<tr>
						<th>First Name</th> <th>Last Name</th> <th>DOB</th> <th>Email</th> <th></th>
						</tr>
					</thead>
					<tbody>
					<?php
						while($row = pg_fetch_row($particulars)){
							echo "\t<tr>\n";
							echo "\t\t<td>$row[0]</td>\n";
							echo "\t\t<td>$row[1]</td>\n";
							echo "\t\t<td>$row[2]</td>\n";
							echo "\t\t<td>$row[3]</td>\n";			
							echo "\t\t<td><form action=\"welcome.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"updateid\" value=\"".$row[4]."\"/>";
							echo "<button type=\"submit\" class=\"btn btn-success\">Update</button></form></td>\n";
							echo "\t</tr>\n";
						}											
					?>
					</tbody>
					</table>
					</div>
				</div>
        <div class="accordionSection" id="ongoingTransaction"><h3>Ongoing Transactions</h3>
			<div class="table-responsive">
					<table class="table table-striped table-bordered table-list">
					<thead>
						<tr>
						<th>itemName</th> <th>itemCategory</th> <th>ownerName</th>
						</tr>
					</thead>
					<tbody>
					<?php
						while($row = pg_fetch_row($onlineT)){
							echo "\t<tr>\n";
							echo "\t\t<td>$row[0]</td>\n";
							echo "\t\t<td>$row[1]</td>\n";
							echo "\t\t<td>$row[2]&nbsp$row[3]</td>\n";													
							echo "\t\t<td><form action=\"welcome.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"onlineid\" value=\"".$row[4]."\"/>";
							echo "<button type=\"submit\" class=\"btn btn-success\">View</button></form></td>\n";
							echo "\t</tr>\n";
						}											
					?>
					</tbody>
					</table>
			</div>
		</div>
        <div class="accordionSection" id="advertisingItems"><h3>Advertisements</h3>				
					<div class="table-responsive">
					<table class="table table-striped table-bordered table-list">
					<thead>
						<tr>
						<th>itemName</th> <th>itemCategory</th> <th>ownerName</th> <th>minimumBiddingPoint</th> <th></th>
						</tr>
					</thead>
					<tbody>
					<?php
						while($row = pg_fetch_row($advertisements)){
							echo "\t<tr>\n";
							echo "\t\t<td>$row[0]</td>\n";
							echo "\t\t<td>$row[1]</td>\n";
							echo "\t\t<td>$row[2] ".$row[3]."</td>\n";
							echo "\t\t<td>$row[4]</td>\n";
							echo "\t\t<td><form action=\"welcome.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"biditemid\" value=\"".$row[5]."\"/>";
							echo "<button type=\"submit\" class=\"btn btn-success\">go bid</button></form></td>\n";
							echo "\t</tr>\n";
						}
					?>
					</tbody>
					</table>
					</div>
				</div>
    </div>
</body>