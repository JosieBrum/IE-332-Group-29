<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Database connection
$servername = "mydb.itap.purdue.edu";
$username = "duttao";
$password = "Ong1905@";
$database = $username;

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tmp = $_GET;

foreach($tmp as $key => $value) {
    $$key = $value;  
}

$onornot = 0;
if (isset($company)) {
    $onornot = 1;
}
if (isset($country)) {
    $onornot = 2;
}
if (isset($continent)) {
    $onornot = 3;
}
if (!empty($city)) {
    $onornot = 4;
}
$filiter = "";
switch ($onornot){
    case 1: 
        $filiter = "AND ('".$company."' = '' OR c.CompanyName   = '".$company."') ";
        break;
    case 2:
        $filiter = "AND ('".$country."' = '' OR l.CountryName   = '".$country."') ";
        break;
    case 3:
        $filiter = "AND ('".$continent."' = '' OR l.ContinentName   = '".$continent."') ";
        break;
    case 4: 
        $filiter = "AND ('".$city."' = '' OR l.City   = '".$city."') ";
        break;
    default: 
        $filiter = "AND ('".$company."' = '' OR c.CompanyName   = '".$company."') ";        
        break;
};

// checking
$sql0 = "
    SELECT DISTINCT
        Company.CompanyName 
    FROM Company";
 
$sql00 = "
    SELECT DISTINCT
        Location.CountryName 
    FROM Location";

$sql000 = "
    SELECT DISTINCT
        Location.ContinentName 
    FROM Location";
$sql0000 = "
    SELECT DISTINCT
        Location.City 
    FROM Location";

//adjustments
$sql1 = "SELECT 
    a.AdjustmentID,
    a.AdjustmentDate AS Date,
    c.CompanyName AS Company,
    p.ProductName AS Product,
    a.QuantityChange AS Quantity,
    a.Reason
FROM InventoryAdjustment a
JOIN InventoryTransaction t ON t.TransactionID = a.TransactionID
JOIN Company c ON c.CompanyID = a.CompanyID
JOIN Location l ON l.LocationID = c.LocationID
JOIN Product p ON p.ProductID = a.ProductID
WHERE t.Type = 'Adjustment'
  AND a.AdjustmentDate >= '".$date1."'
  AND a.AdjustmentDate <= '".$date2."'
  $filiter
ORDER BY a.AdjustmentDate, a.AdjustmentID";

//arriving 
$sql2 = "SELECT 
    s.ShipmentID,
    c.CompanyName AS Company,
    s.Quantity,
    s.ActualDate AS Date,
    p.ProductName AS Product
FROM Shipping s
JOIN InventoryTransaction t ON t.TransactionID = s.TransactionID
JOIN Product p ON p.ProductID = s.ProductID
JOIN Company c ON c.CompanyID = s.DestinationCompanyID
JOIN Location l ON l.LocationID = c.LocationID
WHERE t.Type = 'Shipping'
  AND s.ActualDate >= '".$date1."'
  AND s.ActualDate <= '".$date2."'
  $filiter
ORDER BY s.ActualDate, s.ShipmentID";

//leaving
$sql3 = "SELECT 
    s.ShipmentID,
    c.CompanyName AS Company,
    s.Quantity,
    s.ActualDate AS Date,
    p.ProductName AS Product
FROM Shipping s
JOIN InventoryTransaction t ON t.TransactionID = s.TransactionID
JOIN Product p ON p.ProductID = s.ProductID
JOIN Company c ON c.CompanyID = s.SourceCompanyID
JOIN Location l ON l.LocationID = c.LocationID
WHERE t.Type = 'Shipping'
  AND s.ActualDate >= '".$date1."'
  AND s.ActualDate <= '".$date2."'
  $filiter
ORDER BY s.ActualDate, s.ShipmentID";

