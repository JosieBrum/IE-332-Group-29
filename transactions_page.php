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
date_default_timezone_set("America/New_York");
$tmp = $_GET;

foreach($tmp as $key => $value) {
    $$key = $value;  
}

// list of companies for checking 
$sql0 = "
    SELECT DISTINCT
        Company.CompanyName 
    FROM Company";
//list of countries for checking 
$sql00 = "
    SELECT DISTINCT
        Location.CountryName 
    FROM Location";
// list of continents for checking 
$sql000 = "
    SELECT DISTINCT
        Location.ContinentName 
    FROM Location";
$sql0000 = "
    SELECT DISTINCT
        Location.CityName 
    FROM Location";

    // shipment volume 
$sql1 = "SELECT
    COUNT(*) AS NumberOfShipments,
    SUM(s.Quantity) AS TotalShipmentVolume,
    SUM(s.Quantity) / COUNT(*) AS AverageShipmentVolume
FROM Shipping s
JOIN InventoryTransaction t ON t.TransactionID = s.TransactionID
JOIN Distributor d ON d.CompanyID = s.DistributorID
JOIN Company c ON c.CompanyID = d.CompanyID
WHERE t.Type = 'Shipping'
  AND s.ActualDate BETWEEN '".$date1."' AND '".$date2."'
  AND c.CompanyName = '".$company."'";

  //shipment volume graph
$sql2 = "SELECT
    c.CompanyName,
    SUM(s.Quantity) AS TotalShipmentVolume
FROM Shipping s
JOIN InventoryTransaction t ON t.TransactionID = s.TransactionID
JOIN Distributor d ON d.CompanyID = s.DistributorID
JOIN Company c ON c.CompanyID = d.CompanyID
WHERE t.Type = 'Shipping'
  AND s.ActualDate BETWEEN '".$date1."' AND '".$date2."'
Group By c.CompanyName";

//on time delivery rate
$sql3 = "SELECT 
    ROUND(
        100.0 * SUM(CASE 
            WHEN s.ActualDate <= s.PromisedDate 
            THEN 1 
            ELSE 0 
        END) / COUNT(*),
        2
    ) AS on_time_delivery_rate_percent
FROM 
    Shipping AS s
    JOIN Company AS c ON s.DistributorID = c.CompanyID
WHERE 
  s.ActualDate BETWEEN '".$date1."' AND '".$date2."'
  AND c.CompanyName = '".$company."'";

//disruption exposure
$sql4 = "SELECT
    COUNT(CASE WHEN ic.ImpactLevel = 'Low' THEN 1 END) AS LowImpactEvents,
    COUNT(CASE WHEN ic.ImpactLevel = 'Medium' THEN 1 END) AS MediumImpactEvents,
    COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END) AS HighImpactEvents,
    (COUNT(ic.EventID) + (2 * COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END))) AS DisruptionExposure
FROM ImpactsCompany ic
JOIN DisruptionEvent de ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
WHERE
    de.EventDate <= '".$date2."' and de.EventRecoveryDate >= '".$date1."'
  AND c.CompanyName = '".$company."' ";

// products handeled
$sql5 = "SELECT 
    p.ProductName
FROM Shipping AS s
JOIN Product AS p  
    ON s.ProductID = p.ProductID
JOIN Company AS dist  
    ON s.DistributorID = dist.CompanyID
JOIN Location AS l  
    ON dist.LocationID = l.LocationID
WHERE
  s.ActualDate BETWEEN '".$date1."' AND '".$date2."'
  AND dist.CompanyName = '".$company."'
ORDER BY
    p.ProductID ASC, p.ProductName ASC";

    // outsanding 
$sql6 = "SELECT 
    COUNT(s.ShipmentID) AS OutstandingShipments
FROM Shipping s
JOIN Company dist ON s.DistributorID = dist.CompanyID
WHERE s.ActualDate IS NULL
  and s.ActualDate BETWEEN '".$date1."' AND '".$date2."'
  AND dist.CompanyName = '".$company."' ";

  // delievered 
$sql7 = "SELECT 
    COUNT(s.ShipmentID) AS DeliveredShipments
FROM Shipping s
JOIN Company dist ON s.DistributorID = dist.CompanyID
WHERE s.ActualDate IS NOT NULL
  and s.ActualDate BETWEEN '".$date1."' AND '".$date2."'
  AND dist.CompanyName = '".$company."' ";

