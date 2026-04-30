<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Harry's Hot Sauce</title>
    <meta-http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link rel="stylesheet" href="css/layout.css" type="text/css">
    </head>
    <body>
      <?php include 'includes/core.php'; ?>
      <?php include 'includes/connection.php'; ?>
      <div id="wrapper">
      <?php include 'includes/header.php'; ?>
      <div id="body-wrapper">
       <?php include 'includes/items.php'; ?>
      </div>
      <?php include 'includes/footer.php'; ?>
      </div>
      <?php 
      include 'includes/signIn.php';
      ?>
    </body>
</html>