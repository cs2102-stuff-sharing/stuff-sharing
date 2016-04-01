<?php 
	session_start();
	include('db.php');
	include('header.php');

	if(!isset($_SESSION['key']))
	{
		header("Location: /stuff-sharing/login.php?error=NOT_LOGIN");
	}

	{
	$email = pg_escape_string($connection,$_SESSION['key']);
    $query = "SELECT firstName, lastName, userpoint FROM users where email='".$email."'";
    $result = pg_query($connection,$query) or die('Query failed:'.pg_last_error());
    $row = pg_fetch_row($result);
  	}

	if(isset($_POST['searchItem'])){
		$itemName = pg_escape_string($connection,$_POST['itemName']);
		$itemCategory = pg_escape_string($connection,$_POST['itemCategory']);
		$searchItemQeury = "SELECT l.itemName,l.itemId,l.itemCategory,l.itemDescription FROM ItemList l, Advertise a WHERE l.itemId = a.itemId AND l.itemName LIKE '%".$itemName."%' AND l.itemCategory = '".$itemCategory."'";
		$searchResult = pg_query($connection,$searchItemQeury);
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
          <h3 class="panel-title">Search</h3>
        </div>
        <div class="panel-body">
		  <div class="table-responsive">
			<table class="table table-striped table-bordered table-list">
			<thead>
			<form action="searchItem.php" method="post">
            <fieldset>
              <div class="form-group">
                <input class="form-control" id="searchForm" name="itemName" type="text" autofocus>
              </div>
              <div class="form-group">
                <select class="form-control" id="categories" name="itemCategory">
                	<option value = 'Home'>Home Use</option>
                    <option value = 'Phone'>Phone</option>
                    <option value = 'School'>School Use</option>
                    <option value = 'Personal'>Personal Use</option>
                    <option value = 'Other'>Other</option>
              </div>
              <div class="form-group">
                <input class="form-control" name="searchItem" type="hidden" value="search" />
              </div>
              <button type="submit" class="btn btn-success btn-block">Search</button>

            </fieldset>
          </form>
				
			  <tr>
			    <th>itemName</th> <th>itemId</th> <th>itemCategory</th> <th>itemDescription</th> <th>View details</th>
			  </tr>
			</thead>
			<?php
	
			while($row = pg_fetch_array($searchResult, null, PGSQL_ASSOC)){
				echo "\t<tbody>\n\t<tr>\n";
				foreach ($row as $col_value) {
					echo "\t\t<td>$col_value</td>\n";
				}
				echo "\t\t<td><form action=\"itemList.php\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"itemid\" value=\"".$row['itemid']."\"/>";
				echo "<button type=\"submit\" class=\"btn btn-success\">View</button></form></td>\n";
				echo "\t</tr>\n\t</tbody>\n";
			}
			?>
		    </table>
		  </div>
	    </div>
      </div>
    </div>
  </div>
</div>

	</body>