<?php
$servername="mydb.itap.purdue.edu";

$username="garza80";//yourCAREER/groupusername
$password="GAGA53MZ1Uo%";//yourgrouppassword
$database=$username;//ITaPsetupdatabasename=yourcareerlogin






// Form a connection to the SQL service with your SQL username, password, etc
// at the server's domain name.
$conn=new mysqli($servername,$username,$password,$database);

// If the connection fails, handle it "gracefully" and kill the php process and alert the user.
if($conn->connect_error){
die("Connection failed:" . $conn->connect_error);
}

// Get the value from the q variable within the get request sent by AJAX
$tmp= $_GET['q'];

// Convert the comma-delimited string into an array of strings.
$tmp=explode(',',$tmp);

// Use the array of strings to construct a SQL query we want a result from.
$sql = "SELECT " . $tmp[0] .", " . $tmp[1] . " from " . $tmp[2];

//Execute the SQL query
$result=mysqli_query($conn,$sql);

// Convert the table into individual rows and reformat.
$rows = [];
while($row = mysqli_fetch_array($result))
{
    $rows[] = $row;
}

// Print as plain-text the result of the JSON encoding of our SQL rows for use by the JS AJAX process
// that calls this php file.
echo json_encode($rows);

// MAKE SURE TO CLOSE THE SQL CONNECTION OR THE MACHINE WILL NOT BE HAPPY.
$conn->close();
?>
