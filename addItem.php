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
  if(isset($_POST['action']) && $_POST['action'] == "addItem")
  {  
  	$itemName = pg_escape_string($connection,$_POST['itemName']);
  	$itemDescription = pg_escape_string($connection,$_POST['itemDescription']);
  	$itemCategory = pg_escape_string($connection,$_POST['itemCategory']);

  	$addItemQuery = "insert into ItemList(itemName,itemDescription,itemCategory) values ('"
  		. $itemName ."','" . $itemDescription ."', '" .$itemCategory."')";
	$addItemResult = pg_query($connection, $addItemQuery);

	if($addItemResult){
		echo ("
		<div class=\"alert alert-info\">
			<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>
			Item added!
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
          <h3 class="panel-title">Add Item Page</h3>
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
          <form action="addItem.php" method="post">
            <fieldset>
              <div class="form-group">
                <input class="form-control" id="item-name" name="itemName" type="text" placeholder="Item Name" autofocus>
              </div>
              <div class="form-group">
                <input class="form-control" id="item-description" name="itemDescription" type="text" placeholder="Item Description">
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
                <input class="form-control" name="action" type="hidden" value="addItem" />
              </div>
              <button type="submit" class="btn btn-success btn-block">addItem</button>

            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>