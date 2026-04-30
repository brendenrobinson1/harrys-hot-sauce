<?php 
include 'includes/connection.php';
include 'includes/core.php';
date_default_timezone_set('America/New_York');
error_reporting(E_ERROR | E_PARSE);

if(loggedin()) {

$_SESSION['Login_username'] = getuserfield('Login_username');
$LoginID = $_SESSION['Login_id'];

$query = "SELECT * FROM `Accounts`, Login WHERE `Account_id` = 
Login_account_id and Login_id = '".$_SESSION['Login_id']."'";
if (!$result = mysqli_query($link, $query))
{
  $result = 0;
  echo ('Error executing query1 in receipts: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
}
else 
{
  $row = mysqli_fetch_array($result, MYSQLI_BOTH);
}

$firstName = $row['Account_firstname'];
$lastName = $row['Account_lastname'];
$email = $row['Account_email_address'];
$address = $row['Account_address1'];
$city = $row['Account_city'];
$state = $row['Account_state_region'];
$zip = $row['Account_postal_code'];
$accountID = $row['Account_id'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Harry's Hot Sauce</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<link href="css/layout.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="wrapper">
<?php include 'includes/header.php'; ?>

<div id="body-wrapper">
<h1 class="titles">THANK YOU FOR YOUR ORDER</h1>

<?php
$Order_Number = $_SESSION['OrderNumber'];
$ItemCtr = 1;

$query2 = "SELECT * FROM `Order_headers` WHERE `Order_header_number` = $Order_Number";
if (!$query_run2 = mysqli_query($link, $query2))
{
  $result = 0;
  echo('Error executing 2 in receipts: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
}
else
{
  $row2 = mysqli_fetch_array($query_run2, MYSQLI_BOTH);
}

$orderHeaderID = $row2['Order_header_id'];
$orderDate = $row2['Order_header_order_date'];
$total = 0;

print '<br/>';
print '<br/>';
print '<p class="product-name">Order Number: '.$Order_Number.'</p>';
print '<br/>';
print '<br/>';
print '<br/>';
print '<p>' .$firstName.' '.$lastName.'<br/>'.$address.'<br/>'.$city.', '.$state.' '.$zip.'</p><br/>';
print '<p>Email: '.$email.'</p><br/>Account Number: '.$accountID.'<br/><br/>';
print '<br/>';
print '<br/>';

$query3 = "SELECT od.Order_details_ordered_quantity,
                  p.Products_name,
                  p.Products_unit_price,
                  s.Shipping_tracking_number
           FROM `Order_details` od
           INNER JOIN `Inventory` i
             ON od.Order_details_inventory_id = i.Inventory_id
           INNER JOIN `Products` p
             ON i.Inventory_product_id = p.Products_id
           LEFT JOIN `Shipping` s
             ON od.Order_details_id = s.Shipping_order_details_id
           WHERE od.Order_details_header_id = $orderHeaderID
           ORDER BY od.Order_details_line_number ASC";

if (!$query_run3 = mysqli_query($link, $query3))
{
  $result = 0;
  echo('Error executing 3 in receipts: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
}
else
{
  while($row3 = mysqli_fetch_array($query_run3, MYSQLI_BOTH))
  {
    $subtotal = $row3['Order_details_ordered_quantity'] * $row3['Products_unit_price'];
    $trackingDisplay = !empty($row3['Shipping_tracking_number']) ? $row3['Shipping_tracking_number'] : 'Not assigned yet';

    print '<div style="width:280px; height:auto; float:left; font-weight:bold; font-size:18px;">' .$ItemCtr.'. '.$row3['Products_name'].'</div>';
    print '<div style="width:160px; height:auto; float:left; font-weight:bold; font-size:18px;">Quantity ' . $row3['Order_details_ordered_quantity'].'</div>';
    print '<div style="width:140px; height:auto; float:left; font-weight:bold; font-size:18px;">$'.number_format($subtotal,2).'</div>';
    print '<div style="width:260px; height:auto; float:left; font-weight:bold; font-size:16px;">Tracking: '.$trackingDisplay.'</div>';
    print '<div style="clear:both;"></div>';
    print '<hr class="full-line">';
    print '<br/>';

    $ItemCtr++;
    $total += $subtotal;
  }
}

$shippingMethod = isset($_SESSION['ShippingMethod']) ? $_SESSION['ShippingMethod'] : 'USPS';
$shipping = isset($_SESSION['ShippingCost']) ? (float)$_SESSION['ShippingCost'] : 0.00;

print '<div style="width:300px; height:auto; float:left; font-weight:bold; font-size:18px;">Shipping Method: '.$shippingMethod.'</div>';
print '<div style="width:300px; height:auto; float:left; font-weight:bold; font-size:18px;">Shipping Cost $ '.number_format($shipping,2) . '</div>';
print '<div style="clear:both;"></div>';
print '<br/>';
print '<div style="width:300px; height:auto; float:left; font-weight:bold; font-size:18px;">Total $' . number_format($grandtotal = $total + $shipping,2) . '</div>';
print '<div style="clear:both;"></div>';
print '<br/>';
print '<br/>';
print '<hr class="full-line">';
print '<br/>';
print '<br/>';
print '<p style="font-weight:bold; font-size:18px;">Your order is currently being processed. We will email you with a tracking number once your order has shipped.</p>';
print '<br/>';
print '<br/>';
print '<form><input type="button" value="Print this page" onClick="window.print()" class="removeItem"></form>';
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