$result1=mysqli_query($conn,$sql1);
$result2=mysqli_query($conn,$sql2);
$result3=mysqli_query($conn,$sql3);
$result0 = mysqli_query($conn, $sql0);
$result00 = mysqli_query($conn, $sql00);
$result000 = mysqli_query($conn, $sql000);
$result0000 = mysqli_query($conn, $sql0000);

$output = array();
$companyNameListALL = array();
$cityNameListAll = array();
$countryNameListAll = array();
$continenetNameListAll = array();
$adjustmentID = array();
$adjustmentDate = array();
$adjustmentCompany = array();
$adjustmentProduct = array();
$adjustmentquantity = array();
$adjustmentreason = array();
$adjustmentID = array();
$adjustmentDate = array();
$adjustmentCompany = array();
$adjustmentProduct = array();
$adjustmentquantity = array();
$adjustmentreason = array();
$arrivingID = array();
$arrivingDate = array();
$arrivingCompany = array();
$arrivingProduct = array();
$arrivingquantity = array();
$leavingID = array();
$leavingDate = array();
$leavingCompany = array();
$leavingProduct = array();
$leavingquantity = array();

// Now you can safely loop through the results
while ($row1 = $result1->fetch_assoc()) {
    $adjustmentID[] = $row1['AdjustmentID'];
    $adjustmentDate[] = $row1['Date'];
    $adjustmentCompany[] = $row1['Company'];
    $adjustmentProduct[] = $row1['Product'];
    $adjustmentquantity[] = $row1['Quantity'];
    $adjustmentreason[] = $row1['Reason'];
}
$output['Adjustment ID']       = $adjustmentID;
$output['Adjustment Date']     = $adjustmentDate;
$output['Adjustment Company']  = $adjustmentCompany;
$output['Adjustment Product']  = $adjustmentProduct;
$output['Adjustment Quantity'] = $adjustmentquantity;
$output['Adjustment Reason']   = $adjustmentreason;

while ($row2 = $result2->fetch_assoc()) {
    $arrivingID[] = $row2['ShipmentID'];
    $arrivingDate[] = $row2['Date'];
    $arrivingCompany[] = $row2['Company'];
    $arrivingProduct[] = $row2['Product'];
    $arrivingquantity[] = $row2['Quantity'];
}
$output['Arriving ID']       = $arrivingID;
$output['Arriving Date']     = $arrivingDate;
$output['Arriving Company']  = $arrivingCompany;
$output['Arriving Product']  = $arrivingProduct;
$output['Arriving Quantity'] = $arrivingquantity;

while ($row3 = $result3->fetch_assoc()) {
    $leavingID[] = $row3['ShipmentID'];  
    $leavingDate[] = $row3['Date'];      
    $leavingCompany[] = $row3['Company'];
    $leavingProduct[] = $row3['Product'];
    $leavingquantity[] = $row3['Quantity'];
}
$output['Leaving ID']       = $leavingID;
$output['Leaving Date']     = $leavingDate;
$output['Leaving Company']  = $leavingCompany;
$output['Leaving Product']  = $leavingProduct;
$output['Leaving Quantity'] = $leavingquantity;


if (isset($company)) {
    while ($row0 = $result0->fetch_assoc()) {
        $companylistAll[] = $row0['CompanyName'];
    }
    $output["Company Name List All: "] = $companylistAll;
}
if (isset($country)) {
    while ($row00 = $result00->fetch_assoc()) {
        $countrylistAll[] = $row00['CountryName'];
    }
    $output["Country Name List All: "] = $countrylistAll;
}
if (isset($continent)) {
    while ($row000 = $result000->fetch_assoc()) {
        $continentlistAll[] = $row000['ContinentName'];
    }
    $output["Continent Name List All: "] = $continentlistAll;
}
if (isset($city)) {
    while ($row0000 = $result0000->fetch_assoc()) {
        $citylistAll[] = $row0000['City'];
    }
    $output["City Name List All: "] = $citylistAll;
}

echo json_encode($output);
?>
