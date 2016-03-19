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
		$info = pg_fetch_row($result);
	}

	$itemresult = pg_query($connection,"SELECT l.itemName,l.itemId,l.itemCategory,l.itemDescription FROM ItemList l, Advertise a WHERE l.itemId = a.itemId ORDER BY itemName ASC");
?>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="/stuff-sharing/welcome.php"><?php echo $info[0]. " " .$info[1] ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
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
		  <div class="table-responsive">
			<table class="table table-striped table-bordered table-list">
			<thead>
			  <tr>
			    <th>itemName</th> <th>itemId</th> <th>itemCategory</th> <th>itemDescription</th?>
			  </tr>
			</thead>
			<?php
	
			while($row = pg_fetch_array($itemresult, null, PGSQL_ASSOC)){
				echo "\t<tbody>\n\t<tr>\n";
				foreach ($row as $col_value) {
					echo "\t\t<td>$col_value</td>\n";
				}
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