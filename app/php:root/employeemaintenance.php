<?php
session_start();
include 'includes/core.php';
include 'includes/connection.php';
date_default_timezone_set('America/New_York');
error_reporting(E_ERROR | E_PARSE);

if (loggedin()) {
  $_SESSION['Login_username'] = getuserfield('Login_username');
  $loginID = $_SESSION['Login_id'];

  // reset handling
  if (isset($_POST['resetfind']) || isset($_POST['resetmaintenance'])) {
    $_SESSION['NameID'] = 0;
    echo '<script type="text/javascript">document.location="employeemaintenance.php";</script>';
    exit;
  }

  $query = "SELECT * FROM `Accounts`, `Login`
            WHERE `Account_id` = `Login_account_id`
            AND `Login_id` = '" . $loginID . "'";
  if (!($result = mysqli_query($link, $query)))
  {
    echo ('Error executing query in Employeemaintenance: ' .
    mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>");
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

  if (!isset($_SESSION['NameID'])) {
    $_SESSION['NameID'] = 0;
  }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Harry's Hot Sauce</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<link rel="stylesheet" href="css/layout.css">
</head>
<body>

<div id="wrapper">
  <?php include 'includes/header.php'; ?>

  <div id="body-wrapper">
    <p class="titles">Employee Maintenance</p>

<?php
  // get next employee number
  $query2 = "SELECT MAX(Employee_number) FROM `Employee`";
  if (!$query_run2 = mysqli_query($link, $query2))
  {
    $employeeNumber = 1001;
  }
  else
  {
    $row2 = mysqli_fetch_array($query_run2, MYSQLI_BOTH);
    $employeeNumber = $row2[0];
    if (is_null($employeeNumber)) {
      $employeeNumber = 1001;
    } else {
      $employeeNumber++;
    }
  }

  // build select combo for names
  $query1 = "SELECT Account_lastname, Account_firstname, Account_middlename, Account_id
             FROM `Accounts`
             ORDER BY Account_lastname, Account_firstname, Account_middlename, Account_id";
  if (!$query_run1 = mysqli_query($link, $query1))
  {
    echo ('Error executing query1 in employee maintenance: ' .
    mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>");
    $namesbox = '<select name="NAMES" id="combo"><option value="0"> </option></select>';
  }
  else
  {
    $namesbox = '<select name="NAMES" id="combo">';
    $namesbox .= '<option value="0"> </option>';

    while ($row = mysqli_fetch_array($query_run1, MYSQLI_BOTH))
    {
      $selected = ($_SESSION['NameID'] == $row['Account_id']) ? ' selected' : '';
      $namesbox .= '<option value="' . $row['Account_id'] . '"' . $selected . '>' .
      $row['Account_lastname'] . ', ' . $row['Account_firstname'] .
      ' (' . $row['Account_id'] . ')</option>';
    }

    $namesbox .= '</select>';
    mysqli_free_result($query_run1);
  }
?>

    <div id="register-wrapper">
      <form action="" method="post">
        <div class="label-wrapper">
          <p>Name (Acct ID):</p>
          <?php echo $namesbox; ?>
        </div>

        <input type="submit" name="submitfind" value="Find" class="submit-reset">
        <input type="submit" name="resetfind" value="Reset" class="submit-reset">
      </form>

<?php
      $NameID = 0;
      $ffirstName = '';
      $flastName = '';
      $fmiddleName = '';
      $femail = '';
      $faddress = '';
      $faddress2 = '';
      $fcity = '';
      $fstate = '';
      $fzip = '';
      $facctStatus = '';
      $facctTypeID = '';
      $facctid = 0;

      if (isset($_POST['submitfind']))
      {
        $NameID = $_POST['NAMES'];
        $_SESSION['NameID'] = $NameID;

        $fquery = "SELECT * FROM `Accounts` WHERE `Account_id` = '" . $NameID . "'";
        if (!$fquery_run = mysqli_query($link, $fquery))
        {
          echo ('Error executing query in employee maintenance: ' .
          mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>");
        }
        else
        {
          $frow = mysqli_fetch_array($fquery_run, MYSQLI_BOTH);
          $ffirstName = $frow['Account_firstname'];
          $flastName = $frow['Account_lastname'];
          $fmiddleName = $frow['Account_middlename'];
          $femail = $frow['Account_email_address'];
          $faddress = $frow['Account_address1'];
          $faddress2 = $frow['Account_address2'];
          $fcity = $frow['Account_city'];
          $fstate = $frow['Account_state_region'];
          $fzip = $frow['Account_postal_code'];
          $facctStatus = $frow['Account_status'];
          $facctTypeID = $frow['Account_AT_id'];
          $facctid = $frow['Account_id'];
        }
      }
      else
      {
        $NameID = $_SESSION['NameID'];
        if ($NameID != 0)
        {
          $fquery = "SELECT * FROM `Accounts` WHERE `Account_id` = '" . $NameID . "'";
          if ($fquery_run = mysqli_query($link, $fquery))
          {
            $frow = mysqli_fetch_array($fquery_run, MYSQLI_BOTH);
            $ffirstName = $frow['Account_firstname'];
            $flastName = $frow['Account_lastname'];
            $fmiddleName = $frow['Account_middlename'];
            $femail = $frow['Account_email_address'];
            $faddress = $frow['Account_address1'];
            $faddress2 = $frow['Account_address2'];
            $fcity = $frow['Account_city'];
            $fstate = $frow['Account_state_region'];
            $fzip = $frow['Account_postal_code'];
            $facctStatus = $frow['Account_status'];
            $facctTypeID = $frow['Account_AT_id'];
            $facctid = $frow['Account_id'];
          }
        }
      }

      if ($NameID != 0)
      {
        echo $flastName . ', ' . $ffirstName . ' ' . $fmiddleName . '<br />';
        echo $faddress . '<br />';
        if (!empty($faddress2)) { echo 'Line 2: ' . $faddress2 . '<br />'; }
        echo $fcity . ', ' . $fstate . ' ' . $fzip . '<br /><br />';
        echo 'Acct Status: ' . $facctStatus . '<br />';
        echo 'Acct Type: ' . $facctTypeID . '<br />';
        echo 'Email: ' . $femail . '<br />';
      }
?>
    </div>

<?php
    $eempnumber = '';
    $eempstatus = '';
    $eempposition = '';
    $managerID = 0;

    if ($_SESSION['NameID'] != 0)
    {
      $equery = "SELECT * FROM `Employee` WHERE `Employee_account_id` = '" . $_SESSION['NameID'] . "'";
      if (!$eresult = mysqli_query($link, $equery))
      {
        echo ('Error executing query in employee maintenance: ' .
        mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>");
      }
      else
      {
        $erow = mysqli_fetch_array($eresult, MYSQLI_BOTH);
        $eempnumber = $erow['Employee_number'];
        $eempstatus = $erow['Employee_status'];
        $eempposition = $erow['Employee_position_title'];
        $managerID = $erow['Employee_manager_id'];
      }
    }

    if (!empty($eempnumber))
    {
      $ecurrentemp = 'YES';
      $employeeNumber = $eempnumber;
      $smbutton = 'Update';
    }
    else
    {
      $ecurrentemp = 'NO';
      $smbutton = 'Add';
    }

    // build select combo for employee positions
    $query1 = "SELECT GL_data FROM `General_lookup`
               WHERE `GL_type` = 'Employee Position'
               ORDER BY GL_data";
    if (!$query_run1 = mysqli_query($link, $query1))
    {
      echo ('Error executing query1 in employeemaintenance: ' .
      mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>");
      $empposbox = '<select name="positionTitle" id="combo"><option value="0"> </option></select>';
    }
    else
    {
      $empposbox = '<select name="positionTitle" id="combo">';
      $empposbox .= '<option value="0"> </option>';

      while ($row = mysqli_fetch_array($query_run1, MYSQLI_BOTH))
      {
        $selected = ($eempposition == $row['GL_data']) ? ' selected' : '';
        $empposbox .= '<option value="' . $row['GL_data'] . '"' . $selected . '>' .
        $row['GL_data'] . '</option>';
      }

      $empposbox .= '</select>';
      mysqli_free_result($query_run1);
    }

    // build select combo for employee status
    $query1 = "SELECT GL_data FROM `General_lookup`
               WHERE `GL_type` = 'Employee Status'
               ORDER BY GL_data";
    if (!$query_run1 = mysqli_query($link, $query1))
    {
      echo ('Error executing query in employee maintenance: ' .
      mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>");
      $empstatusbox = '<select name="Estatus" id="combo"><option value="0"> </option></select>';
    }
    else
    {
      $empstatusbox = '<select name="Estatus" id="combo">';
      $empstatusbox .= '<option value="0"> </option>';

      while ($row = mysqli_fetch_array($query_run1, MYSQLI_BOTH))
      {
        $selected = ($eempstatus == $row['GL_data']) ? ' selected' : '';
        $empstatusbox .= '<option value="' . $row['GL_data'] . '"' . $selected . '>' .
        $row['GL_data'] . '</option>';
      }

      $empstatusbox .= '</select>';
      mysqli_free_result($query_run1);
    }

    // build select combo for managers
    $query1 = "SELECT Account_lastname, Account_firstname, Employee_account_id,
               Employee_position_title
               FROM `Employee`, `Accounts`
               WHERE `Employee_position_title` = 'Manager'
               AND `Employee_account_id` = `Account_id`
               ORDER BY `Account_lastname`, `Account_firstname`";

    if (!$query_run1 = mysqli_query($link, $query1))
    {
      echo ('Error executing query in employee maintenance: ' .
      mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>");
      $empmgrbox = '<select name="managerID" id="combo"><option value="0"> </option></select>';
    }
    else
    {
      $empmgrbox = '<select name="managerID" id="combo">';
      $empmgrbox .= '<option value="0"> </option>';

      while ($row = mysqli_fetch_array($query_run1, MYSQLI_BOTH))
      {
        $mgrLast = trim($row['Account_lastname']);
        $mgrFirst = trim($row['Account_firstname']);
        $mgrID = $row['Employee_account_id'];

        if ($mgrLast == '' && $mgrFirst == '')
        {
          continue;
        }

        $selected = ($managerID == $mgrID) ? ' selected' : '';
        $empmgrbox .= '<option value="' . $mgrID . '"' . $selected . '>' .
        $mgrLast . ', ' . $mgrFirst . '</option>';
      }

      $empmgrbox .= '</select>';
      mysqli_free_result($query_run1);
    }
?>

    <div id="register-wrapper">
      <form action="" method="post">

        <div class="label-wrapper">
          Manager:
          <?php echo $empmgrbox; ?>
        </div>

        <div class="label-wrapper">
          Employee Number:
          <input type="text" value="<?php echo $employeeNumber; ?>"
          name="employeeNumber" size="43" readonly />
        </div>

        <div class="label-wrapper">
          Position Title:
          <?php echo $empposbox; ?>
        </div>

        <div class="label-wrapper">
          Employee Status:
          <?php echo $empstatusbox; ?>
        </div>

        <input type="submit" name="submitmaintenance" value="<?php echo $smbutton; ?>"
        class="submit-reset">
        <input type="submit" name="resetmaintenance" value="Reset" class="submit-reset">
      </form>

<?php
      if (isset($_POST['submitmaintenance']) && $_SESSION['NameID'] != 0)
      {
        $dateTime = date("Y-m-d H:i:s");
        $managerID = $_POST['managerID'];
        $positionTitle = $_POST['positionTitle'];
        $estatus = $_POST['Estatus'];
        $NameID = $_SESSION['NameID'];

        if ($ecurrentemp == 'NO')
        {
          $privilegeID = 1;

          $query = "INSERT INTO `Employee`
          (`Employee_number`, `Employee_manager_id`, `Employee_account_id`,
          `Employee_status`, `Employee_start_date`, `Employee_position_title`,
          `Employee_privilege_id`,
          `Employee_created_date`, `Employee_created_by`,
          `Employee_last_updated`, `Employee_last_updated_by`)
          VALUES
          ('$employeeNumber', '$managerID', '$NameID', '$estatus', '$dateTime',
          '$positionTitle', '$privilegeID',
          '$dateTime', 'Employee Registration',
          '$dateTime', 'Employee Registration')";

          if (!$query_run = mysqli_query($link, $query))
          {
            echo ('Error executing query in employee maintenance: ' .
            mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>");
          }
        }
        else
        {
          $query = "UPDATE `Employee`
                    SET `Employee_manager_id` = '$managerID',
                        `Employee_status` = '$estatus',
                        `Employee_position_title` = '$positionTitle'
                    WHERE `Employee_number` = '$employeeNumber'";

          if (!$query_run = mysqli_query($link, $query))
          {
            echo ('Error executing query in employee maintenance: ' .
            mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>");
          }
        }

        if ($estatus == 'Active')
        {
          $astatusid = '2';
        }
        else
        {
          $astatusid = '1';
        }

        $query7 = "UPDATE `Accounts`
                   SET `Account_AT_id` = '" . $astatusid . "',
                       `Account_last_update_date` = '" . $dateTime . "',
                       `Account_last_update_by` = 'Employee Maintenance'
                   WHERE `Account_id` = '" . $_SESSION['NameID'] . "'";

        if (!$query_run7 = mysqli_query($link, $query7))
        {
          echo ('Error executing query in employee maintenance: ' .
          mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>");
        }

        $_SESSION['NameID'] = 0;
        echo '<script type="text/javascript">document.location="employeemaintenance.php";</script>';
      }
?>
    </div>
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