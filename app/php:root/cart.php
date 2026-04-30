<?php
include 'includes/core.php';
include 'includes/connection.php';
date_default_timezone_set('America/New_York');
error_reporting(E_ERROR | E_PARSE);
?>

<?php
if (loggedin()) {
  $_SESSION['Login_username'] = getuserfield('Login_username');
  $LoginID = $_SESSION['Login_id'];

  // Select account information
  $query = "SELECT * FROM `Accounts`, `Login`
            WHERE `Account_id` = `Login_account_id`
            AND `Login_id` = '".$_SESSION['Login_id']."'";

  if (!$result = mysqli_query($link, $query))
  {
    $result = 0;
    echo ('Error executing query1: ' . mysqli_errno($link) . " - " .
    mysqli_error($link). "<BR>");
  }
  else
  {
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);
    $firstName = $row['Account_firstname'];
    $accountID = $row['Account_id'];

    // Selecting all from inventory
    $query2 = "SELECT * FROM `Inventory`";
    if (!$result2 = mysqli_query($link, $query2))
    {
      $result2 = 0;
      echo ('Error executing query2: ' . mysqli_errno($link) . " - " .
      mysqli_error($link). "<BR>");
    }
    else
    {
      $row2 = mysqli_fetch_array($result2, MYSQLI_BOTH);
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Harry's Hot Sauce</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<link href="css/layout.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="wrapper">
  <?php include 'includes/header.php'; ?>

  <div id="body-wrapper">
    <?php
    // Handle remove first
    if (isset($_POST['remove']))
    {
      $id = intval($_POST['id']);

      $query3 = "DELETE FROM `Shopping_cart`
                 WHERE `SC_inventory_id` = '$id'
                 AND `SC_account_id` = '$accountID'";

      if (!$query_run3 = mysqli_query($link, $query3))
      {
        echo ('Error executing query3: ' . mysqli_errno($link) . " - " .
        mysqli_error($link). "<BR>");
      }
      else
      {
        header("Location: cart.php");
        exit();
      }
    }

    // Handle quantity update
    if (isset($_POST['updateQuan']))
    {
      $id = intval($_POST['id']);
      $quantityOrdered = intval($_POST['quantityOrdered']);

      if ($quantityOrdered != 0)
      {
        $query19 = "UPDATE `Shopping_cart`
                    SET `SC_order_quantity` = '".$quantityOrdered."',
                    `SC_last_updated_date` = now(),
                    `SC_last_updated_by` = 'Update Cart (Updated)'
                    WHERE `SC_account_id` = '$accountID'
                    AND `SC_inventory_id` = '$id'";

        if (!$query_run19 = mysqli_query($link, $query19))
        {
          echo ('Error executing query19: ' . mysqli_errno($link) . " - " .
          mysqli_error($link). "<BR>");
        }
      }
      else
      {
        $query3 = "DELETE FROM `Shopping_cart`
                   WHERE `SC_inventory_id` = '$id'
                   AND `SC_account_id` = '$accountID'";

        if (!$query_run3 = mysqli_query($link, $query3))
        {
          echo ('Error executing query3: ' . mysqli_errno($link) . " - " .
          mysqli_error($link). "<BR>");
        }
      }

      header('Location: cart.php');
      exit();
    }

    // Set shipping method before building dropdown
    $shippingMethod = '';
    $shippingCost = 0.00;

    if (isset($_POST['shippingMethod']))
    {
      $shippingMethod = $_POST['shippingMethod'];
    }
    elseif (isset($_SESSION['ShippingMethod']))
    {
      $shippingMethod = $_SESSION['ShippingMethod'];
    }
    else
    {
      $shippingMethod = 'USPS';
    }

    // Selecting all from the shopping cart and products
    $query = "SELECT * FROM `Shopping_cart`, `Products`, `Inventory`
              WHERE `SC_account_id` = $accountID
              AND `SC_inventory_id` = `Inventory_id`
              AND `Inventory_product_id` = `Products_id`
              ORDER BY `SC_inventory_id`";

    if (!$result = mysqli_query($link, $query))
    {
      $result = 0;
      echo ('Error executing query: ' . mysqli_errno($link) . " - " .
      mysqli_error($link). "<BR>");
    }
    else
    {
      print '<div style="width:225px; height:40px; float:left; line-height:40px; font-size:18px; font-weight:bold;">Product Name</div>';
      print '<div style="width:105px; height:40px; float:left; line-height:40px; font-size:18px; font-weight:bold;">Quantity</div>';
      print '<div style="width:115px; height:40px; float:left; line-height:40px; font-size:18px; font-weight:bold;">Unit Price</div>';
      print '<div style="width:105px; height:40px; float:left; line-height:40px; font-size:18px; font-weight:bold;">Subtotal</div>';

      $total = 0;

      while ($row = mysqli_fetch_assoc($result))
      {
        $inventoryRemoveQuan = $row['SC_order_quantity'];

        print '<form method="post" action="">';
        print '<br>';
        print '<br>';
        print '<div style="clear:both;"></div>';
        print '<hr class="full-line">';
        print '<input type="hidden" value="'.$row['SC_inventory_id'].'" name="id"/>';
        print '<div style="width:235px; height:40px; float:left; line-height:40px;">'.$row['Products_name'].'</div>';
        print '<div style="width:110px; height:40px; float:left; line-height:40px;"><input type="text" name="quantityOrdered" value="'.$inventoryRemoveQuan.'" style="width:40px; height:20px;"></div>';
        print '<div style="width:110px; height:40px; float:left; line-height:40px;">$'.number_format($row['SC_unit_price'], 2).'</div>';

        $subTotal = $inventoryRemoveQuan * $row['SC_unit_price'];

        print '<div style="width:105px; height:40px; float:left; line-height:40px;">$'.number_format($subTotal, 2).'</div>';
        print '<div style="width:150px; height:40px; float:left;"><input type="submit" name="remove" value="Remove item" class="removeItem"></div>';
        print '<div style="width:150px; height:40px; float:left;"><input type="submit" name="updateQuan" value="Update Quantity" class="updateItem"></div>';
        print '<br>';
        print '<br>';
        print '<br>';
        print '<div style="clear:both;"></div>';
        print '<hr class="full-line">';
        print '</form>';

        $total += $subTotal;
      }

      // Build shipping dropdown
      $queryShip = "SELECT `GL_name`, `GL_data`
                    FROM `General_lookup`
                    WHERE `GL_type` = 'Shipping Cost'
                    ORDER BY `GL_name`";

      if (!$resultShip = mysqli_query($link, $queryShip))
      {
        echo ('Error executing shipping query: ' . mysqli_errno($link) . " - " .
        mysqli_error($link) . "<BR>");

        $shippingBox = '<select name="shippingMethod">';
        $shippingBox .= '<option value="USPS" selected>USPS - $0.00</option>';
        $shippingBox .= '</select>';
      }
      else
      {
        $shippingBox = '<select name="shippingMethod">';
        $foundShipping = 0;

        while ($rowShip = mysqli_fetch_array($resultShip, MYSQLI_BOTH))
        {
          $foundShipping = 1;
          $selected = ($shippingMethod == $rowShip['GL_name']) ? ' selected' : '';
          $shippingBox .= '<option value="'.$rowShip['GL_name'].'"'.$selected.'>'.$rowShip['GL_name'].' - $'.number_format((float)$rowShip['GL_data'], 2).'</option>';
        }

        if ($foundShipping == 0)
        {
          $shippingBox .= '<option value="USPS" selected>USPS - $0.00</option>';
        }

        $shippingBox .= '</select>';
      }

      // Get shipping cost for selected method
      $queryShipCost = "SELECT `GL_data`
                        FROM `General_lookup`
                        WHERE `GL_type` = 'Shipping Cost'
                        AND `GL_name` = '$shippingMethod'
                        LIMIT 1";

      if (!$resultShipCost = mysqli_query($link, $queryShipCost))
      {
        echo ('Error executing shipping cost query: ' . mysqli_errno($link) . " - " .
        mysqli_error($link) . "<BR>");
        $shippingCost = 0.00;
      }
      else
      {
        if ($rowShipCost = mysqli_fetch_array($resultShipCost, MYSQLI_BOTH))
        {
          $shippingCost = (float)$rowShipCost['GL_data'];
        }
        else
        {
          $shippingCost = 0.00;
        }
      }

      $_SESSION['ShippingMethod'] = $shippingMethod;
      $_SESSION['ShippingCost'] = $shippingCost;

      // Handle shipping update button
      if (isset($_POST['updateShipping']))
      {
        $_SESSION['ShippingMethod'] = $shippingMethod;
        $_SESSION['ShippingCost'] = $shippingCost;
        header('Location: cart.php');
        exit();
      }

      // Handle checkout button
      if (isset($_POST['submitCheckout']) && $total != 0)
      {
        $_SESSION['ShippingMethod'] = $shippingMethod;
        $_SESSION['ShippingCost'] = $shippingCost;
        header('Location: checkout.php');
        exit();
      }

      print '<br>';
      print '<br>';
      print '<br>';
      print 'Purchases $ '.number_format($total, 2);
      print '<br>';
      print '<br>';

      print '<form action="" method="post">';
      print 'Shipping Method '.$shippingBox;
      print ' <input type="submit" name="updateShipping" value="Update Shipping" class="updateItem"/>';
      print '<br>';
      print '<br>';
      print 'Shipping $ '.number_format($shippingCost, 2);
      print '<br>';
      print '<br>';
      print 'Total $ '.number_format($total + $shippingCost, 2);
      print '<br>';
      print '<br>';
      print '<input type="submit" name="submitCheckout" value="Checkout" class="test"/>';
      print '</form>';
    }
    ?>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/signout.php'; ?>
</body>
</html>
<?php
} else {
  header('Location: login.php');
}
?>