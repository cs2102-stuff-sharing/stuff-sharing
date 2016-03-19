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
  }
	
	if(isset($_SESSION['edititemid']))
	{
		$edititemid = pg_escape_string($connection,$_SESSION['edititemid']);
		$itemresult = pg_query($connection,"SELECT i.itemName,i.itemId,i.itemCategory,i.itemDescription 
		FROM ItemList i WHERE i.itemid = '".$edititemid."'");  
		if(!$itemresult)
		{
			$message = "Something seems to be wrong, please try later";
		}
		else
		{
			$row = pg_fetch_row($itemresult);
			$itemName = $row[0];
			$itemId = $row[1];
			$itemCategory = $row[2];
			$itemDescription = $row[3];			
		}
	}

  if(isset($_POST['action']) && $_POST['action'] == "edititem")
  {  
  	$newItemName = pg_escape_string($connection,$_POST['itemName']);
  	$newItemDescription = pg_escape_string($connection,$_POST['itemDescription']);
  	$newItemCategory = pg_escape_string($connection,$_POST['itemCategory']);
  	$editItemQuery = "UPDATE ItemList SET itemName='".$newItemName."', itemDescription='".$newItemDescription."', itemCategory='".$newItemCategory."'
		WHERE itemId = '".$itemId."'";
	  $editItemResult = pg_query($connection, $editItemQuery);

		if($editItemResult){
			echo ("
			<div class=\"alert alert-info\">
				<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>
				Item updated!
			</div>
					");
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
						<li><a href="myitem.php">My Items</a></li>
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
    <div class="col-md-4 col-md-offset-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Edit Item Page</h3>
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
          <form action="edititem.php" method="post">
            <fieldset>
              <div class="form-group">
                <input class="form-control" id="item-name" name="itemName" type="text" placeholder="Item Name" value = "<?php echo $itemName;?>" autofocus>
              </div>
              <div class="form-group">
                <input class="form-control" id="item-description" name="itemDescription" type="text" placeholder="Item Description" value = "<?php echo $itemDescription;?>">
              </div>
              <div class="form-group">
                <select class="form-control" id="categories" name="itemCategory">
                	  <option value = 'Home' <?php if($itemCategory == 'Home') {echo "selected = \"selected\" ";}?> >Home Use</option>
                    <option value = 'Phone' <?php if($itemCategory == 'Phone') {echo "selected = \"selected\" ";}?> >Phone</option>
                    <option value = 'School' <?php if($itemCategory == 'School') {echo "selected = \"selected\" ";}?> >School Use</option>
                    <option value = 'Personal' <?php if($itemCategory == 'Personal') {echo "selected = \"selected\" ";}?> >Personal Use</option>
                    <option value = 'Other' <?php if($itemCategory == 'Other') {echo "selected = \"selected\" ";}?> >Other</option>
              </div>
              <div class="form-group">
                <input class="form-control" name="action" type="hidden" value="edititem" />
              </div>
              <button type="submit" class="btn btn-success btn-block">edit item</button>

            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>