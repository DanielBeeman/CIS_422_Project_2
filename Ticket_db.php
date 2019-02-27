<!DOCTYPE html>
# Authors: Daniel Beeman and Jake Beder. Last Edited 2/26/19. This is the 
# page that displays information about a ticket, or an error message if
# there are no tickets to be displayed.  





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

# This code grabs information from the ticked database
$query = "SELECT * FROM Parking_Ticket.Tickets AS Tic WHERE Tic.License_Plate LIKE ";

# This searches specifically for the state specified by the user in the drop down menu
# because there could be two cars with the same license plate, but in different states.
$query = $query."'".$name."'";
$query = $query." AND Tic.State LIKE ";
$query = $query."'".$name2."';";


# The result of the query
$result = mysqli_query($conn, $query);

# This keeps track of how many rows were returned, which helps us with the error message
print "<pre>";
$rowcount = mysqli_num_rows($result);

# If there are no rows returned, we print the error page
$check = 0;
if ($rowcount > 0){
	$check = 1;
}
if($check ==0){
	echo "Error: No tickets have been found";
}

# This prints out information about tickets for a given license plate
while ($event1 = mysqli_fetch_array($result, MYSQLI_BOTH)){
		
	print "Ticket number: $event1[idTickets], State: $event1[State], License Plate: $event1[License_Plate]";
}

print"</pre>";

# Free and close the sql connection
mysqli_free_result($result);
mysqli_close($conn);
?>

</body>
</html>
