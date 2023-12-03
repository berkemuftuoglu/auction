<?php include_once("header.php")?>

<?php
/* (Uncomment this block to redirect people without selling privileges away from this page)
  // If user is not logged in or not a seller, they should not be able to
  // use this page.
  if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'seller') {
    header('Location: browse.php');
  }
*/
?>

<div class="container">

<!-- Create auction form -->
<div style="max-width: 800px; margin: 10px auto">
  <h2 class="my-3">Create new auction</h2>
  <div class="card">
    <div class="card-body">
      <!-- Note: This form does not do any dynamic / client-side / 
      JavaScript-based validation of data. It only performs checking after 
      the form has been submitted, and only allows users to try once. You 
      can make this fancier using JavaScript to alert users of invalid data
      before they try to send it, but that kind of functionality should be
      extremely low-priority / only done after all database functions are
      complete. -->

      <form method="post" action="create_auction_result.php" enctype="multipart/form-data">

      <!--****************************************************************-->
      <!--********************** Auction Title ***************************-->
      <!--****************************************************************-->

        <div class="form-group row">
          <label for="auctionTitle" class="col-sm-2 col-form-label text-right">Title of auction</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="auctionTitle" name="auctionTitle" placeholder="e.g. Black mountain bike">
            <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> A short description of the item you're selling, which will display in listings.</small> 
          </div>
        </div>

      <!--****************************************************************-->
      <!--********************** Item Name *******************************-->
      <!--****************************************************************-->

      <div class="form-group row">
          <label for="itemName" class="col-sm-2 col-form-label text-right">Name of Item</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="itemName" name="itemName" placeholder="e.g. insert item name">
            <small id="nameHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> A name you wish to give your item.</small>
          </div>
      </div>

      <!--****************************************************************-->
      <!--********************** AuctionDetails *************************-->
      <!--****************************************************************-->

        <div class="form-group row">
          <label for="auctionDetails" class="col-sm-2 col-form-label text-right">Details</label>
          <div class="col-sm-10">
            <textarea class="form-control" id="auctionDetails" name="auctionDetails" rows="4"></textarea>
            <small id="detailsHelp" class="form-text text-muted">Optional.</small>
            
          </div>
        </div>

      <!--****************************************************************-->
      <!--********************** AuctionCategory *************************-->
      <!--****************************************************************-->

        <div class="form-group row">
          <label for="auctionCategory" class="col-sm-2 col-form-label text-right">Category</label>
          <div class="col-sm-10">
            <select class="form-control" id="auctionCategory" name="auctionCategory">
              <option selected disabled>Choose...</option>
              <option value="Electronics">Electronics</option>
              <option value="Fashion">Fashion</option>
              <option value="Home">Home</option>
              <option value="Books">Books</option>
              <option value="Other">Other</option>
            </select>
            <small id="AuctionCategoryHelp" class="form-text text-muted">Optional.</small>
           </div>
        </div>

      <!--****************************************************************-->
      <!--********************** ItemColour ******************************-->
      <!--****************************************************************-->

      <div class="form-group row">
          <label for="itemColour" class="col-sm-2 col-form-label text-right">Colour</label>
          <div class="col-sm-10">
              <select class="form-control" id="itemColour" name="itemColour">
                  <option selected disabled>Choose...</option>
                  <?php
                  $colors = ['Red', 'Orange', 'Yellow', 'Green', 'Blue', 'Purple', 'Pink', 'White', 'Grey', 'Black', 'Brown', 'Other'];
                  foreach ($colors as $color) {
                      echo "<option value=\"$color\">$color</option>";
                  }
                  ?>
              </select>
              <small id="colourHelp" class="form-text text-muted">Optional.</small>
          </div>
        </div>
      
      <!--****************************************************************-->
      <!--********************** ItemConditiom ***************************-->
      <!--****************************************************************--> 

      <div class="form-group row">
          <label for="itemCondition" class="col-sm-2 col-form-label text-right">Condition</label>
          <div class="col-sm-10">
              <select class="form-control" id="itemCondition" name="itemCondition">
                  <option selected disabled>Choose...</option>
                  <option value="Great">Great</option>
                  <option value="Good">Good</option>
                  <option value="Okay">Okay</option>
                  <option value="Poor">Poor</option>
              </select>
              <small id="conditionHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> State the condition of the item</small>
          </div>
      </div>

      <!--****************************************************************-->
      <!--********************** Starting Price **************************-->
      <!--****************************************************************--> 

        <div class="form-group row">
          <label for="auctionStartPrice" class="col-sm-2 col-form-label text-right">Starting price</label>
          <div class="col-sm-10">
	        <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" class="form-control" id="auctionStartPrice" name="auctionStartPrice">
            </div>
            <small id="startBidHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Initial bid amount.</small>
          </div>
        </div>

      <!--****************************************************************-->
      <!--********************** Reserve Price ***************************-->
      <!--****************************************************************--> 

        <div class="form-group row">
          <label for="auctionReservePrice" class="col-sm-2 col-form-label text-right">Reserve price</label>
          <div class="col-sm-10">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" class="form-control" id="auctionReservePrice" name="auctionReservePrice">
            </div>
            <small id="reservePriceHelp" class="form-text text-muted">Optional. Auctions that end below this price will not go through. This value is not displayed in the auction listing.</small>
          </div>
        </div>

      <!--****************************************************************-->
      <!--********************** End Date ********************************-->
      <!--****************************************************************--> 

        <div class="form-group row">
          <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
          <div class="col-sm-10">
            <input type="datetime-local" class="form-control" id="auctionEndDate" name="auctionEndDate">
            <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Day for the auction to end.</small>
          </div>
        </div>


      <!--****************************************************************-->
      <!--********************** Photo Attach ****************************-->
      <!--****************************************************************--> 

      <div class="form-group row">
          <label for="uploadImage" class="col-sm-2 col-form-label text-right">Upload Image</label>
          <div class="col-sm-10">
            <input type="file" name="image" id="uploadImage">
            <small id="uploadImage" class="form-text text-muted"> <span class="text-danger">* Required.</span> Allowed file types: jpg, png, jpeg</small>
          </div>
      </div>

      <!--****************************************************************--> 
      <!--****************************************************************--> 

        <button type="submit" class="btn btn-primary form-control">Create Auction</button>
      </form>


    </div>
  </div>
</div>
</div>




<?php include_once("footer.php")?>