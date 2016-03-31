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
		$query = "SELECT firstName, lastName, userpoint FROM users where email='".$email."'";
		$result = pg_query($connection,$query) or die('Query failed:'.pg_last_error());
        $row = pg_fetch_row($result);
	}

	$itemresult = pg_query($connection,"SELECT l.itemName,l.itemId,l.itemCategory,l.itemDescription FROM ItemList l, Advertise a WHERE l.itemId = a.itemId ORDER BY itemName ASC");
	//get particulars
	$particulars = pg_query($connection,"SELECT u.firstname, u.lastname, u.dob, u.email FROM users u WHERE u.email = '".$email."'") 
	or die ('Query failed: '.pg_last_error());
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
          <h3 class="panel-title">ItemList</h3>
        </div>
        <div class="panel-body">
		  <div class="table-responsive">
			<table class="table table-striped table-bordered table-list">
			<thead>
			  <tr>
			    <th>itemName</th> <th>itemId</th> <th>itemCategory</th> <th>itemDescription</th> <th>View details</th>
			  </tr>
			</thead>
			<?php
	
			while($row = pg_fetch_array($itemresult, null, PGSQL_ASSOC)){
				echo "\t<tbody>\n\t<tr>\n";
				foreach ($row as $col_value) {
					echo "\t\t<td>$col_value</td>\n";
				}
				echo "\t\t<td><form action=\"itemList.php\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"go-specific-id\" value=\"".$row[1]."\"/>";
				echo "<button type=\"submit\" class=\"btn btn-success\">View</button></form></td>\n";
				echo "\t</tr>\n\t</tbody>\n";
			}
			?>
		    </table>
		  </div>
	    </div>
		<div class="panel-footer">
		  <a href="/stuff-sharing/additem.php" class="btn btn-info" role="button">add Item</a>
		</div>
      </div>
    </div>
  </div>
</div>
</body>