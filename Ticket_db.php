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

$query = "SELECT * FROM Parking_Ticket.Tickets";
$result = mysqli_query($conn, $query) or die (mysqli_error($conn));

print "<pre>";

while ($event1 = mysqli_fetch_array($result, MYSQLI_BOTH)){
	print "Ticket number: $event1[idTickets], State: $event1[License_Plate]";
}
print"</pre>";

mysqli_free_result($result);
mysqli_close($conn);
?>

<p>Hello!</p>
</body>
</html>