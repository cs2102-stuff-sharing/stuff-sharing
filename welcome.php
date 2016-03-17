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

    //get archived items
    $email = pg_escape_string($connection,$_SESSION['key']);
    $archivedItems = pg_query($connection,"SELECT l.itemName,l.itemId,l.itemCategory,l.itemDescription FROM ItemList l WHERE l.itemId NOT IN (SELECT a.itemId FROM Advertise a) ORDER BY itemName ASC") or die('Query failed:'.pg_last_error());
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
        <div class="accordionSection" id="advertisingItems"><h3>Advertising Items</h3><div><p>To be implemented</p></div></div>
        <div class="accordionSection" id="archivedItems">
            <h3>Archived Items</h3>
            <div><div class="table-responsive">
            <table class="table table-striped table-bordered table-list">
            <thead>
              <tr>
                <th>itemName</th> <th>itemId</th> <th>itemCategory</th> <th>itemDescription</th>
              </tr>
            </thead>
            <?php
    
            while($row = pg_fetch_array($archivedItems, null, PGSQL_ASSOC)){
                echo "\t<tbody>\n\t<tr>\n";
                foreach ($row as $col_value) {
                    echo "\t\t<td>$col_value</td>\n";
                }
                echo "\t</tr>\n\t</tbody>\n";
            }
            ?>
            </table>
          </div></div>
    </div>
</body>