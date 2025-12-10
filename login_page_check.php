<?php
session_start();

// Database connection
$servername = "mydb.itap.purdue.edu";
$username = "g1151939";
$password = "Group29jjmsio";
$database = $username;

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$tmp= $_GET['l'];
$tmp=explode('|',$tmp);
$username = $tmp[0];
$password = $tmp[1];

$sql1 = "SELECT Username, Role, PASSWORD FROM User WHERE Username = '".$username."'";

$output = [];
$usernamefrom = "";
$passwordfrom = "";
$role ="";
$usernamegot = True; 
$result1=mysqli_query($conn,$sql1);
//https://www.phptutorial.net/php-tutorial/php-login
//https://www.sourcecodester.com/php/11540/how-create-simple-login-validation-phpmysqli.html
//https://moshikur.com/pseudocode/pseudocode-loops/4-4-loops-with-boolean-flags
while ($row4 = mysqli_fetch_array($result1)) {
    $usernamefrom = $row4["Username"]; 
    $passwordfrom = $row4["PASSWORD"];
    $role = $row4["Role"];
}
    if ($usernamefrom == $username) {   
        if ($passwordfrom === $password) { 
            $output[] = "Login successful!";
            $_SESSION['logged_in'] = true; 
            $_SESSION['username'] = $username; 
            if($role === "SupplyChainManager") {
                $output[] = "SupplyChainManager";
            }
            if($role === "SeniorManager") {
                $output[] = "SeniorManager";
            }
        }
        else {
            $output[] = "Wrong password.";
        }
    }
if (empty($output)) {
    $output[] = "Username not found.";
}

echo json_encode($output);
$conn->close();
?>


