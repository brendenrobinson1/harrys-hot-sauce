<?php
include 'includes/core.php';
include 'includes/connection.php';
date_default_timezone_set('America/New_York');
error_reporting(E_ERROR | E_PARSE);

if (loggedin()) {

$_SESSION['Login_username'] = getuserfield('Login_username');
$LoginID = $_SESSION['Login_id'];

$query = "SELECT * FROM `Accounts`, Login
          WHERE `Account_id` = Login_account_id
          AND Login_id = '".$_SESSION['Login_id']."'";

if (!$result = mysqli_query($link, $query))
{
  $result = 0;
  echo ('Error executing query1: ' . mysqli_errno($link)." - ".mysqli_error($link). "<BR>");
}
else
{
  $row = mysqli_fetch_array($result, MYSQLI_BOTH);
  $firstName = $row['Account_firstname'];
  $accountID = $row['Account_id'];
}
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

<?php
if (isset($_GET['added']) && $_GET['added'] == '1')
{
  print '<div class="success-msg">Added to cart!</div>';
}

$productLimit = 15;

$queryLimit = "SELECT `GL_data`
               FROM `General_lookup`
               WHERE `GL_type` = 'Control'
               AND `GL_name` = 'Products Display Limit'
               LIMIT 1";

if ($resultLimit = mysqli_query($link, $queryLimit))
{
  $rowLimit = mysqli_fetch_array($resultLimit, MYSQLI_BOTH);
  if (!empty($rowLimit['GL_data']))
  {
    $productLimit = (int)$rowLimit['GL_data'];
  }
}

if (isset($_GET['limit']))
{
  if ($_GET['limit'] === 'all')
  {
    $productLimit = null;
  }
  else if (is_numeric($_GET['limit']))
  {
    $productLimit = (int)$_GET['limit'];
  }
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1)
{
  $page = 1;
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';

$countQuery = "SELECT COUNT(*) AS totalProducts
               FROM Products, Images, Inventory
               WHERE `Products_image_id` = `Images_id`
               AND `Inventory_product_id` = `Products_id`
               AND LOWER(`Products_status`) = 'active'";

if (!$countResult = mysqli_query($link, $countQuery))
{
  $totalProducts = 0;
}
else
{
  $countRow = mysqli_fetch_array($countResult, MYSQLI_BOTH);
  $totalProducts = $countRow['totalProducts'];
}

$query = "SELECT *
FROM Products, Images, Inventory
WHERE `Products_image_id` = `Images_id`
AND `Inventory_product_id` = `Products_id`
AND LOWER(`Products_status`) = 'active'";

if ($sort == 'price_low')
{
  $query .= " ORDER BY `Products_unit_price` ASC";
}
else if ($sort == 'price_high')
{
  $query .= " ORDER BY `Products_unit_price` DESC";
}
else if ($sort == 'name_az')
{
  $query .= " ORDER BY `Products_display_name` ASC";
}
else if ($sort == 'name_za')
{
  $query .= " ORDER BY `Products_display_name` DESC";
}
else
{
  $query .= " ORDER BY `Products_id`";
}

if ($productLimit !== null)
{
  $start = ($page - 1) * $productLimit;
  $query .= " LIMIT $start, $productLimit";
  $totalPages = ceil($totalProducts / $productLimit);
}
else
{
  $totalPages = 1;
}

if (!$result = mysqli_query($link, $query))
{
  $result = 0;
  echo ('Error executing query2: ' . mysqli_errno($link)." - ".mysqli_error($link). "<BR>");
}
else
{
  $itemCount = 0;

  print '<div class="product-controls">';

  print '<div class="product-toggle-bar">';
  print '<strong>Show: </strong>';
  print '<a class="'.(($productLimit === 15) ? 'active-toggle' : '').'" href="productsli.php?limit=15&page=1&sort='.$sort.'">15</a>';
  print '<a class="'.(($productLimit === 20) ? 'active-toggle' : '').'" href="productsli.php?limit=20&page=1&sort='.$sort.'">20</a>';
  print '<a class="'.(($productLimit === 30) ? 'active-toggle' : '').'" href="productsli.php?limit=30&page=1&sort='.$sort.'">30</a>';
  print '<a class="'.(($productLimit === 50) ? 'active-toggle' : '').'" href="productsli.php?limit=50&page=1&sort='.$sort.'">50</a>';
  print '<a class="'.(($productLimit === null) ? 'active-toggle' : '').'" href="productsli.php?limit=all&page=1&sort='.$sort.'">All</a>';
  print '</div>';

  print '<div class="product-count-message">';
  if ($productLimit === null)
  {
    print 'Showing all active products';
  }
  else
  {
    $shownStart = (($page - 1) * $productLimit) + 1;
    $shownEnd = $shownStart + $productLimit - 1;
    if ($shownEnd > $totalProducts)
    {
      $shownEnd = $totalProducts;
    }
    print 'Showing '.$shownStart.'-'.$shownEnd.' of '.$totalProducts.' active products';
  }
  print '</div>';

  print '<div class="product-limit-select">';
  print '<form method="get" action="productsli.php">';
  print '<select name="limit" onchange="this.form.submit()">';
  print '<option value="15"'.(($productLimit === 15) ? ' selected' : '').'>15 Products</option>';
  print '<option value="20"'.(($productLimit === 20) ? ' selected' : '').'>20 Products</option>';
  print '<option value="30"'.(($productLimit === 30) ? ' selected' : '').'>30 Products</option>';
  print '<option value="50"'.(($productLimit === 50) ? ' selected' : '').'>50 Products</option>';
  print '<option value="all"'.(($productLimit === null) ? ' selected' : '').'>All Products</option>';
  print '</select>';
  print '<input type="hidden" name="page" value="1">';
  print '<input type="hidden" name="sort" value="'.$sort.'">';
  print '</form>';
  print '</div>';

  print '<div class="product-sort-select">';
  print '<form method="get" action="productsli.php">';
  print '<input type="hidden" name="limit" value="'.($productLimit === null ? 'all' : $productLimit).'">';
  print '<input type="hidden" name="page" value="1">';
  print '<select name="sort" onchange="this.form.submit()">';
  print '<option value="default"'.(($sort == 'default') ? ' selected' : '').'>Sort By</option>';
  print '<option value="price_low"'.(($sort == 'price_low') ? ' selected' : '').'>Price: Low to High</option>';
  print '<option value="price_high"'.(($sort == 'price_high') ? ' selected' : '').'>Price: High to Low</option>';
  print '<option value="name_az"'.(($sort == 'name_az') ? ' selected' : '').'>Name: A to Z</option>';
  print '<option value="name_za"'.(($sort == 'name_za') ? ' selected' : '').'>Name: Z to A</option>';
  print '</select>';
  print '</form>';
  print '</div>';

  print '</div>';

  while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
  {
    print '<div class="item-wrapper">';

    print '<form method="post" action="">';
    print '<input type="hidden" value="'.$row['Inventory_id'].'" name="id"/>';

    print '<div class="product-name">';
    print $row['Products_display_name'];

    if (
      $row['Products_display_name'] == "Harry's Hot Sauce Tumbler" ||
      $row['Products_display_name'] == "Fiery Fleece Blanket" ||
      $row['Products_display_name'] == "King of the Grill Apron" ||
      $row['Products_display_name'] == "Chili Pepper Keychain"
    )
    {
      print '<span class="limited-badge">Limited</span>';
    }

    print '</div>';

    print '<div class="product-image">';
    print '<img src="'.$row['Images_url'].'" alt="'.htmlspecialchars($row['Products_display_name']).'"/>';
    print '</div>';

    print '<div class="product-description">';
    print $row['Products_description'];
    print '</div>';

    print '<div class="product-extra">';

    print '<div class="inventory">';
    $inv_id = $row['Inventory_id'];
    $query16a = "SELECT SUM(SC_order_quantity) FROM Shopping_cart WHERE SC_inventory_id = $inv_id";

    if (!$result1 = mysqli_query($link, $query16a))
    {
      echo ('Error executing query3: ' . mysqli_errno($link)." - ".mysqli_error($link)."<BR>");
      $availqty = 0;
    }
    else
    {
      $row16a = mysqli_fetch_array($result1, MYSQLI_BOTH);
      $sCQtya = $row16a['SUM(SC_order_quantity)'];

      if (is_null($sCQtya))
      {
        $sCQtya = 0;
      }

      $availqty = $row['Inventory_units_in_stock'] - $sCQtya;

      if ($availqty <= 0)
      {
        print '<span class="stock-badge out-stock">Out of Stock</span>';
      }
      else if ($availqty <= 25)
      {
        print '<span class="stock-badge low-stock">Low Stock - '.$availqty.' left</span>';
      }
      else
      {
        print '<span class="stock-badge in-stock">In Stock - '.$availqty.'</span>';
      }
    }
    print '</div>';

    print '<div class="price">';
    print '$'.number_format($row['Products_unit_price'], 2);
    print '</div>';

    print '<div class="quantity-wrapper">';
    print '<select class="quantity-select" name="quantity_select">';
    print '<option value="">Quick Qty</option>';
    print '<option value="1">1</option>';
    print '<option value="2">2</option>';
    print '<option value="3">3</option>';
    print '<option value="4">4</option>';
    print '<option value="5">5</option>';
    print '<option value="6">6</option>';
    print '<option value="7">7</option>';
    print '<option value="8">8</option>';
    print '<option value="9">9</option>';
    print '<option value="10">10</option>';
    print '</select>';

    print '<input class="quantity-input" type="number" name="quantity1" placeholder="Custom qty" min="1" />';
    print '</div>';

    print '<div class="button-wrapper">';
    if ($availqty <= 0)
    {
      print '<span class="unavailable">Unavailable</span>';
    }
    else
    {
      print '<input type="submit" value="Add To Cart" name="submitItem" class="test"/>';
    }
    print '</div>';

    print '</div>';
    print '</form>';
    print '</div>';

    $itemCount++;

    if ($itemCount % 4 == 0)
    {
      print '<div style="clear:both;"></div>';
    }
  }

  print '<div style="clear:both;"></div>';

  if ($productLimit !== null && $totalPages > 1)
  {
    print '<div class="pagination">';

    if ($page > 1)
    {
      print '<a class="arrow" href="productsli.php?limit='.$productLimit.'&page='.($page - 1).'&sort='.$sort.'">&#8592;</a>';
    }

    for ($i = 1; $i <= $totalPages; $i++)
    {
      if ($i == $page)
      {
        print '<span class="current-page">'.$i.'</span>';
      }
      else
      {
        print '<a href="productsli.php?limit='.$productLimit.'&page='.$i.'&sort='.$sort.'">'.$i.'</a>';
      }
    }

    if ($page < $totalPages)
    {
      print '<a class="arrow" href="productsli.php?limit='.$productLimit.'&page='.($page + 1).'&sort='.$sort.'">&#8594;</a>';
    }

    print '</div>';
  }

  if ($result)
  {
    mysqli_free_result($result);
  }
}

$quantity = 0;

if (isset($_POST['submitItem']))
{
  if (!empty($_POST['quantity1']) && intval($_POST['quantity1']) > 0)
  {
    $quantity = intval($_POST['quantity1']);
  }
  else if (!empty($_POST['quantity_select']) && intval($_POST['quantity_select']) > 0)
  {
    $quantity = intval($_POST['quantity_select']);
  }

  if ($quantity > 0)
  {
    $id = intval($_POST['id']);
    $dateTime = date("Y-m-d H:i:s");

    $query5 = "SELECT Products.Products_unit_price
               FROM Inventory
               INNER JOIN Products
               ON Inventory.Inventory_product_id = Products.Products_id
               WHERE Inventory.Inventory_id = $id";

    if (!$result5 = mysqli_query($link, $query5))
    {
      echo ('Error executing query5: ' . mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>");
    }
    else
    {
      $row5 = mysqli_fetch_array($result5, MYSQLI_BOTH);
      $unitPrice = $row5['Products_unit_price'];

      $query16 = "SELECT * FROM `Shopping_cart`
                  WHERE `SC_inventory_id` = $id
                  AND `SC_account_id` = $accountID";

      if (!$result16 = mysqli_query($link, $query16))
      {
        echo ('Error executing query16: ' . mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>");
      }
      else
      {
        if (mysqli_num_rows($result16) == 0)
        {
          $query8 = "INSERT INTO `Shopping_cart`
          (`SC_account_id`, `SC_inventory_id`, `SC_order_quantity`,
           `SC_unit_price`, `SC_created_date`, `SC_created_by`,
           `SC_last_updated_date`, `SC_last_updated_by`, `SC_discount_amount`)
          VALUES
          ('$accountID', '$id', '$quantity', '$unitPrice', '$dateTime',
           'addToCartButton', '$dateTime', 'addToCartButton', '0')";

          if (!$result8 = mysqli_query($link, $query8))
          {
            echo ('Error executing query8: ' . mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>");
          }
        }
        else
        {
          $row16 = mysqli_fetch_array($result16, MYSQLI_BOTH);
          $currentQty = $row16['SC_order_quantity'];
          $newQty = $currentQty + $quantity;

          $sql18 = "UPDATE `Shopping_cart`
                    SET `SC_order_quantity` = '$newQty',
                        `SC_last_updated_date` = '$dateTime',
                        `SC_last_updated_by` = 'addToCartButton'
                    WHERE `SC_inventory_id` = $id
                    AND `SC_account_id` = $accountID";

          if (!$result18 = mysqli_query($link, $sql18))
          {
            echo ('Error executing query18: ' . mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>");
          }
        }

        $quantity = 0;
        $_POST['quantity1'] = 0;
        unset($_POST['quantity1']);
        unset($_POST['quantity_select']);
        unset($_POST['submitItem']);

        if (isset($_GET['limit']) && isset($_GET['page']) && isset($_GET['sort']))
        {
          header('Location: productsli.php?limit=' . urlencode($_GET['limit']) . '&page=' . urlencode($_GET['page']) . '&sort=' . urlencode($_GET['sort']) . '&added=1');
        }
        else if (isset($_GET['limit']) && isset($_GET['page']))
        {
          header('Location: productsli.php?limit=' . urlencode($_GET['limit']) . '&page=' . urlencode($_GET['page']) . '&added=1');
        }
        else if (isset($_GET['limit']))
        {
          header('Location: productsli.php?limit=' . urlencode($_GET['limit']) . '&added=1');
        }
        else
        {
          header('Location: productsli.php?added=1');
        }
        exit();
      }
    }
  }
}
?>

<?php include 'includes/signout.php'; ?>
</div>
<?php include 'includes/footer.php'; ?>
</div>
</body>
</html>
<?php
}
?>