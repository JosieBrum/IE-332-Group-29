<?php
// Database connection
$servername = "mydb.itap.purdue.edu";
$username = "g1151939";
$password = "Group29jjmsio";
$database   = $username;

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tmp = $_GET;

//https://flatcoding.com/tutorials/php/foreach-loop-in-php/
//https://www.geeksforgeeks.org/php/what-does-dollar-dollar-or-double-dollar-means-in-php/
//getting user inputs 
foreach($tmp as $key => $value) {
    $$key = $value;  
}



$sql1 = "SELECT EXISTS(
    SELECT 1 
    FROM Location 
    WHERE City = '".$city."' 
      AND CountryName = '".$country."'
      AND ContinentName = '".$continent."'
) AS LocationIDExists";

$locationcheck = [];
$result1=mysqli_query($conn,$sql1);
while ($row1 = mysqli_fetch_array($result1)) {
    $locationcheck = $row1['LocationIDExists'];
}
if($locationcheck == "0") {
    $sql2 = "INSERT INTO Location (City, CountryName, ContinentName) 
    VALUES ('".$city."', '".$country."', '".$continent."')";
    mysqli_query($conn, $sql2);
}
$sql3 = "SELECT LocationID 
        FROM Location 
        WHERE City = '".$city."' 
        AND CountryName = '".$country."'
        AND ContinentName = '".$continent."'";
$locationID = [];
$result2=mysqli_query($conn,$sql3);
while ($row2 = mysqli_fetch_array($result2)) {
    $locationID = $row2['LocationID'];
}
$sql4 = "SELECT EXISTS(
        SELECT 1 
        FROM Company 
        WHERE CompanyName = '".$companyName."'
    ) AS CompanyExists";

$companycheck = [];
$result3=mysqli_query($conn,$sql4);
while ($row3 = mysqli_fetch_array($result3)) {
    $companycheck = $row3['CompanyExists'];
}
if($companycheck == "1") {
    echo json_encode("Company Already Exists.");
}
if($companycheck == "0")  {
    $sql5 = "INSERT INTO Company (CompanyName, TierLevel, Type, LocationID)
    VALUES ('".$companyName."', '".$tier."', '".$type."', '".$locationID."')";
    mysqli_query($conn, $sql5);
}
$sql6 = "SELECT CompanyID 
FROM Company
WHERE CompanyName = '".$companyName."'";
$result6=mysqli_query($conn,$sql6);
while ($row6 = mysqli_fetch_array($result6)) {
    $companyID = $row6['CompanyID'];
}
if($type == "Manufacturer") {
    $sql7 = "INSERT INTO Manufacturer (CompanyID, FactoryCapacity)
VALUES ('".$companyID."', '".$FactoryCapacity."')";
mysqli_query($conn, $sql7);
}
if($type == "Distributor") {
    $sql8 = "INSERT INTO Distributor (CompanyID)
VALUES ('".$companyID."')";
mysqli_query($conn, $sql8);
}
if($type == "Retailer") {
    $sql9 = "INSERT INTO Retailer (CompanyID)
VALUES ('".$companyID."')";
mysqli_query($conn, $sql9);
}

?>
