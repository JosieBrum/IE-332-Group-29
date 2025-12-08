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
//getting user inputs 
foreach($tmp as $key => $value) {
    $$key = $value;  
}
//https://www.geeksforgeeks.org/php/php-switch-statement/
//https://stackoverflow.com/questions/38791309/check-if-variable-has-value-and-isnt-empty
//getting input for switch based on user input 
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
// switch case for sql quieres based on user inputs 
$filiter = "";
$filiter1 = "";
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
        $filiter = "AND ('".$tier."' = '' OR c.TierLevel   = '".$tier."') ";
        break;
    case 5:
        $filiter = "AND ('".$tier."' = '' OR c.TierLevel   = '".$tier."') ";
        $filiter1 = "AND ('".$continent."' = '' OR l.ContinentName   = '".$continent."')";
        break;
    case 6:
        $filiter = "AND ('".$tier."' = '' OR c.TierLevel   = '".$tier."') ";
        $filiter1 = "AND ('".$country."' = '' OR l.CountryName   = '".$country."')";
        break;
    default: 
        $filiter = "AND ('' = '' OR c.CompanyName   = '')         
            AND ('' = '' OR l.CountryName   = '')         
            AND ('' = '' OR l.ContinentName = '')          
            AND ('' = '' OR c.TierLevel     = '') ";
    break;

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

// df
$sql1 = "SELECT 
    c.CompanyName,
    (COUNT(*) * 1.0  
        / NULLIF(TIMESTAMPDIFF(MONTH, '".$date1."', '".$date2."') + 1, 0)
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
	e.EventDate <= '".$date2."' 
and e.EventRecoveryDate >= '".$date1."'
    $filiter
    $filiter1
GROUP BY 
    c.CompanyName
ORDER BY 
    df_per_month DESC"; 

// dsd
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
    $filiter1
GROUP BY c.CompanyID, c.CompanyName
ORDER BY c.CompanyName";

//hdr percents
$sql4_1 = "SELECT
    c.CompanyName,
    (COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END) * 100.0 / NULLIF((
            SELECT COUNT(*) 
            FROM ImpactsCompany ic
            JOIN DisruptionEvent de ON ic.EventID = de.EventID
            JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
            JOIN Location l ON c.LocationID = l.LocationID
            WHERE     
                de.EventDate <= '".$date2."' and de.EventRecoveryDate >= '".$date1."'
                $filiter
                $filiter1
                AND ic.ImpactLevel = 'High'         
        ), 0)
    ) AS Percent
FROM ImpactsCompany ic
JOIN DisruptionEvent de ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
WHERE
    de.EventDate <= '".$date2."' and de.EventRecoveryDate >= '".$date1."'
    $filiter
    $filiter1
GROUP BY
    c.CompanyName
ORDER BY Percent DESC;";

// hdr scores 
$sql4_2 = "SELECT
    c.CompanyName,
    (COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END) * 100.0 / NULLIF(COUNT(ic.EventID), 0)) AS CompanyHDR
FROM ImpactsCompany ic
JOIN DisruptionEvent de ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
WHERE
    de.EventDate <= '".$date2."' and de.EventRecoveryDate >= '".$date1."'
    $filiter
    $filiter1
GROUP BY
    c.CompanyName
ORDER BY CompanyHDR DESC;";

// hdr overall 
$sql4_3 = "SELECT
    (COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END) * 100.0 / NULLIF(COUNT(ic.EventID), 0)) AS HDR
FROM ImpactsCompany ic
JOIN DisruptionEvent de ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
WHERE
    de.EventDate <= '".$date2."' and de.EventRecoveryDate >= '".$date1."'
    $filiter
    $filiter1";


