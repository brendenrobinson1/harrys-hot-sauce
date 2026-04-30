<?php
// Sessions logout
// Starting the session
session_start();
echo '<B>Sessions Logout Page Before Logout</B><br /><br />';
// Display user variable
echo 'User variable: '.$_SESSION['user'];
// Display the session ID
echo '<br /> Session_id: '.session_id();
// Test if user variable is set
if (isset($_SESSION['user']))
  {echo '<br/>Sessions is still active<br/>';}
// Display session status
echo 'Session Status Variable: '.session_status();
// Session commands to clear session variables and stop the session
session_unset();
session_destroy();
echo '<br/><br/><br/><B>Sessions Logout Page After Logout</B> <br/>';
// Display user variable
echo 'User: '.$_SESSION['user'];
// Display the session ID
echo '<br/> Session_id: '.session_id();
// Test if user variable is set
if (isset($_SESSION['user']))
{echo '<br/> Session is still active<br/>';}
else
{echo '<br/> Session is not active<br/>';}
// Display session status
echo 'Session Status Variable: '.session_status();
echo ' <br/><br/>';
// Create and display a link for the main page.
echo '<a href="main.html">Home Page</a> ';
?>