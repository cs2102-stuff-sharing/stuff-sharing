<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="/stuff-sharing/welcome.php"><?php echo $row[0]. " " .$row[1] ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li><a href="myitem.php">My Items</a></li>
						<li><a href="additem.php">Add Item</a></li>
						<li><a href="itemList.php">Explore</a></li>
					</ul>
          <ul class="nav navbar-nav navbar-right">

            
<li><a class="navbar-brand"><?php echo " User Points: ".$row[2]  ?></a></li>
            <li><a href="/stuff-sharing/logout.php/">Logout</a></li>
          </ul> 
        </div>
      </div>
    </nav>