// rrc country
$sql5 = "SELECT l.CountryName, COUNT( DISTINCT de.EventID ) * 100.0 / total.TotalEvents AS RRC
FROM DisruptionEvent de
JOIN ImpactsCompany ic ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
CROSS JOIN (
SELECT COUNT( DISTINCT de2.EventID ) AS TotalEvents
FROM DisruptionEvent de2
JOIN ImpactsCompany ic2 ON ic2.EventID = de2.EventID
JOIN Company c2 ON ic2.AffectedCompanyID = c2.CompanyID
WHERE 
de2.EventDate <= '".$date2."' and de2.EventRecoveryDate >= '".$date1."'
) AS total
WHERE 
de.EventDate <= '".$date2."' and de.EventRecoveryDate >= '".$date1."'   
$filiter
$filiter1
GROUP BY l.CountryName, total.TotalEvents
ORDER BY RRC DESC";

// rrc continenet
$sql6 = "SELECT l.ContinentName, COUNT( DISTINCT de.EventID ) * 100.0 / total.TotalEvents AS RRC
FROM DisruptionEvent de
JOIN ImpactsCompany ic ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
CROSS JOIN (
SELECT COUNT( DISTINCT de2.EventID ) AS TotalEvents
FROM DisruptionEvent de2
JOIN ImpactsCompany ic2 ON ic2.EventID = de2.EventID
JOIN Company c2 ON ic2.AffectedCompanyID = c2.CompanyID
WHERE 
de2.EventDate <= '".$date2."' and de2.EventRecoveryDate >= '".$date1."'
) AS total
WHERE 
de.EventDate <= '".$date2."' and de.EventRecoveryDate >= '".$date1."'      
$filiter
$filiter1
GROUP BY l.ContinentName, total.TotalEvents
ORDER BY RRC DESC";

// TD 
$sql7 = "SELECT
    SUM(de.RecoveryDays) AS TD
FROM (
    SELECT
        e.EventId,
        DATEDIFF(e.EventRecoveryDate, e.EventDate) AS RecoveryDays
    FROM DisruptionEvent AS e
    WHERE
        e.EventDate <= '".$date2."' and e.EventRecoveryDate >= '".$date1."' 
        AND EXISTS (
            SELECT ic.EventID
            FROM ImpactsCompany AS ic
            JOIN Company  AS c ON c.CompanyId  = ic.AffectedCompanyID
            JOIN Location AS l ON l.LocationId = c.LocationId
            WHERE ic.EventId = e.EventId
            $filiter
            $filiter1
            )
    GROUP BY e.EventId
) AS de";

//ART
$sql8 = "SELECT
    AVG(de.RecoveryDays) AS ART
FROM (
    SELECT
        e.EventId,
        DATEDIFF(e.EventRecoveryDate, e.EventDate) AS RecoveryDays
    FROM DisruptionEvent AS e
    WHERE
        e.EventDate <= '".$date2."' and e.EventRecoveryDate >= '".$date1."'
        AND EXISTS (
            SELECT ic.EventID
            FROM ImpactsCompany AS ic
            JOIN Company  AS c ON c.CompanyId  = ic.AffectedCompanyID
            JOIN Location AS l ON l.LocationId = c.LocationId
            WHERE ic.EventId = e.EventId
                $filiter
                $filiter1
                )
    GROUP BY e.EventId
) AS de";

// Histrogram 
$sql9 = "SELECT
e.EventID,
    DATEDIFF(e.EventRecoveryDate, e.EventDate) AS RecoveryDays
FROM DisruptionEvent AS e
WHERE
    e.EventDate <= '".$date2."' and e.EventRecoveryDate >= '".$date1."'
    AND EXISTS (
        SELECT ic.EventID
        FROM ImpactsCompany AS ic
        JOIN Company  AS c ON c.CompanyId  = ic.AffectedCompanyID
        JOIN Location AS l ON l.LocationId = c.LocationId
        WHERE ic.EventId = e.EventId
            $filiter
            $filiter1
    )
GROUP BY e.EventId
ORDER BY e.EventId ASC;";

