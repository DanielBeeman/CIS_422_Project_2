<!DOCTYPE html>


<?php
# Connection data
$server = "ix.cs.uoregon.edu";
$user = "guest";
$pass = "guest";
$dbname = "Parking_Ticket";
$port = "3728";

# Open database connection
$conn = mysqli_connect($server, $user, $pass, $dbname, $port) or die('Error connecting to MySQL server.');

?>
<html>
<body>

<?php
# Start new session
$name = $_POST['plate'];
$name2 = $_POST['state'];


$query = "SELECT * FROM Parking_Ticket.Tickets AS Tic WHERE Tic.License_Plate LIKE ";

$query = $query."'".$name."'";
$query = $query." AND Tic.State LIKE ";
$query = $query."'".$name2."';";



$result = mysqli_query($conn, $query);

print "<pre>";
$rowcount = mysqli_num_rows($result);

$check = 0;
if ($rowcount > 0){
	$check = 1;
}
if($check ==0){
	echo "Error: No tickets have been found";
}


while ($event1 = mysqli_fetch_array($result, MYSQLI_BOTH)){
		
	print "Ticket number: $event1[idTickets], State: $event1[State], License Plate: $event1[License_Plate]";
}

print"</pre>";

mysqli_free_result($result);
mysqli_close($conn);
?>

</body>
</html>
