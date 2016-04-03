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
		header("Location: /stuff-sharing/bid.php?id=".$_POST['biditemid']);
		}
		
		if(isset($_POST['lendingClose']))
		{
			$lendingCloseId = $_POST['lendingClose'];
			$itemResult = pg_query($connection, "SELECT u.email, r.bidAmount FROM users u, record r WHERE r.itemId =".$lendingCloseId." AND u.email = r.bidderid");
			$item = pg_fetch_row($itemResult);
			$amount = (int)$item[1];
			$fetchpointquery = "select userpoint from users where email = '".$item[0]."'";
			$fetchpointresult = pg_query($connection,$fetchpointquery);
			if($fetchpointresult)
			{
				$pointrow = pg_fetch_row($fetchpointresult);
				$currentpoint = (int)$pointrow[0];
				$recoveredpoint = $amount + $currentpoint;
				$recoverpointquery = "update Users set userPoint = '" .$recoveredpoint."' where email = '".$item[0]."'";
				$recoverpointresult = pg_query($connection,$recoverpointquery);
				if($recoverpointresult)
				{
					$deleterecordquery = "DELETE FROM record WHERE bidderId = '".$item[0]."' AND itemId = ".$lendingCloseId;
					$deleterecordresult = pg_query($connection,$deleterecordquery);
				}
			}
			$msg = "LENDING_CLOSED";
		}

		if(isset($_POST['lendingDone']))
		{
			$lendingDoneId = $_POST['lendingDone'];
			$itemResult = pg_query($connection, "SELECT u.email, r.bidAmount FROM users u, record r WHERE r.itemId =".$lendingDoneId." AND u.email = r.bidderid");
			$item = pg_fetch_row($itemResult);
			$amount = (int)$item[1];
			$fetchborrowerpointquery = "select userpoint from users where email = '".$item[0]."'";
			$fetchborrowerpointresult = pg_query($connection,$fetchborrowerpointquery);
			if($fetchborrowerpointresult)
			{
				$pointrow = pg_fetch_row($fetchborrowerpointresult);
				$currentpoint = (int)$pointrow[0];
				$recoveredpoint = (int) ceil($amount*1.5 + $currentpoint);
				$recoverborrowerpointquery = "update Users set userPoint = '" .$recoveredpoint."' where email = '".$item[0]."'";
				$fetchuserpointresult = pg_query($connection,"select userpoint from users where email = '".$email."'");
				$userpointrow = pg_fetch_row($fetchuserpointresult);
				$awardpoint = (int) ceil($amount*1.5 + $userpointrow[0]);
				$lenderrewardpointquery = "update Users set userPoint = '" .$awardpoint."' where email = '".$email."'";
				$recoverborrowerpointresult = pg_query($connection,$recoverborrowerpointquery);
				$lenderrewardpointresult = pg_query($connection,$lenderrewardpointquery);
				if($recoverborrowerpointresult && $lenderrewardpointresult)
				{
					$deleterecordquery = "DELETE FROM record WHERE bidderId = '".$item[0]."' AND itemId = ".$lendingDoneId;
					$deleterecordresult = pg_query($connection,$deleterecordquery);
				}
			}
			//Do the query again to update nav bar
			$query = "SELECT firstName, lastName, userpoint FROM users where email='".$email."'";
        	$result = pg_query($connection,$query) or die('Query failed:'.pg_last_error());
        	$row = pg_fetch_row($result);
        	$msg = "LENDING_DONE";
		}
		if(isset($_POST['lendingGiveUp']))
		{
			$lendingGiveUpId = $_POST['lendingGiveUp'];
			$itemResult = pg_query($connection, "SELECT u.email, r.bidAmount FROM users u, record r WHERE r.itemId =".$lendingGiveUpId." AND u.email = r.bidderid");
			$item = pg_fetch_row($itemResult);
			$amount = (int)$item[1];
			$fetchborrowerquery = "select userpoint, blackListCount from users where email = '".$item[0]."'";
			$fetchborrowerresult = pg_query($connection,$fetchborrowerquery);
			if($fetchborrowerresult)
			{
				$borrowerrow = pg_fetch_row($fetchborrowerresult);
				$currentpoint = (int)$borrowerrow[0];
				$blackListCount = ((int)$borrowerrow[1]) + 1;
				$recoveredpoint = (int) ceil(-$amount*1.5 + $currentpoint);
				$recoverborrowerquery = "update Users set userPoint = '" .$recoveredpoint."', blackListCount = ".$blackListCount." where email = '".$item[0]."'";
				$fetchuserpointresult = pg_query($connection,"select userpoint from users where email = '".$email."'");
				$userpointrow = pg_fetch_row($fetchuserpointresult);
				$awardpoint = (int) ceil($amount*1.5 + $userpointrow[0]);
				$lenderrewardpointquery = "update Users set userPoint = '" .$awardpoint."' where email = '".$email."'";
				$recoverborrowerresult = pg_query($connection,$recoverborrowerquery);
				$lenderrewardpointresult = pg_query($connection,$lenderrewardpointquery);
				if($recoverborrowerresult && $lenderrewardpointresult)
				{
					$deleterecordquery = "DELETE FROM record WHERE bidderId = '".$item[0]."' AND itemId = ".$lendingGiveUpId;
					$deleterecordresult = pg_query($connection,$deleterecordquery);
				}
			}
			//Do the query again to update nav bar
			$query = "SELECT firstName, lastName, userpoint FROM users where email='".$email."'";
        	$result = pg_query($connection,$query) or die('Query failed:'.pg_last_error());
        	$row = pg_fetch_row($result);
        	$msg = "LENDING_GIVE_UP";
		}
	//get advertised items
	$advertisements = pg_query($connection,"SELECT i.itemName,i.itemId,i.itemCategory,a.minimumBidPoint,count(*),b2.bidAmount 
		FROM Advertise a, ItemList i, BiddingList b1, BiddingList b2 WHERE a.itemid = i.itemid AND i.owneremail = '".$email."' 
		AND b1.itemid = i.itemid AND b2.itemid = i.itemid AND b2.bidAmount >= ALL(SELECT bidAmount from BiddingList WHERE itemid = i.itemid) 
		GROUP BY i.itemName,i.itemId,i.itemCategory,a.minimumBidPoint,b2.bidAmount") 
		or die('Query failed:'.pg_last_error());
		$adwithoutbid = pg_query($connection, "SELECT i.itemName,i.itemId,i.itemCategory,a.minimumBidPoint FROM Advertise a, ItemList i 
		WHERE a.itemid = i.itemid AND i.owneremail = '".$email."' AND i.itemId NOT IN (SELECT itemId FROM BiddingList)")
		or die('Query failed:'.pg_last_error());
	$mybids = pg_query($connection, "SELECT i.itemName,i.itemId, u.firstName, u.lastName, i.itemCategory, b1.bidAmount, a.minimumBidPoint, count(*), 
	b2.bidAmount FROM Advertise a, Itemlist i, Users u, BiddingList b1, BiddingList b2, BiddingList b3 WHERE a.itemid = i.itemid AND 
	u.email = i.owneremail AND b1.itemid = i.itemid AND b1.bidderId = '".$email."' AND b2.itemid = i.itemid AND b3.itemid = i.itemid AND 
	b2.bidAmount >= ALL(SELECT bidAmount from BiddingList WHERE itemid = i.itemid) GROUP BY i.itemName,i.itemId, u.firstName, u.lastName, 
	i.itemCategory, b1.bidAmount, a.minimumBidPoint, b2.bidAmount")or die('Query failed:'.pg_last_error());

	if(isset($_POST['itemid']))
	{
		header("Location: /stuff-sharing/bid.php?id=".$_POST['itemid']);
	}
	
	//get particulars
	$particulars = pg_query($connection,"SELECT u.firstname, u.lastname, u.dob, u.email FROM users u WHERE u.email = '".$email."'") 
	or die ('Query failed: '.pg_last_error());
	//get borrowing status
	$borrowStatus = pg_query($connection,"SELECT i.itemid, i.itemname, i.itemcategory, u.firstname, u.lastname, r.bidAmount FROM users u, record r, itemlist i WHERE r.bidderid = '".$email."' AND r.itemid = i.itemid AND i.owneremail = u.email");
	//get lending status
	$lendingStatus = pg_query($connection,"SELECT i.itemid, i.itemname, i.itemcategory, u.firstname, u.lastname, r.bidAmount FROM users u, record r, itemlist i WHERE r.itemid = i.itemid AND i.owneremail ='".$email."' AND u.email = r.bidderid");
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
        else if ($_GET['msg'] = "BID_MANAGE_SUCCESS")
        {
        	 echo "<div class='container'>
            <div class='alert alert-success alert-dismissible' role='alert'>
  <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
  <strong>Success!</strong> The deal is initiated successfully!
</div></div>";
        }
        else if ($_GET['msg'] = "BID_CLOSE_SUCCESS")
        {
        	 echo "<div class='container'>
            <div class='alert alert-success alert-dismissible' role='alert'>
  <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
  <strong>Success!</strong> You have successfuly closed the advertisment!
</div></div>";
        }
    }
    elseif(isset($msg))
    {
    	if($msg == "LENDING_CLOSED")
    	{
    		echo "<div class='container'>
            <div class='alert alert-success alert-dismissible' role='alert'>
  <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
  <strong>Success!</strong> You have successfuly closed the transaction!
</div></div>";
    	}
    	elseif($msg == "LENDING_DONE")
    	{
    		echo "<div class='container'>
            <div class='alert alert-success alert-dismissible' role='alert'>
  <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
  <strong>Success!</strong> You have successfuly finish the transaction!
</div></div>";
    	}
    	elseif($msg == "LENDING_GIVE_UP")
    	{
    		echo "<div class='container'>
            <div class='alert alert-success alert-dismissible' role='alert'>
  <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
  <strong>Success!</strong> You have given up the transaction!
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
    	 <div class="accordionSection" id="borrowingStatus"><h3>My Borrowing Status</h3>
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
						}											
					?>
					</tbody>
					</table>
			</div>
		</div>

		 <div class="accordionSection" id="lendingStatus"><h3>My Lending Status</h3>
			<div class="table-responsive">
					<table class="table table-striped table-bordered table-list">
					<thead>
						<tr>
						<th>itemId</th> <th>itemName</th> <th>itemCategory</th> <th>Borrower Name</th> <th>Successful Bid amount</th> <th>Option</th>
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
							echo "\t\t<td><form action=\"welcome.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"lendingDone\" value=\"".$row[0]."\"/>";
							echo "<button type=\"submit\" class=\"btn btn-success\">Done</button></form>\n";
							echo "\t\t<form action=\"welcome.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"lendingClose\" value=\"".$row[0]."\"/>";
							echo "<button type=\"submit\" class=\"btn btn-success\">Close</button></form>\n";
							echo "\t\t<form action=\"welcome.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"lendingGiveUp\" value=\"".$row[0]."\"/>";
							echo "<button type=\"submit\" class=\"btn btn-success\">Give Up</button></form>\n";
							echo "\t</td></tr>\n";
						}											
					?>
					</tbody>
					</table>
			</div>
		</div>
        <div class="accordionSection" id="advertisingItems"><h3>My Advertisements</h3>				
					<div class="table-responsive">
					<table class="table table-striped table-bordered table-list">
					<thead>
						<tr>
						<th>itemName</th> <th>itemId</th> <th>itemCategory</th> <th>minimumBiddingPoint</th> <th>numberOfBidders</th> <th>highestBiddingPoint</th> <th></th>
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
				<div class="accordionSection" id="bidItems"><h3>My Bids</h3>				
					<div class="table-responsive">
					<table class="table table-striped table-bordered table-list">
					<thead>
						<tr>
						<th>itemName</th> <th>itemId</th> <th>ownerName</th> <th>itemCategory</th> <th>myBidAmount</th> <th>minBidPoint</th> 
						<th>numOfBidders</th> <th>highestBidPoint</th> <th></th>
						</tr>
					</thead>
					<tbody>
					<?php
						while($row = pg_fetch_row($mybids)){
							echo "\t<tr>\n";
							echo "\t\t<td>$row[0]</td>\n";
							echo "\t\t<td>$row[1]</td>\n";
							echo "\t\t<td>".$row[2]." ".$row[3]."</td>\n";
							echo "\t\t<td>$row[4]</td>\n";
							echo "\t\t<td>$row[5]</td>\n";
							echo "\t\t<td>$row[6]</td>\n";
							echo "\t\t<td>$row[7]</td>\n";
							echo "\t\t<td>$row[8]</td>\n";
							echo "\t\t<td><form action=\"welcome.php\" method=\"post\">";
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