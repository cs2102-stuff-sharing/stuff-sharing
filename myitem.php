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

    if(isset($_POST['deleteitemid']))
	{
		echo "<script>alert('Yoohoo')</script>";
		$deleteitemid = $_POST['deleteitemid'];
		$deleteResult = pg_query($connection, "UPDATE itemlist SET itemDeleted = true WHERE itemid = ".$deleteitemid);
	}

    //get archived items
    $email = pg_escape_string($connection,$_SESSION['key']);
    $archivedItems = pg_query($connection,"SELECT l.itemName,l.itemId,l.itemCategory,l.itemDescription FROM ItemList l WHERE l.itemId NOT IN (SELECT a.itemId FROM Advertise a) AND l.itemID NOT IN (SELECT r.itemid FROM record r) AND l.itemDeleted = false ORDER BY itemName ASC") or die('Query failed:'.pg_last_error());
		//get advertised items
		$advertisements = pg_query($connection,"SELECT i.itemName,i.itemId,i.itemCategory,a.minimumBidPoint,count(*),b2.bidAmount 
		FROM Advertise a, ItemList i, BiddingList b1, BiddingList b2 WHERE a.itemid = i.itemid AND i.owneremail = '".$email."' 
		AND b1.itemid = i.itemid AND b2.itemid = i.itemid AND b2.bidAmount >= ALL(SELECT bidAmount from BiddingList WHERE itemid = i.itemid) 
		GROUP BY i.itemName,i.itemId,i.itemCategory,a.minimumBidPoint,b2.bidAmount") 
		or die('Query failed:'.pg_last_error());
		$adwithoutbid = pg_query($connection, "SELECT i.itemName,i.itemId,i.itemCategory,a.minimumBidPoint FROM Advertise a, ItemList i 
		WHERE a.itemid = i.itemid AND i.owneremail = '".$email."' AND i.itemId NOT IN (SELECT itemId FROM BiddingList)")
		or die('Query failed:'.pg_last_error());
		
		if(isset($_POST['aditemid']))
		{
		$aditemid = pg_escape_string($connection,$_POST['aditemid']);
		$minbid = pg_escape_string($connection,$_POST['minbid']);
		$AddAdQuery = "INSERT INTO Advertise(itemId,minimumBidPoint) VALUES('". $aditemid . "','" .$minbid ."')";
		$AddAdResult = pg_query($connection, $AddAdQuery);
			if($AddAdResult)
			{
				//add ad successfully
				header("Location: /stuff-sharing/myitem.php");
			}
			else
			{

			}
		}
		
		if(isset($_POST['biditemid']))
		{
		$biditemid = $_POST['biditemid'];
		$_SESSION['biditemid'] = $biditemid;
		header("Location: /stuff-sharing/managebid.php");
		}
		
		if(isset($_POST['edititemid']))
		{
		$edititemid = $_POST['edititemid'];
		$_SESSION['edititemid'] = $edititemid;
		header("Location: /stuff-sharing/edititem.php");
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
        <div class="accordionSection" id="advertisingItems"><h3>Advertising Items</h3>				
					<div class="table-responsive">
					<table class="table table-striped table-bordered table-list">
					<thead>
						<tr>
						<th>itemName</th> <th>itemId</th> <th>itemCategory</th> <th>minBiddingPoint</th> <th>numberOfBidders</th> <th>highestBiddingPoint</th> <th></th>
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
        <div class="accordionSection" id="archivedItems">
            <h3>Archived Items</h3>
            <div><div class="table-responsive">
            <table class="table table-striped table-bordered table-list">
            <thead>
              <tr>
                <th>itemName</th> <th>itemId</th> <th>itemCategory</th> <th>itemDescription</th> <th>Option</th>
              </tr>
            </thead>
						<tbody>
						<?php
						while($row = pg_fetch_row($archivedItems)){
							echo "\t<tr>\n";
							foreach ($row as $col_value) {
								echo "\t\t<td>$col_value</td>\n";
							}
							echo "\t\t<td><form id=\"adform".$row[1]."\" action=\"myitem.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"aditemid\" value=\"".$row[1]."\"/>";
							echo "<input id=\"point".$row[1]."\" type=\"hidden\" name=\"minbid\"/>";
							echo "<button onclick=\"askMinBid('point".$row[1]."', 'adform".$row[1]."')\" class=\"btn btn-success\">advertise</button></form>\n";
							echo "\t\t<td><form action=\"myitem.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"edititemid\" value=\"".$row[1]."\"/>";
							echo "<button type=\"submit\" class=\"btn btn-sm\"><span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span></button></form>\n";
							echo "\t\t<td><form action=\"myitem.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"deleteitemid\" value=\"".$row[1]."\"/>";
							echo "<button type=\"submit\" class=\"btn btn-sm\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span></button></form></td>\n";
							echo "\t</tr>\n";
						}
						?>
						</tbody>
            </table>
          </div></div>
    </div>
</body>