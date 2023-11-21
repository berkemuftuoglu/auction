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
        // NOTE: Check for required fields to be not null & compare with not null values in the database!
        // NOTE: Check that text fields only have text inside.
        // NOTE: Fix echo message to be redirected onto the same page!
        //******************************************************************************/

        if (empty($_POST['auctionTitle']) || empty($_POST['itemName']) ||
        empty($_POST['auctionCategory']) || empty($_POST['itemColour']) ||
        empty($_POST['itemCondition']) || empty($_POST['auctionStartPrice']) ||
        empty($_POST['auctionEndDate']) ) {
                //$errorMessage = "Please fill in all required fields.";
                echo "Error: Requiered fields is empty";
        }
    }



/* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */

    // *********************************
    // Retrieve data from the form
    // *********************************

    // Access the value of the data from the $_POST array
    $name = $_POST['itemName'];
    $description = $_POST['auctionDetails'];
    $category = $_POST['auctionCategory'];
    $colour = $_POST['itemColour'];
    $condition = $_POST['itemCondition'];
    $start_time = date("Y-m-d H:i:s"); // Get the current date and time;
    $end_time = $_POST['auctionEndDate'];
    $auction_title = $_POST['auctionTitle'];
    $reserve_price = $_POST['auctionReservePrice'];
    $starting_price = $_POST['auctionStartPrice'];

    // Now $X contains the value entered in the 'X' field
    echo "NameX: " . $name . "<br>";
    echo "Description: " . $description . "<br>";
    echo "Category: " . $category . "<br>";
    echo "Colour: " . $colour . "<br>";
    echo "Condition: " . $condition . "<br>";
    echo "Start Time: " . $start_time . "<br>";
    echo "End Time: " . $end_time . "<br>";
    echo "Auction Title: " . $auction_title . "<br>";
    echo "Reserve Price: " . $reserve_price . "<br>";
    echo "Starting Price: " . $starting_price . "<br>";

    // Perform validation or additional processing if needed

    // Insert data into the database
    // $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

    // *********************************

    // Insert into Item table
    $item_query = "INSERT INTO Item (name, description, category, colour, `condition`)
    VALUES ('$name', '$description', '$category', '$colour', '$condition')";

    $item_result = db_query($connection, $item_query);

    if ($item_result) {
        // Get the last inserted item_id
        $item_id = mysqli_insert_id($connection);

        // Insert into Auction table
        $auction_query = "INSERT INTO Auction (item_id, start_time, end_time, auction_title, reserve_price, starting_price)
            VALUES ('$item_id', '$start_time', '$end_time', '$auction_title', '$reserve_price', '$starting_price')";

        $auction_result = db_query($connection, $auction_query);

        if ($auction_result) {
            echo "Item and Auction data inserted successfully!"; 
        } 
        else { 
            echo "Error inserting data into Auction table: " . mysqli_error($connection);
        }

        } 
    else {
            echo "Error inserting data into Item table: " . mysqli_error($connection);
        }

    // Close db connection
    db_disconnect($connection);

    // *********************************

            

// If all is successful, let user know.
echo('<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>');


?>

</div>


<?php include_once("footer.php")?>