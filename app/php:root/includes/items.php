<?php 
if (!mysqli_select_db($link, 'HARRYS')) {
    die('Uh uh, could not select database: ' . mysqli_error($link));
}

$productLimit = 15;

/* default from General_lookup */
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

/* allow URL override */
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

/* page number */
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1)
{
    $page = 1;
}

/* sort option */
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';

/* count total active products */
$countQuery = "SELECT COUNT(*) AS totalProducts
               FROM `Products`, `Images`
               WHERE `Products_image_id` = `Images_id`
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

/* main query */
$query = "SELECT *
          FROM `Products`, `Images`
          WHERE `Products_image_id` = `Images_id`
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
    $query .= " ORDER BY `Products_name` ASC";
}
else if ($sort == 'name_za')
{
    $query .= " ORDER BY `Products_name` DESC";
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
    echo 'Error executing query: ' . mysqli_errno($link) . " - " . mysqli_error($link) . "<BR>";
}
else
{
    $itemCount = 0;

    print '<div class="product-controls">';

    print '<div class="product-toggle-bar">';
    print '<strong>Show: </strong>';
    print '<a class="' . (($productLimit === 15) ? 'active-toggle' : '') . '" href="products.php?limit=15&page=1&sort='.$sort.'">15</a>';
    print '<a class="' . (($productLimit === 20) ? 'active-toggle' : '') . '" href="products.php?limit=20&page=1&sort='.$sort.'">20</a>';
    print '<a class="' . (($productLimit === 30) ? 'active-toggle' : '') . '" href="products.php?limit=30&page=1&sort='.$sort.'">30</a>';
    print '<a class="' . (($productLimit === 50) ? 'active-toggle' : '') . '" href="products.php?limit=50&page=1&sort='.$sort.'">50</a>';
    print '<a class="' . (($productLimit === null) ? 'active-toggle' : '') . '" href="products.php?limit=all&page=1&sort='.$sort.'">All</a>';
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

        print 'Showing ' . $shownStart . '-' . $shownEnd . ' of ' . $totalProducts . ' active products';
    }
    print '</div>';

    print '<div class="product-limit-select">';
    print '<form method="get" action="products.php">';
    print '<select name="limit" onchange="this.form.submit()">';
    print '<option value="15"' . (($productLimit === 15) ? ' selected' : '') . '>15 Products</option>';
    print '<option value="20"' . (($productLimit === 20) ? ' selected' : '') . '>20 Products</option>';
    print '<option value="30"' . (($productLimit === 30) ? ' selected' : '') . '>30 Products</option>';
    print '<option value="50"' . (($productLimit === 50) ? ' selected' : '') . '>50 Products</option>';
    print '<option value="all"' . (($productLimit === null) ? ' selected' : '') . '>All Products</option>';
    print '</select>';
    print '<input type="hidden" name="page" value="1">';
    print '<input type="hidden" name="sort" value="'.$sort.'">';
    print '</form>';
    print '</div>';

    print '<div class="product-sort-select">';
    print '<form method="get" action="products.php">';
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

    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        print '<div class="item-wrapper">';

        print '<div class="product-name">';
        print $row['Products_name'];

        if (
            $row['Products_name'] == "Harry's Hot Sauce Tumbler" ||
            $row['Products_name'] == "Fiery Fleece Blanket" ||
            $row['Products_name'] == "King of the Grill Apron" ||
            $row['Products_name'] == "Chili Pepper Keychain"
        )
        {
            print '<span class="limited-badge">Limited</span>';
        }

        print '</div>';

        print '<div class="product-image">';
        print '<img src="' . $row['Images_url'] . '" alt="' . htmlspecialchars($row['Products_name']) . '"/>';
        print '</div>';

        print '<div class="product-description">';
        print $row['Products_description'];
        print '</div>';

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
            print '<a class="arrow" href="products.php?limit=' . $productLimit . '&page=' . ($page - 1) . '&sort=' . $sort . '">&#8592;</a>';
        }

        for ($i = 1; $i <= $totalPages; $i++)
        {
            if ($i == $page)
            {
                print '<span class="current-page">' . $i . '</span>';
            }
            else
            {
                print '<a href="products.php?limit=' . $productLimit . '&page=' . $i . '&sort=' . $sort . '">' . $i . '</a>';
            }
        }

        if ($page < $totalPages)
        {
            print '<a class="arrow" href="products.php?limit=' . $productLimit . '&page=' . ($page + 1) . '&sort=' . $sort . '">&#8594;</a>';
        }

        print '</div>';
    }

    if ($result)
    {
        mysqli_free_result($result);
    }
}
?>