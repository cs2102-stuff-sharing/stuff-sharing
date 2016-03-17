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
        $query = "SELECT firstName, lastName FROM users where email='".$email."'";
        $result = pg_query($connection,$query) or die('Query failed:'.pg_last_error());
        $row = pg_fetch_row($result);
				$adquery = "SELECT i.itemName,i.itemCategory,u.firstName,u.lastName,a.minimumBidPoint from Advertise a, ItemList i, Users u where 
				a.itemid = i.itemid and i.owneremail = u.email";
				$adresult = pg_query($connection,$adquery) or die('Query failed:'.pg_last_error());
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
	
	<div class="container">
		<div class="alert alert-info">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<strong>Hello!</strong> Just a dummy notification!
		</div>
	</div>

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
						while($adrow = pg_fetch_row($adresult)){
							echo "\t<tr>\n";
							echo "\t\t<td>$adrow[0]</td>\n";
							echo "\t\t<td>$adrow[1]</td>\n";
							echo "\t\t<td>$adrow[2]</td>\n";
							echo "\t\t<td>$adrow[4]</td>\n";		
							echo "\t</tr>\n";
						}
					?>
					</tbody>
					</table>
					</div>
				</div>
        <div class="accordionSection" id="archivedItems"><h3>Archived Items</h3><div><p>To be implemented</p></div></div>
    </div>
</body>