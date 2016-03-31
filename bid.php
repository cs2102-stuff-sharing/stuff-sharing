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
	else if(!isset($_GET['id']))
	{
		header("Location: /stuff-sharing/error.php?id=a");
	}
	else
	{
		$email = pg_escape_string($connection,$_SESSION['key']);
		$query = "SELECT firstName, lastName, userPoint FROM users where email='".$email."'";
		$result = pg_query($connection,$query) or die('Query failed:'.pg_last_error());
		$row = pg_fetch_row($result);

		$biditemid = pg_escape_string($connection,$_GET['id']);
		$itemresult = pg_query($connection,"SELECT l.itemName,l.itemId,l.itemCategory,l.itemDescription,a.minimumBidPoint,u.firstname,u.lastname,u.email 
		FROM ItemList l, Advertise a, Users u WHERE l.itemid = '".$biditemid."' and a.itemid = l.itemid and l.owneremail = u.email");  
		if(pg_num_rows($itemresult) == 0)
		{
			header("Location: /stuff-sharing/error.php?id=b");
		}
	}
	if(isset($_POST['bidform']))
	{
		$bidpoint = pg_escape_string($connection, $_POST['bidAmount']);
		$remainpoint = (int)$row[2] - (int)$bidpoint;
		if($remainpoint < 0)
		{
			$message = "You do not have enough bid point";
		}
		elseif ($bidpoint < (int)$_POST['minimumbid'])
		{
			$message = "Bid must exceed minimum bid";
		}
		else
		{
			$bidquery = "INSERT INTO biddinglist(itemid,bidderId, bidAmount) values (".$biditemid.",'".$email."', ".$bidpoint.")";
			$bidresult = pg_query($connection,$bidquery) or die('Query failed:'.pg_last_error());
			$deductquery = "update users set userPoint = '".$remainpoint."' where email = '".$email."'";
			$deductresult = pg_query($connection,$deductquery) or die('Query failed:'.pg_last_error());
			if($bidresult and $deductresult)
			{
				//add ad successfully
				header("Location: /stuff-sharing/bid.php?id=".$biditemid."&msg=ADD_SUCCESS");
			}
			else
			{
				header("Location: /stuff-sharing/error/php?id=c");
			}
		}
	}
	if(isset($_POST['updatebidform']))
	{
		
		$updatedpoint = (int) pg_escape_string($connection,$_POST['bidAmount']);
		$originalpoint = (int) pg_fetch_array(pg_query($connection,"SELECT b.bidAmount FROM biddinglist b WHERE b.bidderId ='".$_SESSION['key']."' and b.itemId = ".$biditemid))[0];
		$deductpoint = $updatedpoint - $originalpoint;

		if($updatedpoint == 0)
		{
			$retractbidquery = "DELETE FROM BiddingList WHERE bidderId = '" .$email ."' and itemId = '" .$biditemid ."'";
			$retractbidresult = pg_query($connection,$retractbidquery);
			$regainPoints = $row[2] + $originalpoint;
			$updateuserpointquery = "UPDATE users SET userPoint = '" .$regainPoints ."' where email = '" .$email ."'";	
			$retrieveuserpointresult = pg_query($connection,$updateuserpointquery);
			if($retractbidresult && $retrieveuserpointresult)
			{
				header("Location: /stuff-sharing/bid.php?id=".$biditemid."&msg=RT_SUCCESS");

			}
			else
			{
				header("Location: /stuff-sharing/error.php");
			}
		}
		elseif($row[2] - $deductpoint >= 0)
		{
			$updateduserpoint = $row[2] - $deductpoint;
			$updateuserpointquery = "update users set userPoint = " .$updateduserpoint ." where email = '" .$email ."'";
			$updatepointresult = pg_query($connection,$updateuserpointquery);
			$updatebidpointquery = "update biddinglist set bidAmount = ".$updatedpoint ."where bidderId = '" .$email ."' and itemId = '" .$biditemid ."'";
			$updatebidresult = pg_query($connection,$updatebidpointquery);
			if($updatepointresult)
			{
				echo "<script>alert('user updated')</script>";
			}
			if($updatebidresult)
			{
				//modify bid successfully
				echo "<script>alert('".$updateduserpoint."')</script>";
				header("Location: /stuff-sharing/bid.php?id=".$biditemid."&msg=UPD_SUCCESS");
			}
			else
			{
				header("Location: /stuff-sharing/error.php?id=d");
			}
		}
		else
		{
			$message = "You do not have enough bid points";
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
          <h3 class="panel-title">Item row</h3>
        </div>
				<div class="panel-body">				
				<?php
					if(isset($_GET['msg']))
					{
						if($_GET['msg'] == ADD_SUCCESS)
						{
							echo '<div class="alert alert-success" role="alert">
										<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
										<span class="sr-only">Success:</span>Bid entered successfully!</div>';
						}
						elseif($_GET['msg'] == UPD_SUCCESS)
						{
							echo '<div class="alert alert-success" role="alert">
										<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
										<span class="sr-only">Success:</span>Bid updated successfully!</div>';
						}
						elseif($_GET['msg'] == RT_SUCCESS)
						{
							echo '<div class="alert alert-success" role="alert">
										<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
										<span class="sr-only">Success:</span>Bid retracted successfully!</div>';
						}
					}
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
		<div class="panel-footer">
		<?php
				$isOwner = pg_query($connection,"SELECT u.email FROM users u ,itemList l WHERE l.itemId = '".$biditemid."' and u.email = l.owneremail and u.email = '".$_SESSION['key']."'");
				$previousBidAmount = pg_query($connection,"SELECT b.bidAmount FROM biddinglist b WHERE b.bidderId ='".$_SESSION['key']."' and b.itemId = ".$biditemid);
				if(pg_num_rows($isOwner) == 0)
				{
					if(pg_num_rows($previousBidAmount) != 0)
					{
						echo "You have previously bidded ".pg_fetch_array($previousBidAmount)[0]." points.";
						echo "<form id=\"updatebidform\" action=\"bid.php?id=".$biditemid."\" method=\"post\">";
						echo "<input id=\"updatebidform\" type=\"hidden\" name=\"updatebidform\"/>";
						echo "<input id=\"bidAmount\" name=\"bidAmount\" autofocus></input>";
						echo "<button class=\"btn btn-success\">Modify bid</button></form> </p>";
					}
					else
					{
						echo "<form id=\"bidform\" action=\"bid.php?id=".$biditemid."\" method=\"post\">";
						echo "<input id=\"bidform\" type=\"hidden\" name=\"bidform\"/>";
						echo "<input id=\"minimumbid\" type=\"hidden\" name=\"minimumbid\" value=\"".$row[4]."\">";
						echo "<input id=\"bidAmount\" name=\"bidAmount\" autofocus></input>";
						echo "<button class=\"btn btn-success\">Bid</button></form>\n";
					}
				}
				else
				{
					echo "You cannot bid because you are the owner";
				}
		?>
		</div>
    </div>
    </div>
  </div>
</div>
</body>