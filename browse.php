<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php require("database.php")?>

<div class="container">

<h2 class="my-3">Browse listings</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
<form method="get" action="browse.php">
  <div class="row">
    <div class="col-md-5 pr-0">
      <div class="form-group">
        <label for="keyword" class="sr-only">Search keyword:</label>
	    <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text bg-transparent pr-0 text-muted">
              <i class="fa fa-search"></i>
            </span>
          </div>
          <input type="text" class="form-control border-left-0" name="keyword" id="keyword" placeholder="Search for anything">
        </div>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-group">
        <label for="cat" class="sr-only">Search within:</label>
        <select class="form-control" name="cat" id="cat">
          <option selected name="all" value="all">All categories</option>
          <?php
            $connection = db_connect();
            $categories = "SELECT DISTINCT category FROM item";
            $result_categories = db_query($connection, $categories);
            confirm_result_set($result_categories);
            while ($row = mysqli_fetch_array($result_categories)) {
              echo '<option name=' . $row[0] . ' value=' . $row[0] . '>' . $row[0] . '</option>';
            }
            db_free_result($result_categories);
            db_disconnect($connection);
          ?>
        </select>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select class="form-control" name="order_by" id="order_by">
          <option selected name="pricelow" value="pricelow">Price (low to high)</option>
          <option name="pricehigh" value="pricehigh">Price (high to low)</option>
          <option name="date" value="date">Soonest expiry</option>
        </select>
      </div>
    </div>
    <div class="col-md-1 px-0">
      <button type="submit" class="btn btn-primary">Search</button>
    </div>
  </div>
</form>
</div> <!-- end search specs bar -->


</div>

<?php
  // Retrieve these from the URL
  if (!isset($_GET['keyword'])) {
    // TODO: Define behavior if a keyword has not been specified.
    $keyword = '';
  }
  else {
    $keyword = $_GET['keyword'];
  }

  if (!isset($_GET['cat'])) {
    // TODO: Define behavior if a category has not been specified.
    $category = 'all';
  }
  else {
    $category = $_GET['cat'];
  }
  
  if (!isset($_GET['order_by'])) {
    // TODO: Define behavior if an order_by value has not been specified.
    $ordering = 'pricelow';
  }
  else {
    $ordering = $_GET['order_by'];
  }
  
  if (!isset($_GET['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_GET['page'];
  }

  /* TODO: Use above values to construct a query. Use this query to 
  retrieve data from the database. (If there is no form data entered,
  decide on appropriate default value/default query to make. */
  
  /* For the purposes of pagination, it would also be helpful to know the
     total number of results that satisfy the above query */

  $connection = db_connect();
  $search = "SELECT DISTINCT item.item_id,
                             auction.auction_title,
                             item.description,
                             MAX(bids.price) AS highest_bid,
                             COUNT(bids.auction_id) AS num_bids,
                             auction.end_time,
                             item.category
             FROM item
             INNER JOIN auction
             ON item.item_id = auction.item_id
             LEFT JOIN bids
             ON auction.auction_id = bids.auction_id
             WHERE item.name LIKE '%$keyword%'
             OR item.description LIKE '%$keyword%'
             OR auction.auction_title LIKE '%$keyword%'
             GROUP BY item.item_id,
                      auction.auction_title,
                      auction.end_time";
  
  if($category != 'all') {
    $search .= " HAVING item.category = '$category'";
  };

  if($ordering == "pricelow") {
    $search .= " ORDER BY highest_bid ASC";
  } elseif($ordering == "pricehigh") {
    $search .= " ORDER BY highest_bid DESC";
  } elseif($ordering == "date") {
    // putting the already expired auctions at the bottom of the list;
    $search .= " ORDER BY auction.end_time <= CURRENT_TIMESTAMP(), auction.end_time ASC";
  };

  $search_results = db_query($connection, $search);
  confirm_result_set($search_results);

  $num_results = db_num_rows($search_results); // TODO: Calculate me for real
  $results_per_page = 10;
  $max_page = ceil($num_results / $results_per_page);
?>

<div class="container mt-5">

<!-- TODO: If result set is empty, print an informative message. Otherwise... -->
<?php
  if ($num_results == 0) {
    echo "No matching results";
  };
?>

<ul class="list-group">

<!-- TODO: Use a while loop to print a list item for each auction listing
     retrieved from the query -->
<?php 
  while($row = db_fetch_single($search_results)) {
    $item_id = $row["item_id"];
    $title = $row["auction_title"];
    $description = $row["description"];
    $current_price = $row["highest_bid"];
    $num_bids = $row["num_bids"];
    $end_date = new DateTime($row["end_time"]);
    print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
  };
db_free_result($search_results);
db_disconnect($connection);
?>

</ul>

<!-- Pagination for results listings -->
<nav aria-label="Search results pages" class="mt-5">
  <ul class="pagination justify-content-center">
  
<?php

  // Copy any currently-set GET variables to the URL.
  $querystring = "";
  foreach ($_GET as $key => $value) {
    if ($key != "page") {
      $querystring .= "$key=$value&amp;";
    }
  }
  
  $high_page_boost = max(3 - $curr_page, 0);
  $low_page_boost = max(2 - ($max_page - $curr_page), 0);
  $low_page = max(1, $curr_page - 2 - $low_page_boost);
  $high_page = min($max_page, $curr_page + 2 + $high_page_boost);
  
  if ($curr_page != 1) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');
  }
    
  for ($i = $low_page; $i <= $high_page; $i++) {
    if ($i == $curr_page) {
      // Highlight the link
      echo('
    <li class="page-item active">');
    }
    else {
      // Non-highlighted link
      echo('
    <li class="page-item">');
    }
    
    // Do this in any case
    echo('
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }
  
  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
  }
?>

  </ul>
</nav>


</div>



<?php include_once("footer.php")?>