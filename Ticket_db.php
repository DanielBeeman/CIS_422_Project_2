<!DOCTYPE html>
<!--
Authors: Daniel Beeman, Jake Beder, Anna Saltveit, and Chandler Potter. Last Edited 3/12/19. This is the 
page that displays information about a ticket, or an error message if
there are no tickets to be displayed. 
Anna Saltveit and Chandler Potter - php ticket parsing, adding that data to the google map 
-->



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
<body style='height:1200px;background:linear-gradient(to bottom right, #33ccff 0%, #6600ff 100%);'>

<?php
# Start new session
$name = $_POST['plate'];
$name2 = $_POST['state'];

# This code grabs information from the ticked database
$query = "SELECT * FROM Tickets AS Tic WHERE Tic.License_Plate LIKE ";

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

$im = imagecreatefrompng();
# Stores all tickets for use in javaScript
$ticket_array = array();

# To index the main array
$i = 0;
# Retrieve each ticket from database and parse
while ($event1 = mysqli_fetch_array($result, MYSQLI_BOTH)){
        # Encode image data
        $tempIm = '<img src="data:image/jpeg;base64,'.base64_encode($event1['Picture']).'"height=350" width="350" />';

        # Create tempEvent to avoid non-encoded image data
        $tempEvent = array();
        $tempEvent['End_Date'] = $event1['End_Date'];
        $tempEvent['idTickets'] = $event1['idTickets'];
        $tempEvent['State'] = $event1['State'];
        $tempEvent['License_Plate'] = $event1['License_Plate'];
        $tempEvent['Description'] = $event1['Description'];
        $tempEvent['Latitude'] = $event1['Latitude'];
        $tempEvent['Longitude'] = $event1['Longitude'];
        # Combine event data and encoded image into array and add to array of tickets
        $ticket_array[$i] = array($tempEvent, $tempIm); //changed from tempIm
        $i++;
}
// Printing 'ticket' vs. 'tickets' based on number of tickets
if ($rowcount == 1){
        print "<center><p style='font-size:20px;font-family:futura'> You have $rowcount ticket.</p><p style='font-family:futura'> Hover over a marker to view more information.</p><p style='font-family:futura'> Click a marker to view the image of the violation.</p></center>";
}
else {
        print "<center><p style='font-size:20px; font-family:futura'> You have $rowcount tickets.</p><p style='font-family:futura'> Hover over a marker to view more information.</p><p style='font-family:futura'> Click a marker to view the image of the violation.</p></center>";
}

# json_encode turns php array into JavaScript dictionary
$js_array = json_encode($ticket_array);
print"</pre>";


# Free and close the sql connection
mysqli_free_result($result);
mysqli_close($conn);
?>
<center><div id="map_canvas" style='height:800px;margin-right:100px;margin-left:100px;'></div></center>
<script type="text/javascript">

function myMap() {
  // Create map centered on the geographical center of the USA
  var latLng = new google.maps.LatLng(39.8097343, -98.5556199);
  var mapBounds = new google.maps.LatLngBounds();
  var cityLevelZoom = 4;
  var roadLevelZoom = 13;
  var mapProp= {
    center:latLng,
    zoom:cityLevelZoom,
  };
  var map = new google.maps.Map(document.getElementById("map_canvas"), mapProp);
  // Containers store relevant data for each marker(ticket)
  var markers = [];
  var pic_contents = [];
  var desc_contents = [];
  var desc_infowindows = [];
  var pic_infowindows = [];
  // Get and loop through dictionary of ticket information from the database/php
  var php_data = <?php echo $js_array; ?>;
  var ticketArLen = Object.keys(php_data).length;
   
  for (var i = 0; i < ticketArLen; i++){
        // Latitude and Longititude first come as strings
        var lat = parseFloat(php_data[i][0]['Latitude']);  
        var lng = parseFloat(php_data[i][0]['Longitude']);
        // Create marker and marker's position
        var markLatLng = new google.maps.LatLng(lat, lng);
        var marker = new google.maps.Marker({position: markLatLng, map: map, id: i});
        // Include marker's position within the focus of the map
        mapBounds.extend(marker.position);
        // Store marker and its index
        markers[i] = marker;
        markers[i].index = i;
        // Create and store the contents for a  picture window and a description window for the current marker
        pic_contents[i] ='<p>' + 'Ticket ID: ' + php_data[i][0]['idTickets'] + '</p>' + '<p> You may contest this ticket until: ' + php_data[i][0]['End_Date'] + '</p><p> Description: ' + php_data[i][0]['Description'] + '</p>' + '<p><center>' + php_data[i][1] + '</center></p>';
        desc_contents[i] = '<p>' + 'Ticket ID: ' + php_data[i][0]['idTickets'] + '</p><p> You may contest this ticket until: ' + php_data[i][0]['End_Date']  + '</p><p> Description: ' + php_data[i][0]['Description'] + '</p>';
        // Create and store the actual picture infowindow and description infowindow for the marker
        desc_infowindows[i] = new google.maps.InfoWindow({
                content: desc_contents[i],
                position: markers[i].getPosition(),
        });     
        pic_infowindows[i] = new google.maps.InfoWindow({
                content: pic_contents[i],
                position: markers[i].getPosition(),
        });
        // Opens a popup infowindow when user clicks on the marker
        google.maps.event.addListener(markers[i], 'click', function() {
                console.log(this.index);
                pic_infowindows[this.index].open(map);
        });

        // Opens a popup infowindow when user hovers the mouse over the marker
        google.maps.event.addListener(markers[i], 'mouseover', function() {
                desc_infowindows[this.index].open(map, this);

        });

        // Closes the popup  infowindow when the mouse leaves the  marker
        google.maps.event.addListener(markers[i], 'mouseout', function() {
                desc_infowindows[this.index].close();
        });
  }// Ends loop
  // Check if there are multiple markers
  if (ticketArLen > 1){
        // Focus map on markers
        map.fitBounds(mapBounds);
};
  // Only 1 marker
  if (ticketArLen == 1){
        map.fitBounds(mapBounds);
        // Restore the zoom level after the map is done scaling
        var listener = google.maps.event.addListener(map, "idle", function () {
                map.setZoom(13);
                google.maps.event.removeListener(listener);
        });
  };
} // Ends mymap function

</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBrR-wjiUewpwtVylBDSYL3Hpu5AuKJ4SQ&callback=myMap"
  type="text/javascript"></script>


