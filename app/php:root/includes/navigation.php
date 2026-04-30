<?php

$allNavItems = array(
  array(
    'link' => 'index.php',
    'text' => 'Home',
    'subs' => array(
      array(
        'link' => 'account.php',
        'text' => 'Account'
      ),
      array(
        'link' => 'employeemaintenance.php',
        'text' => 'Employee Maintenance'
      )
    )
  ),
  array(
    'link' => 'productsli.php',
    'text' => 'Products'
  ),
  array(
    'link' => 'cart.php',
    'text' => 'Cart'
  ),
  array(
    'link' => 'shipping.php',
    'text' => 'Shipping'
  )
);

function displayAllSiteLinks($items, $defaultUlClass = '', $depth = 1)
{
  // Convert numbers to verbose words. This stops at 9 levels.
  $mappedLevelNames = array(
    1 => 'first',
    2 => 'second',
    3 => 'third',
    4 => 'fourth',
    5 => 'fifth',
    6 => 'sixth',
    7 => 'seventh',
    8 => 'eigth',
    9 => 'ninth'
  );

  $currentClassLevel = $mappedLevelNames[$depth] . '-level';

  // Create the class for the current level's UL
  if ($defaultUlClass != '')
    $ulClass = $currentClassLevel;
  else
    $ulClass = $currentClassLevel;

  echo '<ul class="' . $ulClass . '">';

  // Loop through each item at the current level
  foreach ($items as $item)
  {
    $activeClass = '';
    $found = strpos($_SERVER['PHP_SELF'], $item['link']);

    if ($found != false)
    {
      $activeClass = 'active';
    }

    echo '<li class="' . $activeClass . '">';
    echo '<a href="' . $item['link'] . '">' . $item['text'] . '</a>';

    if (isset($item['subs']))
    {
      displayAllSiteLinks(
        $item['subs'],
        (isset($item['subsClass']) ? $item['subsClass'] : ''),
        $depth + 1
      );
    }

    echo '</li>';
  }

  echo '</ul>';
}
?>
<nav>
  <?php displayAllSiteLinks($allNavItems); ?>
</nav>