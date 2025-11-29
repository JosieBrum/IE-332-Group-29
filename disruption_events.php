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
//https://flatcoding.com/tutorials/php/foreach-loop-in-php/
foreach($tmp as $key => $value) {
    $$key = $value;  
}

//https://www.geeksforgeeks.org/php/php-switch-statement/
//https://stackoverflow.com/questions/38791309/check-if-variable-has-value-and-isnt-empty
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
if (!empty($tier)) {
    $onornot = 4;
}
if (isset($tier) && isset($continent))  {
    $onornot = 5;
}
if (isset($tier) && isset($country))  {
    $onornot = 6;
}
https://stackoverflow.com/questions/1905048/date-1-year
if (empty($date2)) {
    $date2 = date("Y-m-d");
}
if (empty($date1)) {
    $date1 = date('Y-m-d', strtotime('-1 year', strtotime($date2)) );
} 


switch ($onornot){
    case 1: 
        $filiter = "AND ('".$company."' = '' OR c.CompanyName   = '".$company."')";
        break;
    case 2:
        $filiter = "AND ('".$country."' = '' OR l.CountryName   = '".$country."')";
        break;
    case 3:
        $filiter = "AND ('".$continent."' = '' OR l.ContinentName   = '".$continent."')";
        break;
    case 4:
        $filiter = "AND ('".$tier."' = '' OR c.TierLevel   = '".$tier."')";
        break;
    case 5:
        $filiter = "AND ('".$tier."' = '' OR c.TierLevel   = '".$tier."')";
        $filiter1 = "AND ('".$continent."' = '' OR l.ContinentName   = '".$continent."')";
        break;
    case 6:
        $filiter = "AND ('".$tier."' = '' OR c.TierLevel   = '".$tier."')";
        $filiter1 = "AND ('".$country."' = '' OR l.CountryName   = '".$country."')";
        break;
    default: 
        $filiter = "AND ('' = '' OR c.CompanyName   = '')         
            AND ('' = '' OR l.CountryName   = '')         
            AND ('' = '' OR l.ContinentName = '')          
            AND ('' = '' OR c.TierLevel     = '')";
    break;

}

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

$sql1 = "SELECT 
    c.CompanyName,
    (COUNT(*) * 1.0 
        / NULLIF(TIMESTAMPDIFF(MONTH, '".$date1."', '".$date2."'), 0)
    ) AS df_per_month
FROM 
    DisruptionEvent AS e
    JOIN ImpactsCompany AS ic 
        ON e.EventID = ic.EventID
    JOIN Company AS c 
        ON ic.AffectedCompanyID = c.CompanyID
    JOIN Location AS l
        ON c.LocationID = l.LocationID
WHERE 
    e.EventDate <= '".$date2."' and e.EventRecoveryDate >= '".$date1."'
    $filiter
GROUP BY 
    c.CompanyName
ORDER BY 
    c.CompanyName DESC"; 

$sql2 = "SELECT
    c.CompanyName,
    SUM(CASE WHEN ic.ImpactLevel = 'Low'    THEN 1 ELSE 0 END) AS low_count,
    SUM(CASE WHEN ic.ImpactLevel = 'Medium' THEN 1 ELSE 0 END) AS medium_count,
    SUM(CASE WHEN ic.ImpactLevel = 'High'   THEN 1 ELSE 0 END) AS high_count
FROM DisruptionEvent AS e
JOIN ImpactsCompany AS ic 
    ON e.EventID = ic.EventID
JOIN Company AS c 
    ON ic.AffectedCompanyID = c.CompanyID
JOIN Location AS l
    ON c.LocationID = l.LocationID
WHERE     
    e.EventDate <= '".$date2."' and e.EventRecoveryDate >= '".$date1."'
    $filiter
GROUP BY c.CompanyID, c.CompanyName
ORDER BY c.CompanyName";

$sql3 = "SELECT
    (COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END) * 100.0 / NULLIF(COUNT(ic.EventID), 0)) AS HDR
FROM ImpactsCompany ic
JOIN DisruptionEvent de ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
WHERE
    de.EventDate <= '".$date2."' and de.EventRecoveryDate >= '".$date1."'
    $filiter";

$sql4 = "SELECT
    c.CompanyName, 
    (COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END) * 100.0 / NULLIF((
            SELECT COUNT(*) 
            FROM ImpactsCompany ic
            JOIN DisruptionEvent de ON ic.EventID = de.EventID
            JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
            JOIN Location l ON c.LocationID = l.LocationID
            WHERE 
              ic.ImpactLevel = 'High'
                AND de.EventDate <= '".$date2."' AND de.EventRecoveryDate >= '".$date1."'
                $filiter
        ), 0)
    ) AS Percent
FROM ImpactsCompany ic
JOIN DisruptionEvent de ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
WHERE
    de.EventDate <= '".$date2."' and de.EventRecoveryDate >= '".$date1."'
    $filiter
GROUP BY
    c.CompanyName
ORDER BY Percent DESC";

$result0=mysqli_query($conn,$sql0);
$result00=mysqli_query($conn,$sql00);
$result000=mysqli_query($conn,$sql000);
$result1=mysqli_query($conn,$sql1);
$result2=mysqli_query($conn,$sql2);
$result3=mysqli_query($conn,$sql3);
$result4=mysqli_query($conn,$sql4);

$output = array();
$companylistAll =  array();
$countrylistAll = array();
$continentlistAll = array();
$companyName = array();
$df = array();
$companyName1 = array();
$lowcount = array();
$mediumcount = array();
$highcount = array();
$companyname2 = array();
$HDRper = array();

//DF
while ($row1 = $result1->fetch_assoc()) {
    $companyName[] = $row1['CompanyName'];
    $df[] = $row1['df_per_month'];
}
$output["Company Name DF: "] = $companyName;
$output["DF: "] = $df;
//dsd
while ($row2 = $result2->fetch_assoc()) {
    $companyName1[] = $row2['CompanyName'];
    $lowcount[] = $row2['low_count'];
    $mediumcount[] = $row2['medium_count'];
    $highcount[] = $row2['high_count'];
}
$output["Company Name DSD: "] = $companyName1;
$output["Low Count: "] = $lowcount;
$output["Medium Count: "] = $mediumcount;
$output["High Count: "] = $highcount;

//hdr
while ($row4 = $result4->fetch_assoc()) {
    $companyName2[] = $row4['CompanyName'];
    $HDRper[] = $row4['Percent'];
}
$output["Company Name HRD: "] = $companyName2;
$output["HDR Percents: "] = $HDRper;

//Lists of everything 
if($onornot == 1) {
    while ($row0 = $result0->fetch_assoc()) {
        $companylistAll[] = $row0['CompanyName'];
    }
    $output["Company Name List All: "] = $companylistAll;
}
if($onornot == 2 || $onornot == 6) {
    while ($row00 = $result00->fetch_assoc()) {
        $countrylistAll[] = $row00['CountryName'];
    }
    $output["Country Name List All: "] = $countrylistAll;
}
if($onornot == 3 || $onornot == 5) {
    while ($row000 = $result000->fetch_assoc()) {
        $continentlistAll[] = $row000['ContinentName'];
    }
    $output["Continent Name List All: "] = $continentlistAll;
}
echo json_encode($output);

?>