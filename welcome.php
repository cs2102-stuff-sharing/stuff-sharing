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
	$advertisements = pg_query($connection,"SELECT i.itemName,i.itemId,i.itemCategory,a.minimumBidPoint,count(*),b2.bidAmount 
		FROM Advertise a, ItemList i, BiddingList b1, BiddingList b2 WHERE a.itemid = i.itemid AND i.owneremail = '".$email."' 
		AND b1.itemid = i.itemid AND b2.itemid = i.itemid AND b2.bidAmount >= ALL(SELECT bidAmount from BiddingList WHERE itemid = i.itemid) 
		GROUP BY i.itemName,i.itemId,i.itemCategory,a.minimumBidPoint,b2.bidAmount") 
		or die('Query failed:'.pg_last_error());
		$adwithoutbid = pg_query($connection, "SELECT i.itemName,i.itemId,i.itemCategory,a.minimumBidPoint FROM Advertise a, ItemList i 
		WHERE a.itemid = i.itemid AND i.owneremail = '".$email."' AND i.itemId NOT IN (SELECT itemId FROM BiddingList)")
		or die('Query failed:'.pg_last_error());
	//get particulars
	$particulars = pg_query($connection,"SELECT u.firstname, u.lastname, u.dob, u.email FROM users u WHERE u.email = '".$email."'") 
	or die ('Query failed: '.pg_last_error());
	//get online transaction
	$onlineT = pg_query($connection,"SELECT l.itemname, l.itemcategory, u.firstname, u.lastname, l.itemid FROM itemlist l INNER JOIN biddinglist b ON l.itemid = b.itemid INNER JOIN users u ON l.owneremail = u.email WHERE b.bidderid = '".$email."'");
	//get borrowing status
	$borrowStatus = pg_query($connection,"SELECT i.itemid, i.itemname, i.itemcategory, u.firstname, u.lastname, r.bidAmount FROM users u, record r, itemlist i WHERE r.bidderid = '".$email."' AND r.itemid = i.itemid AND i.owneremail = u.email");
	//get lending status
	$lendingStatus = pg_query($connection,"SELECT i.itemid, i.itemname, i.itemcategory, u.firstname, u.lastname, r.bidAmount FROM users u, record r, itemlist i WHERE r.itemid = i.itemid AND i.owneremail ='".$email."' AND u.email = r.bidderid");
	
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

    <?php

    include('navbar.php');
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
    	 <div class="accordionSection" id="borrowingStatus"><h3>Borrowing Status</h3>
			<div class="table-responsive">
					<table class="table table-striped table-bordered table-list">
					<thead>
						<tr>
						<th>itemId</th> <th>itemName</th> <th>itemCategory</th> <th>ownerName</th> <th>Your Bid amount</th>
						</tr>
					</thead>
					<tbody>
					<?php
						while($row = pg_fetch_row($borrowStatus)){
							echo "\t<tr>\n";
							echo "\t\t<td>$row[0]</td>\n";
							echo "\t\t<td>$row[1]</td>\n";
							echo "\t\t<td>$row[2]</td>\n";
							echo "\t\t<td>$row[3]&nbsp$row[4]</td>\n";
							echo "\t\t<td>$row[5]</td>\n";
							/*echo "\t\t<td><form action=\"welcome.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"onlineid\" value=\"".$row[4]."\"/>";
							echo "<button type=\"submit\" class=\"btn btn-success\">View</button></form></td>\n";
							echo "\t</tr>\n";*/
						}											
					?>
					</tbody>
					</table>
			</div>
		</div>

		 <div class="accordionSection" id="lendingStatus"><h3>Lending Status</h3>
			<div class="table-responsive">
					<table class="table table-striped table-bordered table-list">
					<thead>
						<tr>
						<th>itemId</th> <th>itemName</th> <th>itemCategory</th> <th>Borrower Name</th> <th>Successful Bid amount</th>
						</tr>
					</thead>
					<tbody>
					<?php
						while($row = pg_fetch_row($lendingStatus)){
							echo "\t<tr>\n";
							echo "\t\t<td>$row[0]</td>\n";
							echo "\t\t<td>$row[1]</td>\n";
							echo "\t\t<td>$row[2]</td>\n";
							echo "\t\t<td>$row[3]&nbsp$row[4]</td>\n";
							echo "\t\t<td>$row[5]</td>\n";
							/*echo "\t\t<td><form action=\"welcome.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"onlineid\" value=\"".$row[4]."\"/>";
							echo "<button type=\"submit\" class=\"btn btn-success\">View</button></form></td>\n";
							echo "\t</tr>\n";*/
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
						<th>itemName</th> <th>itemCategory</th> <th>ownerName</th> <th>minimumBiddingPoint</th> <th>numberOfBidders</th> <th>highestBiddingPoint</th> <th></th>
						</tr>
					</thead>
					<tbody>
					<?php
						while($row = pg_fetch_row($advertisements)){
							echo "\t<tr>\n";
							echo "\t\t<td>$row[0]</td>\n";
							echo "\t\t<td>$row[1]</td>\n";
							echo "\t\t<td>$row[2]</td>\n";
							echo "\t\t<td>$row[3]</td>\n";
							echo "\t\t<td>$row[4]</td>\n";
							echo "\t\t<td>$row[5]</td>\n";
							echo "\t\t<td><form action=\"myitem.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"biditemid\" value=\"".$row[1]."\"/>";
							echo "<button type=\"submit\" class=\"btn btn-success\">go manage</button></form></td>\n";
							echo "\t</tr>\n";
						}
						while($row = pg_fetch_row($adwithoutbid)){
							echo "\t<tr>\n";
							echo "\t\t<td>$row[0]</td>\n";
							echo "\t\t<td>$row[1]</td>\n";
							echo "\t\t<td>$row[2]</td>\n";
							echo "\t\t<td>$row[3]</td>\n";
							echo "\t\t<td>0</td>\n";
							echo "\t\t<td>N/A</td>\n";
							echo "\t\t<td><form action=\"myitem.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"biditemid\" value=\"".$row[1]."\"/>";
							echo "<button type=\"submit\" class=\"btn btn-success\">go manage</button></form></td>\n";
							echo "\t</tr>\n";
						}						
					?>
					</tbody>
					</table>
					</div>
				</div>
    </div>
</body>