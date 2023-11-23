<?php include_once("header.php")?>
<?php include_once("database.php") ?>

<div class="container my-5">

<?php

/* 

CREATE TABLE Item (
    item_id INT(11) AUTO_INCREMENT PRIMARY KEY,
**********************    name VARCHAR(255) NOT NULL,
    description VARCHAR(511),
**********************        category ENUM('Electronics', 'Fashion', 'Home', 'Books', 'Other') NOT NULL,
    colour ENUM('Red', 'Orange', 'Yellow', 'Green', 'Blue', 'Purple', 'Pink', 'White', 'Grey', 'Black', 'Brown', 'Other'),
    `condition` ENUM('Great', 'Good', 'Okay', 'Poor'),
    photo VARCHAR(255) -- filepath
);

CREATE TABLE Auction (
    auction_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    item_id INT(11),
**********************        start_time DATETIME NOT NULL,
**********************        end_time DATETIME NOT NULL, -- if end time in earlier than current time, then the auction is over
    auction_title VARCHAR(255),
    reserve_price FLOAT(2),
    starting_price FLOAT(2),
    FOREIGN KEY (item_id) REFERENCES Item(item_id)
);

*/

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
        // NOTE: Check for required fields to be not null synchronize that with the database!
        // NOTE: Check that text fields only have text inside.
        // NOTE: Fix echo message to be redirected onto the same page!
        // NOTE: Provide helpful feedback to the user!
        //******************************************************************************/

        if (empty($_POST['auctionTitle']) || empty($_POST['itemName']) ||
            empty($_POST['auctionCategory']) || empty($_POST['itemCondition']) ||
            empty($_POST['auctionStartPrice']) || empty($_POST['auctionEndDate']) ||
            empty($_POST['itemColour'])
            
            ) {
                // $errorMessage = "Error: Requiered fields is empty <br> ";
                // echo "Error: Requiered fields is empty <br> ";
        }
    }



/* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */

    // Retrieve data from the form

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

    $item_result = db_query($connection, $item_query);

    echo "Item added to DB <br>";

    if ($item_result) {
        // Get the last inserted item_id
        $item_id = mysqli_insert_id($connection);

        // Insert into Auction table
        $auction_query = "INSERT INTO Auction (item_id, start_time, end_time, auction_title, reserve_price, starting_price)
            VALUES ('$item_id', '$start_time', '$end_time', '$auction_title', '$reserve_price', '$starting_price')";

        $auction_result = db_query($connection, $auction_query);
        echo "Auction added to DB <br>";

        if ($auction_result) {
            echo "Item and Auction data inserted successfully!"; 
        } 
        else { 
            echo "Error inserting data into Auction table: " . mysqli_error($connection);
            // error_log("Auction Insert Error: " . mysqli_error($connection));
        }

        } 
    else {
            echo "Error inserting data into Item table: " . mysqli_error($connection);
            // error_log("Auction Insert Error: " . mysqli_error($connection));
        }

    // Close db connection
    db_disconnect($connection);
            

//*******************************************************************************/
// If all is successful, let user know.
//*******************************************************************************/
echo('<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>');


?>

</div>


<?php include_once("footer.php")?>