<?php
//https://w3schools.tech/tutorial/php/php_sessions
//https://phpgurukul.com/automatic-logout-after-10-minutes-of-inactive-session-in-php
session_start();
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session

// Redirect to the login page after logging out
header("Location: index.php"); // Change to your login page URL
?>