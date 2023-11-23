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
            empty($_POST['auctionStartPrice']) ) 
        
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
        
            echo $errorMessage;
            db_disconnect($connection);
            exit();
        }
            
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

    // *********************************************************
    // Note: Reserve price needs to be empty or else system will crash. 
    // *********************************************************

    // Now $X contains the value entered in the 'X' field
    echo "Name: " . $name . "<br>";
    echo "Description: " . $description . "<br>";
    echo "Category: " . $category . "<br>";
    echo "Colour: " . $colour . "<br>";
    echo "Condition: " . $condition . "<br>";
    echo "Start Time: " . $start_time . "<br>";
    echo "End Time: " . $end_time . "<br>";
    echo "Auction Title: " . $auction_title . "<br>";
    echo "Reserve Price: " . $reserve_price . "<br>";
    echo "Starting Price: " . $starting_price . "<br>";

    // *****************************************************
    // Perform validation or additional processing if needed
    // *****************************************************

    // Insert data into the database
    $item_query = "INSERT INTO Item (name, description, category, colour, `condition`)
    VALUES ('$name', '$description', '$category', '$colour', '$condition')";

    // Execute the item query
    $item_result = db_query($connection, $item_query);

    // Check item values
    if ($item_result) {
        echo "Item added to DB <br>";

        // Get the last inserted item_id
        $item_id = mysqli_insert_id($connection);

        // Insert into Auction table
        $auction_query = "INSERT INTO Auction (item_id, start_time, end_time, auction_title, reserve_price, starting_price)
            VALUES ('$item_id', '$start_time', '$end_time', '$auction_title', '$reserve_price', '$starting_price')";

        $auction_result = db_query($connection, $auction_query);
        if ($auction_result) {
            echo "Auction data inserted successfully!"; 
        } 
        else { 
            echo "Error inserting data into Auction table: " . mysqli_error($connection);
            db_disconnect($connection);    
            exit();
            // error_log("Auction Insert Error: " . mysqli_error($connection));
        }

        } 
    else {
            echo "Error inserting data into Item table: " . mysqli_error($connection);
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
    echo '<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>';
} else {
    echo '<div class="text-center">Error creating auction. Please try again.</div>';
}


?>

</div>


<?php include_once("footer.php")?>