<?php
include 'includes/core.php';
include 'includes/connection.php';
date_default_timezone_set('America/New_York');
error_reporting(E_ERROR | E_PARSE);

if (loggedin()) {

  $_SESSION['Login_username'] = getuserfield('Login_username');
  $LoginID = $_SESSION['Login_id'];

  $message = '';
  $selectedOrderNumber = 0;
  $orderHeaderID = 0;

  // Build pending orders dropdown.
  // In this database, Entered means Pending.
  $ordersBox = '<select name="orderNumberSelect" style="width:360px; height:30px;">';
  $ordersBox .= '<option value="0"> </option>';

  $queryOrders = "SELECT oh.Order_header_number, oh.Order_header_orderdate, os.OS_name
                  FROM `Order_headers` oh, `Order_status` os
                  WHERE oh.Order_header_status_id = os.OS_id
                  AND os.OS_name = 'Entered'
                  ORDER BY oh.Order_header_orderdate ASC";

  if (!$resultOrders = mysqli_query($link, $queryOrders))
  {
    echo ('Error executing orders query: ' . mysqli_errno($link) . ' - ' . mysqli_error($link) . '<BR>');
  }
  else
  {
    while ($rowOrders = mysqli_fetch_array($resultOrders, MYSQLI_BOTH))
    {
      $ordersBox .= '<option value="' . $rowOrders['Order_header_number'] . '">' .
      $rowOrders['Order_header_number'] . ' - Pending - ' .
      $rowOrders['Order_header_orderdate'] .
      '</option>';
    }
  }

  $ordersBox .= '</select>';

  // Find selected order.
  if (isset($_POST['submitFindOrder']))
  {
    $selectedOrderNumber = intval($_POST['orderNumberSelect']);
    $typedOrderNumber = intval($_POST['orderNumberTyped']);

    if ($typedOrderNumber > 0)
    {
      $selectedOrderNumber = $typedOrderNumber;
    }

    $_SESSION['ShippingOrderNumber'] = $selectedOrderNumber;
  }
  elseif (isset($_SESSION['ShippingOrderNumber']))
  {
    $selectedOrderNumber = $_SESSION['ShippingOrderNumber'];
  }

  // Change status without shipping.
  if (isset($_POST['submitUpdateStatus']))
  {
    $selectedOrderNumber = intval($_POST['selectedOrderNumber']);
    $_SESSION['ShippingOrderNumber'] = $selectedOrderNumber;

    $newStatusName = mysqli_real_escape_string($link, $_POST['orderStatus']);

    $queryStatusID = "SELECT `OS_id`
                      FROM `Order_status`
                      WHERE `OS_name` = '$newStatusName'
                      LIMIT 1";

    if ($resultStatusID = mysqli_query($link, $queryStatusID))
    {
      $rowStatusID = mysqli_fetch_array($resultStatusID, MYSQLI_BOTH);
      $newStatusID = $rowStatusID['OS_id'];

      $queryUpdateStatus = "UPDATE `Order_headers`
                            SET `Order_header_status_id` = '$newStatusID',
                                `Order_header_last_updated_date` = now(),
                                `Order_header_last_updated_by` = 'Shipping Page'
                            WHERE `Order_header_number` = '$selectedOrderNumber'";

      if (!$resultUpdateStatus = mysqli_query($link, $queryUpdateStatus))
      {
        $message = 'Error updating order status: ' . mysqli_errno($link) . ' - ' . mysqli_error($link);
      }
      else
      {
        unset($_SESSION['ShippingOrderNumber']);
        $selectedOrderNumber = 0;
        $message = 'Order status updated to ' . $newStatusName . '.';
      }
    }
  }

  // Ship order.
  if (isset($_POST['submitShipOrder']))
  {
    $selectedOrderNumber = intval($_POST['selectedOrderNumber']);
    $_SESSION['ShippingOrderNumber'] = $selectedOrderNumber;

    $shipMethod = mysqli_real_escape_string($link, $_POST['shippingMethod']);
    $freightCost = floatval($_POST['freightCost']);
    $handlingCost = 2.95;

    $queryShippedID = "SELECT `OS_id`
                       FROM `Order_status`
                       WHERE `OS_name` = 'Shipped'
                       LIMIT 1";
    if ($resultShippedID = mysqli_query($link, $queryShippedID))
    {
      $rowShippedID = mysqli_fetch_array($resultShippedID, MYSQLI_BOTH);
      $shippedStatusID = $rowShippedID['OS_id'];
    }

    $queryHeader = "SELECT `Order_header_id`
                    FROM `Order_headers`
                    WHERE `Order_header_number` = '$selectedOrderNumber'
                    LIMIT 1";
    if ($resultHeader = mysqli_query($link, $queryHeader))
    {
      $rowHeader = mysqli_fetch_array($resultHeader, MYSQLI_BOTH);
      $orderHeaderID = $rowHeader['Order_header_id'];
    }

    $queryDetails = "SELECT od.Order_details_id
                     FROM `Order_details` od
                     WHERE od.Order_details_header_id = '$orderHeaderID'
                     ORDER BY od.Order_details_line_number ASC";

    if ($resultDetails = mysqli_query($link, $queryDetails))
    {
      $lineCtr = 1;

      while ($rowDetails = mysqli_fetch_array($resultDetails, MYSQLI_BOTH))
      {
        $detailID = $rowDetails['Order_details_id'];

        $shippedDate = trim($_POST['shippedDate'][$detailID]);
        $shippedQty = intval($_POST['shippedQty'][$detailID]);
        $trackingInput = trim($_POST['trackingNumber'][$detailID]);

        if ($shippedDate == '')
        {
          $shippedDate = date('Y-m-d');
        }

        if ($shippedQty < 0)
        {
          $shippedQty = 0;
        }

        // Generate a tracking number only when the field is blank.
        if ($trackingInput == '')
        {
          $trackingNumber = 'TRK' . strtoupper(bin2hex(random_bytes(5)));
        }
        else
        {
          $trackingNumber = mysqli_real_escape_string($link, $trackingInput);
        }

        if ($lineCtr == 1)
        {
          $methodValue = $shipMethod;
          $freightValue = $freightCost;
          $handlingValue = $handlingCost;
        }
        else
        {
          $methodValue = '';
          $freightValue = 0.00;
          $handlingValue = 0.00;
        }

        $queryCheckShipping = "SELECT `Shipping_order_details_id`
                               FROM `Shipping`
                               WHERE `Shipping_order_details_id` = '$detailID'
                               LIMIT 1";

        if ($resultCheckShipping = mysqli_query($link, $queryCheckShipping))
        {
          if (mysqli_num_rows($resultCheckShipping) > 0)
          {
            $queryUpdateShipping = "UPDATE `Shipping`
                                    SET `Shipping_shipped_date` = '$shippedDate',
                                        `Shipping_tracking_number` = '$trackingNumber',
                                        `Shipping_shipped_quantity` = '$shippedQty',
                                        `Shipping_method` = '$methodValue',
                                        `Shipping_freight_costs` = '$freightValue',
                                        `Shipping_handling_costs` = '$handlingValue',
                                        `Shipping_last_updated_date` = now(),
                                        `Shipping_last_updated_by` = 'Shipping Page'
                                    WHERE `Shipping_order_details_id` = '$detailID'";

            if (!$resultUpdateShipping = mysqli_query($link, $queryUpdateShipping))
            {
              $message = 'Error updating shipping: ' . mysqli_errno($link) . ' - ' . mysqli_error($link);
            }
          }
          else
          {
            $queryInsertShipping = "INSERT INTO `Shipping`
                                    (
                                      `Shipping_order_details_id`,
                                      `Shipping_shipped_date`,
                                      `Shipping_tracking_number`,
                                      `Shipping_shipped_quantity`,
                                      `Shipping_method`,
                                      `Shipping_freight_costs`,
                                      `Shipping_handling_costs`,
                                      `Shipping_created_date`,
                                      `Shipping_created_by`,
                                      `Shipping_last_updated_date`,
                                      `Shipping_last_updated_by`
                                    )
                                    VALUES
                                    (
                                      '$detailID',
                                      '$shippedDate',
                                      '$trackingNumber',
                                      '$shippedQty',
                                      '$methodValue',
                                      '$freightValue',
                                      '$handlingValue',
                                      now(),
                                      'Shipping Page',
                                      now(),
                                      'Shipping Page'
                                    )";

            if (!$resultInsertShipping = mysqli_query($link, $queryInsertShipping))
            {
              $message = 'Error inserting shipping: ' . mysqli_errno($link) . ' - ' . mysqli_error($link);
            }
          }
        }
        else
        {
          $message = 'Error checking shipping row: ' . mysqli_errno($link) . ' - ' . mysqli_error($link);
        }

        $lineCtr++;
      }

      $_SESSION['ShippingMethod'] = $shipMethod;
      $_SESSION['ShippingCost'] = $freightCost + $handlingCost;

      $queryUpdateHeader = "UPDATE `Order_headers`
                            SET `Order_header_status_id` = '$shippedStatusID',
                                `Order_header_last_updated_date` = now(),
                                `Order_header_last_updated_by` = 'Shipping Page'
                            WHERE `Order_header_number` = '$selectedOrderNumber'";

      if (!$resultUpdateHeader = mysqli_query($link, $queryUpdateHeader))
      {
        $message = 'Error updating order to shipped: ' . mysqli_errno($link) . ' - ' . mysqli_error($link);
      }
      else
      {
        $_SESSION['ShippingSuccessMessage'] = 'Order shipped successfully.';
        unset($_SESSION['ShippingOrderNumber']);
        header('Location: shipping.php');
        exit();
      }
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
    <p class="product-name">Shipping</p>
    <br>

    <?php
    if (isset($_SESSION['ShippingSuccessMessage']))
    {
      print '<div class="success-msg">' . $_SESSION['ShippingSuccessMessage'] . '</div>';
      unset($_SESSION['ShippingSuccessMessage']);
    }
    elseif (!empty($message))
    {
      print '<p style="font-weight:bold; color:red; margin-bottom:15px;">' . $message . '</p>';
    }
    ?>

    <form action="" method="post">
      <div style="width:390px; float:left; margin-right:25px;">
        <strong>Select Pending Order:</strong><br>
        <?php print $ordersBox; ?>
      </div>

      <div style="width:300px; float:left; margin-right:25px;">
        <strong>Or Enter Order Number:</strong><br>
        <input type="text" name="orderNumberTyped" value="" style="width:280px;">
      </div>

      <div style="width:150px; float:left; padding-top:20px;">
        <input type="submit" name="submitFindOrder" value="Find Order" class="submit-reset">
      </div>
      <div style="clear:both;"></div>
    </form>

    <hr class="full-line">

    <?php
    if ($selectedOrderNumber > 0)
    {
      $orderFound = false;

      $queryOrderInfo = "SELECT oh.Order_header_id, oh.Order_header_orderdate, os.OS_name,
                         a.Account_firstname, a.Account_lastname
                         FROM `Order_headers` oh, `Order_status` os, `Accounts` a
                         WHERE oh.Order_header_status_id = os.OS_id
                         AND oh.Order_header_account_id = a.Account_id
                         AND oh.Order_header_number = '$selectedOrderNumber'
                         AND os.OS_name = 'Entered'
                         LIMIT 1";

      if ($resultOrderInfo = mysqli_query($link, $queryOrderInfo))
      {
        if ($rowOrderInfo = mysqli_fetch_array($resultOrderInfo, MYSQLI_BOTH))
        {
          $orderFound = true;
          $orderHeaderID = $rowOrderInfo['Order_header_id'];

          print '<p><strong>Order Number:</strong> ' . $selectedOrderNumber . '</p>';
          print '<p><strong>Customer:</strong> ' . $rowOrderInfo['Account_firstname'] . ' ' . $rowOrderInfo['Account_lastname'] . '</p>';
          print '<p><strong>Order Date:</strong> ' . $rowOrderInfo['Order_header_orderdate'] . '</p>';
          print '<p><strong>Current Status:</strong> Pending</p>';
          print '<br>';
        }
        else
        {
          unset($_SESSION['ShippingOrderNumber']);
          $selectedOrderNumber = 0;
        }
      }

      if ($orderFound)
      {
        $selectedShipMethod = '';
        $defaultFreight = '0.00';
        $defaultHandling = '2.95';
        $orderLines = array();

        $queryLines = "SELECT od.Order_details_id,
                              od.Order_details_line_number,
                              od.Order_details_ordered_quantity,
                              p.Products_name,
                              s.Shipping_shipped_date,
                              s.Shipping_tracking_number,
                              s.Shipping_shipped_quantity,
                              s.Shipping_method,
                              s.Shipping_freight_costs,
                              s.Shipping_handling_costs
                       FROM `Order_details` od
                       INNER JOIN `Inventory` i
                         ON od.Order_details_inventory_id = i.Inventory_id
                       INNER JOIN `Products` p
                         ON i.Inventory_product_id = p.Products_id
                       LEFT JOIN `Shipping` s
                         ON od.Order_details_id = s.Shipping_order_details_id
                       WHERE od.Order_details_header_id = '$orderHeaderID'
                       ORDER BY od.Order_details_line_number ASC";

        if ($resultLines = mysqli_query($link, $queryLines))
        {
          while ($rowLines = mysqli_fetch_array($resultLines, MYSQLI_BOTH))
          {
            if ($selectedShipMethod == '' && !empty($rowLines['Shipping_method']))
            {
              $selectedShipMethod = $rowLines['Shipping_method'];
            }

            if ($defaultFreight == '0.00' && $rowLines['Shipping_freight_costs'] !== null && $rowLines['Shipping_freight_costs'] !== '')
            {
              $defaultFreight = $rowLines['Shipping_freight_costs'];
            }

            $orderLines[] = $rowLines;
          }

          $shipBox = '<select name="shippingMethod" style="width:250px; height:30px;">';
          $queryShipMethods = "SELECT `GL_data`
                               FROM `General_lookup`
                               WHERE `GL_type` = 'Shipping Method'
                               ORDER BY `GL_data`";

          if ($resultShipMethods = mysqli_query($link, $queryShipMethods))
          {
            while ($rowShipMethods = mysqli_fetch_array($resultShipMethods, MYSQLI_BOTH))
            {
              $selectedText = ($rowShipMethods['GL_data'] == $selectedShipMethod) ? ' selected' : '';
              $shipBox .= '<option value="' . $rowShipMethods['GL_data'] . '"' . $selectedText . '>' . $rowShipMethods['GL_data'] . '</option>';
            }
          }
          $shipBox .= '</select>';

          print '<form action="" method="post">';
          print '<input type="hidden" name="selectedOrderNumber" value="' . $selectedOrderNumber . '">';

          print '<div style="width:260px; float:left; font-weight:bold;">Product</div>';
          print '<div style="width:115px; float:left; font-weight:bold;">Ordered Qty</div>';
          print '<div style="width:180px; float:left; font-weight:bold;">Shipped Date</div>';
          print '<div style="width:130px; float:left; font-weight:bold;">Shipped Qty</div>';
          print '<div style="width:220px; float:left; font-weight:bold;">Tracking #</div>';
          print '<div style="clear:both;"></div>';
          print '<hr class="full-line">';

          foreach ($orderLines as $rowLines)
          {
            $detailID = $rowLines['Order_details_id'];

            $dateValue = !empty($rowLines['Shipping_shipped_date'])
                       ? substr($rowLines['Shipping_shipped_date'], 0, 10)
                       : date('Y-m-d');

            $qtyValue = ($rowLines['Shipping_shipped_quantity'] !== null && $rowLines['Shipping_shipped_quantity'] !== '')
                      ? $rowLines['Shipping_shipped_quantity']
                      : $rowLines['Order_details_ordered_quantity'];

            $trackingValue = !empty($rowLines['Shipping_tracking_number'])
                           ? $rowLines['Shipping_tracking_number']
                           : '';

            print '<div style="width:260px; float:left; min-height:45px;">' . $rowLines['Products_name'] . '</div>';
            print '<div style="width:115px; float:left; min-height:45px;">' . $rowLines['Order_details_ordered_quantity'] . '</div>';
            print '<div style="width:180px; float:left; min-height:45px;"><input type="date" name="shippedDate[' . $detailID . ']" value="' . $dateValue . '" style="width:150px;"></div>';
            print '<div style="width:130px; float:left; min-height:45px;"><input type="text" name="shippedQty[' . $detailID . ']" value="' . $qtyValue . '" style="width:60px;"></div>';
            print '<div style="width:220px; float:left; min-height:45px;"><input type="text" name="trackingNumber[' . $detailID . ']" value="' . $trackingValue . '" style="width:200px;"></div>';
            print '<div style="clear:both;"></div>';
            print '<hr class="full-line">';
          }

          print '<br>';
          print '<div style="width:300px; float:left;">';
          print '<strong>Shipping Method:</strong><br>' . $shipBox;
          print '</div>';

          print '<div style="width:170px; float:left;">';
          print '<strong>Freight Cost:</strong><br>';
          print '<input type="text" name="freightCost" value="' . $defaultFreight . '" style="width:100px;">';
          print '</div>';

          print '<div style="width:170px; float:left;">';
          print '<strong>Handling Cost:</strong><br>';
          print '<input type="text" value="' . $defaultHandling . '" readonly style="width:100px; background:#eeeeee;">';
          print '</div>';

          print '<div style="clear:both;"></div><br>';
          print '<input type="submit" name="submitShipOrder" value="Ship Order" class="submit-reset">';
          print '</form>';

          print '<hr class="full-line">';

          print '<form action="" method="post">';
          print '<input type="hidden" name="selectedOrderNumber" value="' . $selectedOrderNumber . '">';
          print '<strong>Change Status Without Shipping:</strong> ';
          print '<select name="orderStatus" style="height:30px;">';
          print '<option value="Processed">Processed</option>';
          print '<option value="On Hold">On Hold</option>';
          print '<option value="Cancelled">Cancelled</option>';
          print '</select> ';
          print '<input type="submit" name="submitUpdateStatus" value="Update Status" class="submit-reset">';
          print '</form>';
        }
      }
    }
    ?>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>
<?php include 'includes/signout.php'; ?>
</body>
</html>
<?php
}
else
{
  header('Location: login.php');
}
?>
