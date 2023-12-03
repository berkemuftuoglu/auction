<?php include_once("header.php")?>
<?php include_once("database.php") ?>

<div class="container my-5">

<?php

// This function takes the form data and adds the new auction to the database.

/* TODO #1: Connect to MySQL database (perhaps by requiring a file that
            already does this). */

    // Create database connection
    $connection = db_connect();

/* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'], 
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        //*******************************************************************************/
        // NOTE: Provide helpful feedback to the user!
        //******************************************************************************/

        if (empty($_POST['auctionTitle']) || empty($_POST['itemName']) || 
            empty($_POST['auctionEndDate']) || empty($_POST['itemCondition']) ||
            empty($_POST['auctionStartPrice']) || empty($_FILES["image"]["name"]) ) 
        
        {
            $errorMessage = "Error: Required fields are empty. Please fill in the following:";
            
            if (empty($_POST['auctionTitle'])) {
                $errorMessage .= "<br>- Auction Title";
            }
        
            if (empty($_POST['itemName'])) {
                $errorMessage .= "<br>- Item Name";
            }
        
            if (empty($_POST['auctionEndDate'])) {
                $errorMessage .= "<br>- Auction End Date";
            }
        
            if (empty($_POST['itemCondition'])) {
                $errorMessage .= "<br>- Item Condition";
            }

            if (empty($_POST['auctionStartPrice'])) {
                $errorMessage .= "<br>- Start Price";
            }

            if (empty($_FILES["image"]["name"])) {
                $errorMessage .= "<br>- image name";
            }
    
        
            echo '<div class="alert alert-danger mt-3" role="alert">' . $errorMessage . '</div>';
            db_disconnect($connection);
            exit();
        }

        // Check if the end date is before today
        $today = date("Y-m-d H:i:s");
        if ($_POST['auctionEndDate'] < $today) {
            echo '<div class="alert alert-danger mt-3" role="alert">Error: End date cannot be before today.</div>';
            db_disconnect($connection);
            exit();
        }

        // ************************ check photo  ************************ 

        // File upload handling
        # echo $_FILES["image"]["name"];

        $targetDirectory = "photos/";
        $targetFile = $targetDirectory . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            echo '<div class="alert alert-danger mt-3" role="alert">Error: The uploaded file is not an image.</div>';
            $uploadOk = 0;
        }

        // Check file size (adjust the limit as needed)
        if ($_FILES["image"]["size"] > 500000) {
            echo '<div class="alert alert-danger mt-3" role="alert">Error: The uploaded file is too large.</div>';
            $uploadOk = 0;
        }

        // Allow certain file formats (adjust as needed)
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        if (!in_array($imageFileType, $allowedExtensions)) {
            echo '<div class="alert alert-danger mt-3" role="alert">Error: Invalid file format. Allowed formats: jpg, jpeg, png.</div>';
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo '<div class="alert alert-danger mt-3" role="alert">Error: File not uploaded.</div>';
            db_disconnect($connection);
            exit();
        } else {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                echo '<div class="alert alert-success mt-3" role="alert">File uploaded successfully!</div>';
            } else {
                echo '<div class="alert alert-danger mt-3" role="alert">Error uploading file.</div>';
                db_disconnect($connection);
                exit();
            }
        }
        

        // **************************************************************

    }



/* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */

    // Retrieve data from the form

    // Access the value of the data from the $_POST array
    $name = $_POST['itemName'];
    $description = !empty($_POST['auctionDetails']) ? $_POST['auctionDetails'] : NULL ; // Check if empty
    $category = !empty($_POST['auctionCategory']) ? $_POST['auctionCategory'] : "Other"; // Check if empty
    $colour = !empty($_POST['itemColour']) ? $_POST['itemColour'] : "Other"; // Check if empty
    $condition = $_POST['itemCondition'];
    $start_time = date("Y-m-d H:i:s"); // Get the current date and time;
    $end_time = $_POST['auctionEndDate'];
    $auction_title = $_POST['auctionTitle'];
    $reserve_price = !empty($_POST['auctionReservePrice']) ? $_POST['auctionReservePrice'] : 0; // Check if empty
    $starting_price = $_POST['auctionStartPrice'];

    $user_id = $_SESSION['user_id'];

    // *********************************************************
    // Note: Reserve price needs to be empty or else system will crash. 
    // *********************************************************

    // Now $X contains the value entered in the 'X' field
    echo '<div class="text-left mt-3">';
    echo '<h3>Entered Values:</h3>';
    echo "<p><strong>Name:</strong> " . $name . "</p>";
    echo "<p><strong>Description:</strong> " . $description . "</p>";
    echo "<p><strong>Category:</strong> " . $category . "</p>";
    echo "<p><strong>Colour:</strong> " . $colour . "</p>";
    echo "<p><strong>Condition:</strong> " . $condition . "</p>";
    echo "<p><strong>Start Time:</strong> " . $start_time . "</p>";
    echo "<p><strong>End Time:</strong> " . $end_time . "</p>";
    echo "<p><strong>Auction Title:</strong> " . $auction_title . "</p>";
    echo "<p><strong>Reserve Price:</strong> " . $reserve_price . "</p>";
    echo "<p><strong>Starting Price:</strong> " . $starting_price . "</p>";
    echo '</div>';

    // *****************************************************
    // Perform validation or additional processing if needed
    // *****************************************************

    $filePath = "/" . "photos/" . basename($_FILES["image"]["name"]);
    #echo 'File path = ' . $filePath;

    // Insert data into the database
    // ********************** Add photo to auction_query  **********************
    $item_query = "INSERT INTO Item (name, description, category, colour, `condition`, photo)
        VALUES ('$name', '$description', '$category', '$colour', '$condition', '$filePath')";

    // Execute the item query
    $item_result = db_query($connection, $item_query);

    // Check item values
    if ($item_result) {
            // echo "Item added to DB <br>";
            # echo '<div class="alert alert-success mt-3" role="alert"> Item data inserted successfully! </div>';

            // Get the last inserted item_id
            $item_id = mysqli_insert_id($connection);
 
            // Insert into Auction table
            $auction_query = "INSERT INTO Auction (user_id, item_id, start_time, end_time, auction_title, reserve_price, starting_price)
                VALUES ('$user_id', '$item_id', '$start_time', '$end_time', '$auction_title', '$reserve_price', '$starting_price')";

            $auction_result = db_query($connection, $auction_query);
            if ($auction_result) {
                #echo '<div class="alert alert-success mt-3" role="alert"> Auction data inserted successfully! </div>';
            } 
            else { 
                #echo '<div class="alert alert-danger mt-3" role="alert"> Error: adding data to auction table </div>';
                db_disconnect($connection);    
                exit();
                // error_log("Auction Insert Error: " . mysqli_error($connection));
            }
        } 
    else {
            #echo '<div class="alert alert-danger mt-3" role="alert"> Error: adding data to item table </div>';
            db_disconnect($connection);
            exit();
        }

    // Close db connection
    db_disconnect($connection);
            

//*******************************************************************************/
// If all is successful, let user know.
//*******************************************************************************/
// Check the success of the queries
if ($item_result && $auction_result) {

    // Create the listing page link
    $listingLink = "listing.php?item_id=$item_id";

    // Display the success message with the link
    echo '<div class="text-center">Auction successfully created! <a href="' . $listingLink . '">View your new listing.</a></div>';
    

} else {
    echo '<div class="text-center">Error creating auction. Please try again.</div>';
}


?>

</div>


<?php include_once("footer.php")?>