$result0=mysqli_query($conn,$sql0);
$result00=mysqli_query($conn,$sql00);
$result000=mysqli_query($conn,$sql000);
$result1=mysqli_query($conn,$sql1);
$result2=mysqli_query($conn,$sql2);
$result4_1=mysqli_query($conn,$sql4_1);
$result4_2=mysqli_query($conn,$sql4_2);
$result4_3=mysqli_query($conn,$sql4_3);
$result5=mysqli_query($conn,$sql5);
$result6=mysqli_query($conn,$sql6);
$result7=mysqli_query($conn,$sql7);
$result8=mysqli_query($conn,$sql8);
$result9=mysqli_query($conn,$sql9);

$output = array();
$companylistAll =  array();
$countrylistAll = array();
$continentlistAll = array();
$companyNameDF = array();
$df = array();
$companyNameDSD = array();
$lowcount = array();
$mediumcount = array();
$highcount = array();
$companyNameHDRPercent = array();
$HDRpercent = array();
$companyNameHDR = array();
$HDRpercompany = array();
$HDROVERALL = array();
$RRC = array();
$TD = array(); 
$ART = array();  
$RecoveryDays = array(); 
$EventID = array();

//DF
while ($row1 = $result1->fetch_assoc()) {
    $companyNameDF[] = $row1['CompanyName'];
    $df[] = $row1['df_per_month'];
}
$output["Company Name DF: "] = $companyNameDF;
$output["DF: "] = $df;

//dsd
while ($row2 = $result2->fetch_assoc()) {
    $companyNameDSD[] = $row2['CompanyName'];
    $lowcount[] = $row2['low_count'];
    $mediumcount[] = $row2['medium_count'];
    $highcount[] = $row2['high_count'];
}
$output["Company Name DSD: "] = $companyNameDSD;
$output["Low Count: "] = $lowcount;
$output["Medium Count: "] = $mediumcount;
$output["High Count: "] = $highcount;

//hdr percents
while ($row4_1 = $result4_1->fetch_assoc()) {
    $companyNameHDRPercent[] = $row4_1['CompanyName'];
    $HDRpercent[] = $row4_1['Percent'];
}
$output["Company Name HRD Percent: "] = $companyNameHDRPercent;
$output["HDR Percents: "] = $HDRpercent;

//hdr scores per company
while ($row4_2 = $result4_2->fetch_assoc()) {
    $companyNameHDR[] = $row4_2['CompanyName'];
    $HDRpercompany[] = $row4_2['CompanyHDR'];
}
$output["Company Name HRD: "] = $companyNameHDR;
$output["HDR Company Scores: "] = $HDRpercompany;

//hdr overall
while ($row4_3 = $result4_3->fetch_assoc()) {
    $HDROVERALL[] = $row4_3['HDR'];
}
$output["HRD Overall: "] = $HDROVERALL;

//td overall
while ($row7 = $result7->fetch_assoc()) {
    $TD[] = $row7['TD'];
}
$output["TD: "] = $TD;

//ART overall
while ($row8 = $result8->fetch_assoc()) {
    $ART[] = $row8['ART'];
}
$output["ART: "] = $ART;

//Histogram 
while ($row9 = $result9->fetch_assoc()) {
    $RecoveryDays[] = $row9['RecoveryDays'];
    $EventID[] = $row9['EventID'];
}
$output["RecoveryDays: "] = $RecoveryDays;
$output["EventID: "] = $EventID;


//https://www.w3schools.com/php/php_arrays_associative.asp
if (isset($continent)) {
    while ($row6 = $result6->fetch_assoc()) {
        $RRC[] = array("continent" =>$row6['ContinentName'],
        "rrc value" =>$row6['RRC']);
    }
    $output["RRC"] = $RRC;
}

if (isset($country)) {
    while ($row5 = $result5->fetch_assoc()) {
        $RRC[] = array("country" =>$row5['CountryName'],
        "rrc value" =>$row5['RRC']);
    }
    $output["RRC"] = $RRC;
}

//Lists of everything for checking 
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