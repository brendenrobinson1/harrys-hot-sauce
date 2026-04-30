<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Harry's Hot Sauce</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <link rel="stylesheet" href="css/layout.css" type="text/css">
</head>
<body>
<?php include 'includes/core.php'; ?>
<?php include 'includes/connection.php'; ?>

<?php
$dateTime = date("Y-m-d H:i:s");

// initialize variables
$firstName = $lastName = $emailAddress = $address = $city = $state = $zipCode = $username = $password = '';
$passwordHash = '';
$errors = array();

// styles for error highlighting
$firstNameError = $lastNameError = $emailError = $addressError = $cityError = $stateError = $zipError = $usernameError = $passwordError = '';

if (isset($_POST['submitRegistration']))
{
  // get form values safely
  $firstName = trim($_POST['firstName']);
  $lastName = trim($_POST['lastName']);
  $emailAddress = trim($_POST['emailAddress']);
  $address = trim($_POST['address']);
  $city = trim($_POST['city']);
  $state = trim($_POST['state']);
  $zipCode = trim($_POST['zipCode']);
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  // validation
  if ($firstName == '')
  {
    $errors[] = "First name is required";
    $firstNameError = 'style="border:2px solid red;"';
  }

  if ($lastName == '')
  {
    $errors[] = "Last name is required";
    $lastNameError = 'style="border:2px solid red;"';
  }

  if ($emailAddress == '')
  {
    $errors[] = "Email is required";
    $emailError = 'style="border:2px solid red;"';
  }
  elseif (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL))
  {
    $errors[] = "Invalid email format";
    $emailError = 'style="border:2px solid red;"';
  }

  if ($address == '')
  {
    $errors[] = "Address is required";
    $addressError = 'style="border:2px solid red;"';
  }

  if ($city == '')
  {
    $errors[] = "City is required";
    $cityError = 'style="border:2px solid red;"';
  }

  if ($state == '')
  {
    $errors[] = "State is required";
    $stateError = 'style="border:2px solid red;"';
  }

  if ($zipCode == '')
  {
    $errors[] = "Zip code is required";
    $zipError = 'style="border:2px solid red;"';
  }
  elseif (!preg_match('/^[0-9]{5}$/', $zipCode))
  {
    $errors[] = "Zip must be 5 digits";
    $zipError = 'style="border:2px solid red;"';
  }

  if ($username == '')
  {
    $errors[] = "Username is required";
    $usernameError = 'style="border:2px solid red;"';
  }

  if ($password == '')
  {
    $errors[] = "Password is required";
    $passwordError = 'style="border:2px solid red;"';
  }

  // only run inserts if no errors
  if (count($errors) == 0)
  {
    $passwordHash = md5($password);

    $query = "INSERT INTO `Accounts`
    (`Account_lastname`, `Account_firstname`, `Account_middlename`,
    `Account_email_address`, `Account_address1`, `Account_city`,
    `Account_state_region`, `Account_postal_code`, `Account_status`,
    `Account_created_date`, `Account_created_by`,
    `Account_last_update_date`, `Account_last_update_by`, `Account_AT_id`)
    VALUES
    ('$lastName', '$firstName', '', '$emailAddress', '$address', '$city',
    '$state', '$zipCode', 'active', '$dateTime', 'Registration Form',
    '$dateTime', 'Registration Form', '1')";

    if (!$query_run = mysqli_query($link, $query))
    {
      $query_run = 0;
      echo "Error executing query: " . mysqli_errno($link) . " - " . mysqli_error($link) . "<br>";
    }
    else
    {
      $query4 = "SELECT `Account_id` FROM `Accounts`
                 WHERE `Account_email_address` = '$emailAddress'";
      if (!$result4 = mysqli_query($link, $query4))
      {
        $result4 = 0;
        echo "Error executing query: " . mysqli_errno($link) . " - " . mysqli_error($link) . "<br>";
      }
      else
      {
        $row4 = mysqli_fetch_array($result4, MYSQLI_BOTH);
        $accountID = $row4['Account_id'];

        $query2 = "INSERT INTO `Login`
        (`Login_username`, `Login_password`, `Login_status`,
        `Login_created_date`, `Login_created_by`, `Login_account_id`,
        `Login_last_update_date`, `Login_last_update_by`)
        VALUES
        ('$username', '$passwordHash', 'active', '$dateTime',
        'Registration Form', '$accountID', '$dateTime', 'Registration Form')";

        if (!$query_run2 = mysqli_query($link, $query2))
        {
          $query_run2 = 0;
          echo "Error executing query: " . mysqli_errno($link) . " - " . mysqli_error($link) . "<br>";
        }
        else
        {
          echo "<script type='text/javascript'> document.location = 'login.php'; </script>";
        }
      }
    }
  }
}
?>

<div id="wrapper">
  <?php include 'includes/header.php'; ?>

  <div id="body-wrapper">
    <p class="titles">Registration</p>

    <div id="register-wrapper">

      <?php
      if (count($errors) > 0)
      {
        print '<div style="color:red; font-weight:bold;">';
        foreach ($errors as $error)
        {
          print $error . '<br>';
        }
        print '</div><br>';
      }
      ?>

      <form action="" method="post">

        <div class="label-wrapper">
          <p>First Name:</p>
          <input name="firstName" type="text"
                 value="<?php print $firstName; ?>"
                 <?php print $firstNameError; ?>
                 placeholder="First Name:" size="40"/>
        </div>

        <div class="label-wrapper">
          <p>Last Name:</p>
          <input name="lastName" type="text"
                 value="<?php print $lastName; ?>"
                 <?php print $lastNameError; ?>
                 placeholder="Last Name:" size="40"/>
        </div>

        <div class="label-wrapper">
          <p>Email Address:</p>
          <input name="emailAddress" type="text"
                 value="<?php print $emailAddress; ?>"
                 <?php print $emailError; ?>
                 placeholder="Email Address:" size="40"/>
        </div>

        <div class="label-wrapper">
          <p>Address:</p>
          <input name="address" type="text"
                 value="<?php print $address; ?>"
                 <?php print $addressError; ?>
                 placeholder="Address:" size="40"/>
        </div>

        <div class="label-wrapper">
          <p>City:</p>
          <input name="city" type="text"
                 value="<?php print $city; ?>"
                 <?php print $cityError; ?>
                 placeholder="City:" size="40"/>
        </div>

        <div class="label-wrapper">
          <p>State:</p>
          <input name="state" type="text"
                 value="<?php print $state; ?>"
                 <?php print $stateError; ?>
                 placeholder="State:" size="40"/>
        </div>

        <div class="label-wrapper">
          <p>Zip Code:</p>
          <input name="zipCode" type="text"
                 value="<?php print $zipCode; ?>"
                 <?php print $zipError; ?>
                 placeholder="Zip Code:" size="40"/>
        </div>

        <div class="label-wrapper">
          <p>Username:</p>
          <input name="username" type="text"
                 value="<?php print $username; ?>"
                 <?php print $usernameError; ?>
                 placeholder="Username:" size="40"/>
        </div>

        <div class="label-wrapper">
          <p>Password:</p>
          <input name="password" type="password"
                 value="<?php print $password; ?>"
                 <?php print $passwordError; ?>
                 placeholder="Password:" size="40"/>
        </div>

        <input type="submit" name="submitRegistration" value="Register" class="submit-reset"/>
        <input type="reset" name="reset" value="Reset" class="submit-reset"/>
      </form>
    </div>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/signIn.php'; ?>
</body>
</html>