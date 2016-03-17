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
		$info = pg_fetch_row($result);
	}

	$itemresult = pg_query($connection,"SELECT itemName,itemId,itemCategory,itemDescription FROM ItemList ORDER BY itemName ASC");
	
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
			$message = "Something seems to be wrong, please try later";
		}
	}
?>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="/stuff-sharing/welcome.php"><?php echo $info[0]. " " .$info[1] ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li class="active"><a href="itemlist.php">Item List</a></li>
						<li><a href="additem.php">Add Item</a></li>
					</ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="/stuff-sharing/logout.php/">Logout</a></li>
          </ul> 
        </div>
      </div>
    </nav>

<div class="container">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">ItemList</h3>
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
		  <div class="table-responsive">
			<table class="table table-striped table-bordered table-list">
			<thead>
			  <tr>
			    <th>itemName</th> <th>itemId</th> <th>itemCategory</th> <th>itemDescription</th> <th></th>
			  </tr>
			</thead>
			<?php
			while($row = pg_fetch_row($itemresult)){
				echo "\t<tbody>\n\t<tr>\n";
				foreach ($row as $col_value) {
					echo "\t\t<td>$col_value</td>\n";
				}
				echo "\t\t<td><form id=\"bidform".$row[1]."\" action=\"itemList.php\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"itemid\" value=\"".$row[1]."\"/>";
				echo "<input id=\"point".$row[1]."\" type=\"hidden\" name=\"minbid\"/>";
				echo "<button onclick=\"askMinBid('point".$row[1]."', 'bidform".$row[1]."')\" class=\"btn btn-success\">advertise</button></form></td>\n";
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