$result0=mysqli_query($conn,$sql0);
$result00=mysqli_query($conn,$sql00);
$result000=mysqli_query($conn,$sql000);
$result0000=mysqli_query($conn,$sql0000);
$result1=mysqli_query($conn,$sql1);
$result2=mysqli_query($conn,$sql2);
$result3=mysqli_query($conn,$sql3);
$result4=mysqli_query($conn,$sql4);
$result5=mysqli_query($conn,$sql5);
$result6=mysqli_query($conn,$sql6);
$result7=mysqli_query($conn,$sql7);

$output = array();
$companylistAll =  array();
$countrylistAll = array();
$continentlistAll = array();
$citylistAll = array();
$NumberOfShipments = array();
$TotalShipmentVolume = array();
$AverageShipmentVolume = array();
$TotalShipmentVolumeList = array();
$CompanyNamesforShipments = array();
$on_time_delivery_rate_percent = array();
$impacts = array();
$DisruptionExposure = array();
$OutstandingShipments = array();
$Products = array();
$DeliveredShipments = array();

//Lists of everything for checking 
if(isset($company)) {
    while ($row0 = $result0->fetch_assoc()) {
        $companylistAll[] = $row0['CompanyName'];
    }
    $output["Company Name List All: "] = $companylistAll;
}
if(isset($country)) {
    while ($row00 = $result00->fetch_assoc()) {
        $countrylistAll[] = $row00['CountryName'];
    }
    $output["Country Name List All: "] = $countrylistAll;
}
if(isset($continent)) {
    while ($row000 = $result000->fetch_assoc()) {
        $continentlistAll[] = $row000['ContinentName'];
    }
    $output["Continent Name List All: "] = $continentlistAll;
}
if(isset($city)) {
    while ($row0000 = $result0000->fetch_assoc()) {
        $citylistAll[] = $row0000['CityName'];
    }
    $output["City Name List All: "] = $citylistAll;
}

// shipment volume 
while ($row1 = $result1->fetch_assoc()) {
    $NumberOfShipments[] = $row1['NumberOfShipments'];
    $TotalShipmentVolume[] = $row1['TotalShipmentVolume'];
    $AverageShipmentVolume[] = $row1['AverageShipmentVolume'];
}
$output["Number of Shipments:"] = $NumberOfShipments;
$output["Total Shipment Volume:"] = $TotalShipmentVolume;
$output["Average Shipment Volume:"] = $AverageShipmentVolume;

// shipment volume chart 
while ($row2 = $result2->fetch_assoc()) {
    $CompanyNamesforShipments[] = $row2['CompanyName'];
    $TotalShipmentVolumeList[] = $row2['TotalShipmentVolume'];
}
$output["Company Names for Shipment Volume:"] = $CompanyNamesforShipments;
$output["Total Shipment Volume List:"] = $TotalShipmentVolumeList;

// on time deleivery 
while ($row3 = $result3->fetch_assoc()) {
    $on_time_delivery_rate_percent[] = $row3['on_time_delivery_rate_percent'];
}
$output["on time delivery rate percent:"] = $on_time_delivery_rate_percent;

//disruption exposure
while ($row4 = $result4->fetch_assoc()) {
    $impacts[] = $row4['LowImpactEvents'];
    $impacts[] = $row4['MediumImpactEvents'];
    $impacts[] = $row4['HighImpactEvents'];
    $DisruptionExposure[] = $row4['DisruptionExposure'];
}
$output["Impacts(low,medium,high):"] = $impacts;
$output["DisruptionExposure:"] = $DisruptionExposure;

// products handeled
while ($row5 = $result5->fetch_assoc()) {
    $Products[] = $row5['ProductName'];
}
$output["Products Handeled List:"] = $Products;

//outsanding shipments 
while ($row6 = $result6->fetch_assoc()) {
    $OutstandingShipments[] = $row6['OutstandingShipments'];
}
$output["Outstanding Shipments:"] = $OutstandingShipments;

//delievered shipments 
while ($row7 = $result7->fetch_assoc()) {
    $DeliveredShipments[] = $row7['DeliveredShipments'];
}
$output["Delievered Shipments:"] = $DeliveredShipments;

echo json_encode($output);
?>

