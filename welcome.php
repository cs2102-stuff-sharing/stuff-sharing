<?php

    session_start();
    include('db.php');
    include('header.php');
?>
<script>
function askMinBid(pointid, formid) {
    var minbid = prompt("Please enter the minimum bid point");
    
    if (parseInt(minbid) > 0) {
        document.getElementById(pointid).value = minbid;
				document.getElementById(formid).submit();
    }
}
</script>
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

    //get archived items
    $email = pg_escape_string($connection,$_SESSION['key']);
    $archivedItems = pg_query($connection,"SELECT l.itemName,l.itemId,l.itemCategory,l.itemDescription FROM ItemList l WHERE l.itemId NOT IN (SELECT a.itemId FROM Advertise a) ORDER BY itemName ASC") or die('Query failed:'.pg_last_error());
		$advertisements = pg_query($connection,"SELECT i.itemName,i.itemCategory,u.firstName,u.lastName,a.minimumBidPoint from Advertise a, ItemList i, Users u where 
				a.itemid = i.itemid and i.owneremail = u.email") or die('Query failed:'.pg_last_error());
		
		if(isset($_POST['itemid']))
		{
		$itemid = pg_escape_string($connection,$_POST['itemid']);
		$minbid = pg_escape_string($connection,$_POST['minbid']);
		$AddAdQuery = "insert into Advertise(itemId,minimumBidPoint) values('". $itemid . "','" .$minbid ."')";
		$AddAdResult = pg_query($connection, $AddAdQuery);
			if($AddAdResult)
			{
				//add ad successfully
				header("Location: /stuff-sharing/welcome.php");
			}
			else
			{

			}
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
						<li><a href="itemlist.php">Item List</a></li>
						<li><a href="additem.php">Add Item</a></li>
					</ul>
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
        <div class="accordionSection" id="ongoingTransaction"><h3>Ongoing Transactions</h3><div><p>To be implemented</p></div></div>
        <div class="accordionSection" id="advertisingItems"><h3>Advertising Items</h3>				
					<div class="table-responsive">
					<table class="table table-striped table-bordered table-list">
					<thead>
						<tr>
						<th>itemName</th> <th>itemCategory</th> <th>ownerName</th> <th>minimumBiddingPoint</th>
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
                <th>itemName</th> <th>itemId</th> <th>itemCategory</th> <th>itemDescription</th> <th></th>
              </tr>
            </thead>
						<?php
						while($row = pg_fetch_row($archivedItems)){
							echo "\t<tbody>\n\t<tr>\n";
							foreach ($row as $col_value) {
								echo "\t\t<td>$col_value</td>\n";
							}
							echo "\t\t<td><form id=\"bidform".$row[1]."\" action=\"welcome.php\" method=\"post\">";
							echo "<input type=\"hidden\" name=\"itemid\" value=\"".$row[1]."\"/>";
							echo "<input id=\"point".$row[1]."\" type=\"hidden\" name=\"minbid\"/>";
							echo "<button onclick=\"askMinBid('point".$row[1]."', 'bidform".$row[1]."')\" class=\"btn btn-success\">advertise</button></form></td>\n";
							echo "\t</tr>\n\t</tbody>\n";
						}
						?>
            </table>
          </div></div>
    </div>
</body>