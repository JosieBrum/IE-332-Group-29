<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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

$companyID = "";
$type = "";


if (isset($name)) {

    $sql1 = "
        SELECT CompanyID, Type
        FROM Company
        WHERE CompanyName = '".$name."'
    ";

    $result1 = mysqli_query($conn, $sql1);
    while ($row1 = mysqli_fetch_array($result1)) {
        $companyID = $row1['CompanyID'];
        $type      = $row1['Type'];
    }
}

if (isset($newName) || isset($tier)) {

    $sql2 = "
        UPDATE Company
        SET
            CompanyName = CASE 
                WHEN '".$newName."' = '' THEN CompanyName 
                ELSE '".$newName."' 
            END,
            TierLevel = CASE 
                WHEN '".$tier."' = '' THEN TierLevel 
                ELSE '".$tier."' 
            END
        WHERE CompanyID = '".$companyID."'
    ";
    mysqli_query($conn, $sql2);
}

if ($type === "Manufacturer") {

    $sql3 = "
        UPDATE Manufacturer
        SET
            FactoryCapacity = CASE 
                WHEN '".$capacity."' = '' THEN FactoryCapacity 
                ELSE '".$capacity."' 
            END
        WHERE CompanyID = '".$companyID."'
    ";
    mysqli_query($conn, $sql3);
}

if ($companyID && $type === "Distributor") {
    $sql4 = "SELECT CompanyID FROM Company WHERE CompanyName = '$from'";
        $result4= mysqli_query($conn, $sql4);
    while ($row4 = mysqli_fetch_array($result4)) {
        $companyIDfrom = $row4['CompanyID'];
    }

    $sql5 = "SELECT CompanyID FROM Company WHERE CompanyName = '$to'";
        $result5= mysqli_query($conn, $sql5);
    while ($row5 = mysqli_fetch_array($result5)) {
        $companyIDto = $row5['CompanyID'];
    }

    $sql7 = "SELECT CompanyID FROM Company WHERE CompanyName = '$fromo'";
        $result7= mysqli_query($conn, $sql7);
    while ($row7 = mysqli_fetch_array($result7)) {
        $companyIDfromorginal = $row7['CompanyID'];
    }

    $sql8 = "SELECT CompanyID FROM Company WHERE CompanyName = '$too'";
        $result8= mysqli_query($conn, $sql8);
    while ($row8 = mysqli_fetch_array($result8)) {
        $companyIDtoorginal = $row8['CompanyID'];
    }

    $sql6 = "
        UPDATE OperatesLogistics
        SET
            FromCompanyID = CASE WHEN '$companyIDfrom' = '' THEN FromCompanyID ELSE '$companyIDfrom' END,
            ToCompanyID   = CASE WHEN '$companyIDto'   = '' THEN ToCompanyID   ELSE '$companyIDto'   END
        WHERE DistributorID = '$companyID'
          AND FromCompanyID = '$companyIDfromorginal'
          AND ToCompanyID   = '$companyIDtoorginal'
    ";
    mysqli_query($conn, $sql6);
}

echo json_encode("success")

?>

