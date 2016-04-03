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
    $cRow = pg_fetch_row($result);
  }
	
	if(isset($_SESSION['itemid']))
	{
		$itemid = pg_escape_string($connection,$_SESSION['itemid']);
		$updateResult = pg_query($connection,"SELECT i.owneremail, i.itemname, i.itemdescription, i.itemcategory
		FROM itemList i WHERE i.itemid='".$itemid."'");
		if(!$updateResult)
		{
			$row = pg_fetch_row($updateResult);			
			$message = "Something seems to be wrong, please try later ";
		}
		else
		{
			$row = pg_fetch_row($updateResult);
			$owneremail = $row[0];
			$itemname = $row[1];			
			$itemdescription = $row[2];			
			$itemcategory = $row[3];
			
		}
	}

  if(isset($_POST['action']) && $_POST['action'] == "itemid")
  {  
		$newowneremail = pg_escape_string($connection,$_POST['owneremail']);
		$newitemname = pg_escape_string($connection,$_POST['itemname']);		
		$newitemdescription = pg_escape_string($connection,$_POST['itemdescription']);
		$newitemdeleted = pg_escape_string($connection, $_POST['itemdeleted']);
		$newitemcategory = pg_escape_string($connection, $_POST['itemcategory']);
		
		$updateIDQuery = "UPDATE itemlist SET owneremail='".$newowneremail."', itemname='".$newitemname."', itemdescription='".$newitemdescription."', itemdeleted ='".$newitemdeleted."', itemcategory='".$newitemcategory."'
		WHERE itemid = '".$itemid."'";
		
	  $updateIDResult = pg_query($connection, $updateIDQuery);

		if($updateIDResult){
			echo ("
			<div class=\"alert alert-info\">
				<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>
				Item updated!
			</div>
					");
		
		}
				
		//header("Refresh: 10; Location: /stuff-sharing/updateParticular.php");
		//header(Location: /stuff-sharing/updateParticular.php");
  }

?>

<body>
    <?php
      include('admin_navbar.php');
    ?>

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
          <form action="admin_edititem.php" method="post">
            <fieldset>
              <div class="form-group">
                <input class="form-control" id="item-owneremail" name="owneremail" type="text" placeholder="Item Owner" value = "<?php echo $owneremail;?>" autofocus>
              </div>
			  <div class="form-group">
                <input class="form-control" id="item-itemname" name="itemname" type="text" placeholder="Item Name" value = "<?php echo $itemname;?>" autofocus>
              </div>			 
			  <div class="form-group">
                <input class="form-control" id="item-itemdescription" name="itemdescription" type="text" placeholder="Item Desc" value = "<?php echo $itemdescription;?>" autofocus>
              </div>
			  <div class="form-group">
                <input class="form-control" id="item-itemdeleted" name="itemdeleted" type="text" placeholder="Item isDeleted" value = "<?php echo $itemdeleted;?>" autofocus>
              </div>
			  <div class="form-group">
                <input class="form-control" id="item-itemcategory" name="itemcategory" type="text" placeholder="Item Category" value = "<?php echo $itemcategory;?>" autofocus>
              </div>
			  
			  
			  
              
              <div class="form-group">
                <input class="form-control" name="action" type="hidden" value="itemid" />
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