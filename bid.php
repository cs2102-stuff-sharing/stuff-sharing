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
		$query = "SELECT firstName, lastName, userPoint FROM users where email='".$email."'";
		$result = pg_query($connection,$query) or die('Query failed:'.pg_last_error());
		$info = pg_fetch_row($result);
	}
	
	if(isset($_SESSION['biditemid']))
	{
		$biditemid = pg_escape_string($connection,$_SESSION['biditemid']);
		$itemresult = pg_query($connection,"SELECT l.itemName,l.itemId,l.itemCategory,l.itemDescription,a.minimumBidPoint,u.firstname,u.lastname,u.email 
		FROM ItemList l, Advertise a, Users u WHERE l.itemid = '".$biditemid."' and a.itemid = l.itemid and l.owneremail = u.email");  
		$biddersresult = pg_query($connection, "SELECT b.bidderId, b.bidAmount, u.firstname, u.lastname FROM BiddingList b, Users u WHERE b.itemId = '".$biditemid."' and u.email = b.bidderId");
		if(!$itemresult)
		{
			$message = "Something seems to be wrong, please try later";
		}		
	}
	
	if(isset($_POST['bidpoint']))
	{
		$bidpoint = pg_escape_string($connection,$_POST['bidpoint']);
		$bidquery = "insert into BiddingList(itemId,bidderId,bidAmount) values('".$biditemid. "','" .$email ."','" .$bidpoint. "')";
		$bidresult = pg_query($connection,$bidquery) or die('Query failed:'.pg_last_error());
		$remainpoint = (int)$info[2] - (int)$bidpoint;
		$deductquery = "update Users set userPoint = '".$remainpoint."' where email = '".$email."'";
		$deductresult = pg_query($connection,$deductquery) or die('Query failed:'.pg_last_error());
		if($bidresult and $deductresult)
		{
			//add ad successfully
			header("Location: /stuff-sharing/bid.php");
		}
		else
		{
			$message = "Something seems to be wrong, please try later";
		}		
	}
	
	if(isset($_POST['updatepoint']))
	{
		$updatepoint = pg_escape_string($connection,$_POST['updatepoint']);
		$updatepointnum = (int)$updatepoint;
		$recoverpoint = pg_escape_string($connection,$_POST['recoverpoint']);
		if($updatepointnum == 0)
		{
			$retractbidquery = "DELETE FROM BiddingList WHERE bidderId = '" .$email ."' and itemId = '" .$biditemid ."'";
			$updatebidresult = pg_query($connection,$retractbidquery);
		}
		else
		{
			$updatebidquery = "update BiddingList set bidAmount = '" .$updatepoint ."' where bidderId = '" .$email ."' and itemId = '" .$biditemid ."'";
			$updatebidresult = pg_query($connection,$updatebidquery);
		}
		$updatepointquery = "update Users set userPoint = '".$recoverpoint."' where email = '".$email."'";
		$updatepointresult = pg_query($connection,$updatepointquery);
		if($updatebidresult and $updatepointresult)
		{
			//modify bid successfully
			header("Location: /stuff-sharing/bid.php");
		}
		else
		{
			$message = "Something seems to be wrong, please try later";
		}
	}
?>
<body>
    <?php
      include('navbar.php');
    ?>

<div class="container">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
		  <div class="panel panel-default">
			  <div class="panel-heading">
          <h3 class="panel-title">Item Info</h3>
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
				<?php
				$row = pg_fetch_row($itemresult);
				echo "<p>Item Name: ".$row[0]."</p>";
				echo "<p>Item Id: ".$row[1]."</p>";
				echo "<p>Item Category: ".$row[2]."</p>";
				echo "<p>Item Description: ".$row[3]."</p>";
				echo "<p>Minimum Bidding Point: ".$row[4]."</p>";
				echo "<p>Owner's Name: ".$row[5]." ".$row[6]."</p>";
				echo "<p>Owner's Email ".$row[7]."</p>";
				$minbid = $row[4];
				?>
				</div>
			</div>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Bidder List</h3>
        </div>
        <div class="panel-body">
		  <div class="table-responsive">
			<table class="table table-striped table-bordered table-list">
			<thead>
			  <tr>
			    <th>Bidder Name</th> <th>Bidder Email</th> <th>Bid Point</th> <th>Action</th>
			  </tr>
			</thead>
			<tbody>
			<?php
			while($row = pg_fetch_row($biddersresult)){
				echo "\t<tr>\n";
				echo "\t\t<td>".$row[2]." ".$row[3]."</td>\n";
				echo "\t\t<td>".$row[0]."</td>\n";
				echo "\t\t<td>".$row[1]."</td>\n";
				if ($row[0] == $email)
				{
					echo "\t\t<td>";
					echo "<form id=\"updateform\" action=\"bid.php\" method=\"post\">";
					echo "<input id=\"updatepoint\" type=\"hidden\" name=\"updatepoint\"/>";
					echo "<input id=\"recoverpoint\" type=\"hidden\" name=\"recoverpoint\"/>";
					echo "<button onclick=\"update('".$minbid."', '".$info[2]."', '".$row[1]."')\" class=\"btn btn-success\">update</button></form>\n";
					echo "</td>\n";
				}
				else
				{
					echo "\t\t<td></td>\n";
				}
				echo "\t</tr>\n";
			}
			?>
			</tbody>
		  </table>
		  </div>
	    </div>
		<div class="panel-footer">
		<?php
				$selfresult = pg_query($connection,"SELECT count(*) FROM BiddingList b WHERE b.bidderId = '".$email."' and b.itemId = '".$biditemid."'");
				$isselfin = pg_fetch_row($selfresult)[0];
				if($isselfin == 0)
				{
				echo "<form id=\"bidform\" action=\"bid.php\" method=\"post\">";
				echo "<input id=\"bidpoint\" type=\"hidden\" name=\"bidpoint\"/>";
				echo "<button onclick=\"addBid('".$minbid."', '".$info[2]."')\" class=\"btn btn-success\">add bid</button></form>\n";
				}
				else
				{
					echo "You have already bidded for the item";
				}
		?>
		</div>
    </div>
    </div>
  </div>
</div>
</body>