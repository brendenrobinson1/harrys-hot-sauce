<html>
  <title>Brenden Test Page</title>
  <body><center> Brenden Test Page </center></body>
  <p>
    <?php
      /* The connection to mysql database */
      include 'Appendix_A_Connection.php';
      $link = mysqli_connect("robinb1867.csd2800oo01c.26sp.stu.clarkstate.edu", $db_user, $db_pass, $db_name, $db_port);
      if (mysqli_connect_error()) {
          die('Could not connect: ' . mysqli_connect_error($link). " - ".mysqli_connect_error($link). "<BR>");
      }
else {
    echo "Connection Successful<BR>";
}
$db_query = "Select *from ".$db_name.".CONNECTION_TEST";
// Execute the query
if (!$result = mysqli_query($link, $db_query)) {
   $result = 0;
   die('Error executing query: ' . mysqli_error(). " - ".mysqli_error(). "<BR>");
}
else {
    // retrieve and print first row of data only
  $array = mysqli_fetch_array($result,MYSQLI_BOTH);
  $out_status = $array[0];
  echo $array[0]."  ";
  echo $array[1]."  ";
  echo $array[2]."  ";
  print("<BR><BR>");
  // Retrieve and print entire table
  $result = mysqli_query($link, $db_query); //Force requery to start again.
  while($array = mysqli_fetch_array($result,MYSQLI_BOTH)) {
     print("<FONT COLOR='RED'>$array[0] $array[1] $array[2] </FONT><BR>");
  }
}

mysqli_close($link);
echo ' <BR/> <BR/>';
echo '<a href="main.html">Home Page</a>';
?>
  </p>
  </body>
</html> 