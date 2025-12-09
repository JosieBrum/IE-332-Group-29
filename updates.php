<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Database connection
$servername = "mydb.itap.purdue.edu";
$username   = "duttao";
$password   = "Ong1905@";
$database   = $username;

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// get all GET parameters
$tmp = $_GET;


foreach ($tmp as $key => $value) {
    $$key = $conn->real_escape_string($value);
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
    while ($row1 = $result1->fetch_assoc()) {
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

    // fetch from company
    $sql4 = "SELECT CompanyID FROM Company WHERE CompanyName = '$from'";
        $result4= mysqli_query($conn, $sql4);
    while ($row4 = $result4->fetch_assoc()) {
        $companyIDfrom = $row4['CompanyID'];
    }

    // fetch to company
    $sql5 = "SELECT CompanyID FROM Company WHERE CompanyName = '$to'";
        $result5= mysqli_query($conn, $sql5);
    while ($row5 = $result5->fetch_assoc()) {
        $companyIDto = $row5['CompanyID'];
    }

     // fetch to company
    $sql7 = "SELECT CompanyID FROM Company WHERE CompanyName = '$fromo'";
        $result7= mysqli_query($conn, $sql7);
    while ($row7 = $result7->fetch_assoc()) {
        $companyIDfromorginal = $row7['CompanyID'];
    }
         // fetch to company
    $sql8 = "SELECT CompanyID FROM Company WHERE CompanyName = '$too'";
        $result8= mysqli_query($conn, $sql8);
    while ($row8 = $result8->fetch_assoc()) {
        $companyIDtoorginal = $row8['CompanyID'];
    }

    // update logistics relation
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

echo json_encode("Done")

?>

