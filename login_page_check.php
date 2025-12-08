<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
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
FROM User
";
$output = "";
$usernamegood = false; 
$result1=mysqli_query($conn,$sql1);
while ($row4 = $result1->fetch_assoc()) {
    if ($row4["Username"] == $username) {
        $usernamegood = true;
        $passwordlist = $row4["PASSWORD"];
        if ($passwordlist == $password) {

            $output = "Login successful!";
        }
        else {
            $output = "Wrong password.";
        }
    }
}
if ($usernamegood == false) {
    $output = "Username not found.";
}
echo json_encode($output);
$conn->close();
?>
