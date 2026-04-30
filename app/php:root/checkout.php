<?php
include 'includes/core.php';
include 'includes/connection.php';
date_default_timezone_set('America/New_York');
error_reporting(E_ERROR | E_PARSE);

if (loggedin()) {
  $_SESSION['Login_username'] = getuserfield('Login_username');
  $LoginID = $_SESSION['Login_id'];

  $query = "SELECT * FROM `Accounts`, Login WHERE `Account_id` =
  Login_account_id and Login_id = '".$_SESSION['Login_id']."'";
  if (!$result = mysqli_query($link, $query))
  {
    echo "Error executing query in checkout: " . mysqli_errno($link). " - " . mysqli_error($link)."<BR>";
  }
  else
  {
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);
  }

  $firstName = $row['Account_firstname'];
  $lastName = $row['Account_lastname'];
  $address = $row['Account_address1'];
  $city = $row['Account_city'];
  $state = $row['Account_state_region'];
  $zip = $row['Account_postal_code'];
  $email = $row['Account_email_address'];
  $accountID = $row['Account_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Harry's Hot Sauce</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<link rel="stylesheet" href="css/layout.css" type="text/css">
</head>

<body>
<div id="wrapper">
<?php include 'includes/header.php'; ?>

<div id="body-wrapper">
<p class="product-name">Shipping/Billing information</p>
<br/><br/><br/><br/>

<form action="" method="post">
<div style="width:310px; height:auto; float:left;"> First Name: <input type="text" name="firstName" value="<?php print $firstName; ?>" /></div>
<div style="width:310px; height:auto; float:left;"> Last Name: <input type="text" name="lastName" value="<?php print $lastName; ?>" /></div>
<div style="width:310px; height:auto; float:left;"> Email Address: <input type="text" name="emailAddress" value="<?php print $email; ?>" /></div>

<div style="clear:both;"></div>
<br/>

<div style="width:310px; height:auto; float:left;"> Address: <input type="text" name="address" value="<?php print $address; ?>" /></div>
<div style="width:310px; height:auto; float:left;">City: <input type="text" name="city" value="<?php print $city; ?>" /></div>
<div style="width:310px; height:auto; float:left;"> State: <input type="text" name="state" value="<?php print $state; ?>" /></div>

<div style="clear:both;"></div>
<br/>

<div style="width:310px; height:auto; float:left;"> Zip code: <input type="text" name="zipCode" value="<?php print $zip; ?>" /></div>

<div style="clear:both"></div>
<br/> <hr class="full-line"><br/>

<p class="product-name">Credit card information</p>
<br/><br/><br/><br/>

<div style="width:310px; height:auto; float:left;">
Payment method:
<select name="paymentMethod">
<option value="mastercard">Mastercard</option>
<option value="visa">Visa</option>
</select>
</div>

<div style="clear:both;"></div>
<br/><br/>

<div style="width:340px; height:auto; float:left;">
Credit Card Number:
<input type="text" name="creditCard"/>
</div>

<div style="width:300px; height:auto; float:left;">
Exp Date (mm/yy):
<input type="text" name="expDate"/>
</div>

<div style="width:300px; height:auto; float:left;">
Cvv:
<input type="text" name="cvv"/>
</div>

<div style="clear:both;"></div>
<br/><br/>

<?php
$shippingMethod = isset($_SESSION['ShippingMethod']) ? $_SESSION['ShippingMethod'] : 'USPS';
$shippingCost = isset($_SESSION['ShippingCost']) ? $_SESSION['ShippingCost'] : 0.00;
?>
<p>Shipping Method: <?php print $shippingMethod; ?> $<?php print number_format($shippingCost, 2); ?></p>

<br/><br/>
<input type="submit" name="submitCheckout" value="pay" class="submit-reset">
</form>

<?php
if (isset($_POST['submitCheckout']))
{
  $fName = $_POST['firstName'];
  $lName = $_POST['lastName'];
  $emailA = $_POST['emailAddress'];
  $address1 = $_POST['address'];
  $city1 = $_POST['city'];
  $state1 = $_POST['state'];
  $zip1 = $_POST['zipCode'];
  $payment = $_POST['paymentMethod'];
  $cCN = $_POST['creditCard'];
  $expDate = $_POST['expDate'];
  $cVV = $_POST['cvv'];
  $handlingCost = 2.95;

  // Build the order. Do NOT change its status to Processed here.
  // The order should stay Entered so shipping.php can display it as Pending.
  build_order($accountID, $link, $shippingMethod, $shippingCost, $handlingCost);

  $Order_Number = $_SESSION['OrderNumber'];

  $query1 = "UPDATE `Accounts` SET `Account_lastname` = '$lName', `Account_firstname` = '$fName',
  `Account_address1` = '$address1', `Account_city` = '$city1', `Account_state_region` = '$state1',
  `Account_postal_code` = '$zip1', `Account_email_address` = '$emailA', `Account_last_update_date` = now(),
  `Account_last_update_by` = 'Check Out' WHERE `Account_id` = '$accountID'";
  if (!$result1 = mysqli_query($link, $query1))
  {
    echo "Error executing query1 of checkout: " . mysqli_errno($link). " - " . mysqli_error($link)."<BR>";
  }

  $query2 = "INSERT INTO `Payment_method` (`PM_type`, `PM_number`, `PM_card_security_code`, `PM_exp_date`,
  `PM_account_id`, `PM_created_date`, `PM_created_by`, `PM_last_updated_date`, `PM_last_updated_by`)
  VALUES ('$payment', '$cCN', '$cVV', STR_TO_DATE('$expDate', '%m/%y'), '$accountID',
  now(), 'Check Out', now(), 'Check Out')";
  if (!$query_run2 = mysqli_query($link, $query2))
  {
    echo "Error executing query2 of checkout: " . mysqli_errno($link). " - " . mysqli_error($link)."<BR>";
  }

  $query3 = "SELECT MAX(PM_id) FROM `Payment_method` WHERE PM_account_id = $accountID";
  if (!$result3 = mysqli_query($link, $query3))
  {
    echo "Error executing query3 of checkout: " . mysqli_errno($link). " - " . mysqli_error($link)."<BR>";
  }
  else
  {
    $row3 = mysqli_fetch_array($result3, MYSQLI_BOTH);
    $PM_id = $row3[0];
  }

  // Link payment to order only. Do NOT update Order_header_status_id here.
  $query4 = "UPDATE `Order_headers` SET `Order_header_pm_id` = '$PM_id',
  `Order_header_last_updated_date` = now(), `Order_header_last_updated_by` = 'Check Out'
  WHERE `Order_header_account_id` = $accountID and `Order_header_number` = $Order_Number";
  if (!$query_run4 = mysqli_query($link, $query4))
  {
    echo "Error executing query4 of checkout: " . mysqli_errno($link). " - " . mysqli_error($link)."<BR>";
  }

  echo "<script type='text/javascript'> document.location = 'receipt.php'; </script>";
}
?>
</div>

<?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/logout.php'; ?>
</body>
</html>
<?php
} else {
  header('Location: login.php');
}
?>
