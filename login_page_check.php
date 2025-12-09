<?php
session_start();

// Database connection
$servername = "mydb.itap.purdue.edu";
$username = "duttao";
$password = "Ong1905@";
$database = $username;

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$tmp= $_GET['l'];
$tmp=explode('|',$tmp);
$username = $tmp[0];
$password = $tmp[1];

$sql1 = "SELECT Username,
PASSWORD
FROM User";

$output = "";
$usernamefrom = "";
$passwordfrom = "";
$usernamegot = false; 
$result1=mysqli_query($conn,$sql1);
//https://www.phptutorial.net/php-tutorial/php-login
//https://www.sourcecodester.com/php/11540/how-create-simple-login-validation-phpmysqli.html
//https://moshikur.com/pseudocode/pseudocode-loops/4-4-loops-with-boolean-flags
while ($row4 = mysqli_fetch_array($result1)) {
    $usernamefrom = $row4["Username"]; 
    $passwordfrom = $row4["PASSWORD"];
    if ($usernamefrom == $username) {   
        $usernamegot = true;
        if ($passwordfrom == $password) { 
                $output = "Login successful!";
                $_SESSION['logged_in'] = true; 
                $_SESSION['username'] = $username; 
            }
        else {
            $output = "Wrong password.";
        }
    }
}
if ($usernamefrom !=$usernamegot) {
    $output = "Username not found.";
}
echo json_encode($output);
$conn->close();
?>



