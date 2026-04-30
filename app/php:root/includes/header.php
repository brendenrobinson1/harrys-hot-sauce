<header>
  <div id="logo">
      <img src="images/logo.png" alt="Harry's Hot Sauce"/>
  </div>
  



    <div id="cart-search">
    <div style="width: 198px; height: 27px; float:left;"></div>
    <!--<input type="search" placeholder="ProductSearch:/>-->
    <div>
    <p class="displayname">
      <?php 
      if (isset($_SESSION[ 'Login_id'])) {
        // Select all information from accounts where it equals to the session login id
        $query = "SELECT * FROM `Accounts`, Login WHERE `Account_id` = Login_account_id and Login_id = '".$_SESSION['Login_id']."'";
        if (!$result = mysqli_query($link, $query))
          {
            $result = 0;
            echo ('Error executing query: ' .mysqli_errno($link). " - ".mysqli_error($link). "<BR>");
          }
          else {
            $row = mysqli_fetch_array($result, MYSQLI_BOTH);
            // Putting accounts table columns in variables
            $firstName = $row['Account_firstname'];
            $accountID = $row['Account_id'];
            echo "Welcome ".$firstName;
          }
      }
      ?>
      </p>
    </div>
    <div id="cart">
    <p class="cart">
    <?php
 // Items for cart selecting the sum of all items for the account session and displaying them
$accountID = (int)$accountID;  // <-- add this one line
$query8 = "SELECT SUM(SC_order_quantity) FROM `Shopping_cart` WHERE `SC_account_id` = $accountID";
if (!$result = mysqli_query($link, $query8) and
isset($_SESSION['Login_id']))
      {
        $result = 0;
        echo('Error retrieving cart info');
      }
      else
      {
    $row8 = mysqli_fetch_array($result, MYSQLI_BOTH);
    
    if ($row8['SUM(SC_order_quantity)'] > 0)
    {
      // If the sum quantity is greater than zero display total
      print $row8['SUM(SC_order_quantity)'].' items';
    }
      
    else 
    {
      // If the items are less than zero then display cart is empty
      print 'Your cart is empty';
    }
  }
?>
</p>
</div>
    </div>
    <?php
    if (loggedin())
      include 'includes/navigation.php';
    else
          include 'includes/nav.php'; 
        ?>
</header>