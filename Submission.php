<!--
This will be the home page for the parking company. They will use it to upload the violation ticket data, i.e. the longitude, latitude, timestamp, a description of the violation, and the photograph evidence of the violation, to the database

Sources: Input field: https://www.w3schools.com/tags/tag_input.asp
https://www.w3schools.com/php/php_file_upload.asp

References for limiting file inputs to only be iage files:
https://stackoverflow.com/questions/1561847/html-how-to-limit-file-upload-to-be-only-images
-->


<?php

        session_start();

        # Connection data
        $server = "ix.cs.uoregon.edu";
        $user = "guest";
        $pass = "guest";
        $dbname = "Parking_Ticket";
        $port = "3728";

        # Open database connection
        $db = mysqli_connect($server, $user, $pass, $dbname, $port) or die('Error connecting to MySQL server.');
        # check to make sure values are entered into the license plate and description fields
        if(isset($_POST['id'])) {

        $id = $_POST['id'];
        $plate = $_POST['plate'];
        $state = $_POST['state'];
        $descr = $_POST['description'];

        $image = addslashes(file_get_contents($_FILES['upload_image']['tmp_name'])); //SQL Injection defence!
        echo strlen($image);
        //$image_name = addslashes($_FILES['upload_image']['name']);

        $insert = "INSERT into Tickets (idTickets, State, License_Plate, Picture, Latitude, Longitude, Timestamp, Days_Left, Description) VALUES ('$id', '$state', '$plate', '$image', NULL, NULL, NULL, NULL, '$descr')";

        $sql = mysqli_query($db, $insert) or die(mysql_error());

        }
?>


<!--
References for limiting file inputs to only be iage files:
https://stackoverflow.com/questions/1561847/html-how-to-limit-file-upload-to-be-only-images
-->

<!DOCTYPE html>
<html>
<body>

<h1>Welcome to the Parking Ticket Hub</h1>

<p>Please enter violation data below:</p>


<!--
        The code below is surrounded by a form block. This block takes in data and then calls Ticket_db.php,
        which is a php page that makes calls to our database.
-->
<form action="Submission.php" method="POST" enctype="multipart/form-data"> <!--action="Ticket_db.php" method="post"-->

  Ticket Identification Number: <input type="text" name="id">
  <br>

<!--
        The code below takes in a license plate as text that will be used by the php page (Ticket_db.php)
-->
  License Plate: <input type="text" name="plate">


 <!--
        The code below creates a dropdown of the 50 US states to select from. When selected, the state will
        be passed to the php page (Ticket_db.php).
 -->
  Plate's state: <select name="state">
    <option value="Alabama">Alabama</option>
    <option value="Alaska">Alaska</option>
    <option value="Arizona">Arizona</option>
    <option value="Arkansas">Arkansas</option>
    <option value="California">California</option>
    <option value="Colorado">Colorado</option>
    <option value="Connecticut">Connecticut</option>
    <option value="Deleware">Deleware</option>
    <option value="Florida">Florida</option>
    <option value="Georgia">Georgia</option>
    <option value="Hawaii">Hawaii</option>
    <option value="Idaho">Idaho</option>
    <option value="Illinois">Illinois</option>
    <option value="Indiana">Indiana</option>
    <option value="Iowa">Iowa</option>
    <option value="Kansas">Kansas</option>
    <option value="Kentucky">Kentucky</option>
    <option value="Louisiana">Louisiana</option>
    <option value="Maine">Maine</option>
    <option value="Maryland">Maryland</option>
    <option value="Massachusetts">Massachusetts</option>
    <option value="Michigan">Michigan</option>
    <option value="Minnesota">Minnesota</option>
    <option value="Mississippi">Mississippi</option>
    <option value="Missouri">Missouri</option>
    <option value="Montana">Montana</option>
    <option value="Nebraska">Nebraska</option>
    <option value="Nevada">Nevada</option>
    <option value="New Hampshire">New Hampshire</option>
    <option value="New Jersey">New Jersey</option>
    <option value="New Mexico">New Mexico</option>
    <option value="New York">New York</option>
    <option value="North Carolina">North Carolina</option>
    <option value="North Dakota">North Dakota</option>
    <option value="Ohio">Ohio</option>
    <option value="Oklahoma">Oklahoma</option>
    <option value="Oregon">Oregon</option>
    <option value="Pennsylvania">Pennsylvania</option>
    <option value="Rhode Island">Rhode Island</option>
    <option value="South Carolina">South Carolina</option>
    <option value="South Dakota">South Dakota</option>
    <option value="Tennessee">Tennessee</option>
    <option value="Texas">Texas</option>
    <option value="Utah">Utah</option>
    <option value="Vermont">Vermont</option>
    <option value="Virginia">Virginia</option>
    <option value="Washington">Washington</option>
    <option value="West Virginia">West Virginia</option>
    <option value="Wisconsin">Wisconsin</option>
    <option value="Wyoming">Wyoming</option>

  </select>
  <br />

  <!-- This is the input that will allow the user to upload an image from their local machine onchange="img_check()" image/*,
    The accept attribute limits the files the user can select from we will set this to only accept JPEG-->
  Image Evidence: <input type="file" name="upload_image" accept=".jpg, .png" id="image">
  <!-- This will need a helper function to ensure that the file selected was of the correct type -->
  <br />

  <!-- This is a textbox for describing why the ticket was given -->
  Violation Descrption: <input type="text" name="description">

  <input type="submit" name="upload"  value="Add to Database">
</form>
<!--  The javascript would be used to verify whether or not the uploaded file is the correct type I don't
  know if this is needed given the accept attribute
<script type="text/javascript">
  function img_check(){
    alert("in function");
    var img_type = document.getElementById("image");
    console.log(img_type);
    var result = img_type.type();
    console.log(result);
    if(result){
      alert("Acceptable image type for our system");
    }
    else{
      alert("Only JPEG images are acceptible");
      document.getElementById("image").value()="";
    }
  }
</script> -->

<br/>



</body>
</html>

