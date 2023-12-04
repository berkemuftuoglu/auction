<?php include_once("header.php");
require("utilities.php");
require("database.php"); ?>

<div class="container mt-5">
    <div class="row ">
        <?php

        $has_session = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
        $user_id = $_SESSION['user_id'];

        if (!$has_session) {
            echo ('<div class="text-center">Please Login.</div>');
            header("location: login.php");
            exit;
        }



        // Establish database connection
        $connection = db_connect();

        // TODO: Use user_id to make a query to the database.
        $watchlist_query = "SELECT
  i.item_id,
  i.name,
  i.description,
  i.category,
  i.colour,
  i.condition,
  i.photo
FROM
  watchlist w
JOIN
  item i ON w.item_id = i.item_id
WHERE
  w.user_id = $user_id;
";
        $watchlist_result = db_query($connection, $watchlist_query);

        // Check if item exists
        if (db_num_rows($watchlist_result) == 0) {
            echo "<div>Error: Item not found.</div>";
            db_disconnect($connection);
            exit;
        }

        // Get item details
        while ($row = mysqli_fetch_assoc($watchlist_result)) {
            $item_id = $row['item_id'];
            $item_name = $row['name'];
            $item_description = $row['description'];
            $item_category = $row['category'];
            $item_colour = $row['colour'];
            $item_condition = $row['condition'];
            $item_photo = $row['photo'];

            if (strlen($item_description) > 250) {
                $desc_shortened = substr($item_description, 0, 250) . '...';
            } else {
                $desc_shortened = $item_description;
            }

        ?>



            <div class="col-md-4">
                <div class="card watchlist-item" data-itemid="<?php echo $item_id; ?>" style="width: 18rem;">
                    <img class="card-img-top" src="<?php echo $item_photo ?? "/photos/empty.png"; ?>" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title"><a href="listing.php?item_id=<?php echo $item_id; ?>"><?php echo $item_name; ?></a></h5>
                        <p class="card-text"><?php echo $desc_shortened; ?></p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Category:</strong> <?php echo $item_category; ?></li>
                        <li class="list-group-item"><strong>Color:</strong> <?php echo $item_colour; ?></li>
                        <li class="list-group-item"><strong>Condition:</strong> <?php echo $item_condition; ?></li>
                    </ul>
                    <div class="card-body">
                        <a href="#" onclick="removeFromWatchlist(event)" class="card-link" data-userid="<?php echo $user_id; ?>" data-itemid="<?php echo $item_id; ?>">Remove from Watchlist</a>
                    </div>
                </div>
            </div>

        <?php

        }
        include_once("footer.php") ?>
    </div>
</div>

<script>
    // JavaScript functions: and removeFromWatchlist.

    function removeFromWatchlist(event) {
        // This performs an asynchronous call to a PHP function using POST method.
        // Sends item ID as an argument to that function.
        var userId = event.target.getAttribute('data-userid');
        var itemId = event.target.getAttribute('data-itemid');

        var itemCard = $('.watchlist-item[data-itemid="' + itemId + '"]');
        $.ajax({
            url: 'watchlist_funcs.php',
            type: "POST",
            data: {
                functionname: 'remove_from_watchlist',
                user_id: userId,
                item_id: itemId
            },

            success: function(obj, textstatus) {
                // Callback function for when call is successful and returns obj
                var objT = obj.trim();
                console.log(objT);

                if (objT == "success") {
                    itemCard.hide();
                } else {
                    console.log("I am Having Error", objT);
                }
            },

            error: function(obj, textstatus) {
                console.log("Error");
            }
        }); // End of AJAX call

    } // End of addToWatchlist func
</script>