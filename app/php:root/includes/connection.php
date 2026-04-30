<?php 
error_reporting(E_ERROR | E_PARSE);
include 'includes/Harrys_DB_Connection.php';
$link = mysqli_connect('robinb1867.csd2800oo01c.26sp.stu.clarkstate.edu', 'robinb1867', 'dSTUoMXRNlgQuCZN', 'HARRYS', 3306);
mysqli_select_db($link, 'HARRYS');
?>