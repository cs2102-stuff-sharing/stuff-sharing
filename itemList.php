<?php
include('db.php');


$output = "";

$result = pg_query($connection,"SELECT * FROM ItemList ORDER BY itemName ASC");

while($row = pg_fetch_array($result)){
	print_r($row);
}
echo "something";
?>
