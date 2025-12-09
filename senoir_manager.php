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
date_default_timezone_set('America/New_York');
$tmp = $_GET;

foreach($tmp as $key => $value) {
    $$key = $value;  
}
$date111 = new DateTime($date1);
$date222 = new DateTime($date2);
$date3 = clone $date111;
$date4 = clone $date222;
$date5 = clone $date111;
$date6 = clone $date222;
$year1 = $date3->format('Y');
$year2 = $date4->format('Y');
$month1 = intval($date5->format('m'));
$month2 = intval($date6->format('m'));
$d1 = $date111->format("Y-m-d");
$d2 = $date222->format("Y-m-d");


if($month1 < 4) {
    $quarter1 = "Q1";
}
else if($month1 < 7) {
    $quarter1 = "Q2";
}
else if($month1 <10) {
    $quarter1 = "Q3";
}
else {
    $quarter1 = "Q4";
}
if($month2 < 4) {
    $quarter2 = "Q1";
}
else if($month2 < 7) {
    $quarter2 = "Q2";
}
else if($month2 <10) {
    $quarter2 = "Q3";
}
else {
    $quarter2 = "Q4";
}

if(isset($type)) {
    $avgfinhealth = "('".$type."' = '' OR c.Type = '".$type."')";
}
else {
    $avgfinhealth = "('' = '' OR c.Type = '')";
}

$sql1 = "SELECT
    c.CompanyName,
    AVG(fr.HealthScore) AS AvgFinancialHealth
FROM Company c
JOIN FinancialReport fr ON fr.CompanyID = c.CompanyID
WHERE $avgfinhealth
  AND (
        (fr.RepYear = '".$year1."' AND fr.Quarter >= '".$quarter1."') -- INITIAL DATE AND INITIAL QUARTER
        OR (fr.RepYear > '".$year1."' AND fr.RepYear < '".$year2."') -- INITIAL DATE AND END DATE
        OR (fr.RepYear = '".$year2."' AND fr.Quarter <= '".$quarter2."') -- END DATE AND END QUARTER
      )
GROUP BY c.CompanyID, c.CompanyName
ORDER BY AvgFinancialHealth DESC ";

$result1=mysqli_query($conn,$sql1);
$companyNamefinheath = array();
$AvgFinancialHelathName = array();
$output = array();
while ($row = mysqli_fetch_array($result1)) {
    $companyNamefinheath[] = $row['CompanyName'];
    $AvgFinancialHelathName[] = $row['AvgFinancialHealth'];
}
$output['Company Name Fin Health'] = $companyNamefinheath;
$output['Avg Fin Health'] = $AvgFinancialHelathName;

$sql7 = "SELECT
            DATE_FORMAT(e.EventDate, '%Y-%m') AS Period,
            COUNT(*) 
                AS DisruptionCount
        FROM DisruptionEvent e
        WHERE e.EventDate <= '".$d2."' AND e.EventRecoveryDate >= '".$d1."'
        GROUP BY 
            DATE_FORMAT(e.EventDate, '%Y-%m')
        ORDER BY 
            Period ASC;";
$result7=mysqli_query($conn,$sql7);
while ($row6 = mysqli_fetch_array($result7)) {
    $dsiruptioncount[] = array("period" =>$row6['Period'],
    "disruptionCount" => $row6['DisruptionCount']);
}
$output["DisruptionCount"] = $dsiruptioncount;

if (isset($regional) && $regional == "Country") {
    if(isset($tier)) {
        $regionalfiliter = "AND ('".$tier."' = '' OR c2.TierLevel   = '".$tier."') ";
        $regionalfiliter1 = "AND ('".$tier."' = '' OR c.TierLevel   = '".$tier."') ";
    }
    else {
        $regionalfiliter = "AND ('' = '' OR c2.TierLevel   = '') ";
        $regionalfiliter1 = "AND ('' = '' OR c.TierLevel   = '') ";
    }
    $sql2 = "SELECT
                l.CountryName,
                COUNT(DISTINCT CASE WHEN ic.ImpactLevel = 'High' THEN de.EventID END) AS HighImpactEvents,
                total.TotalEvents,
                (COUNT(DISTINCT CASE WHEN ic.ImpactLevel = 'High' THEN de.EventID END) * 100 / total.TotalEvents) AS PercentHigh
            FROM DisruptionEvent de
            JOIN ImpactsCompany ic ON ic.EventID = de.EventID
            JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
            JOIN Location l ON c.LocationID = l.LocationID
            CROSS JOIN (
                SELECT COUNT(DISTINCT e2.EventID) AS TotalEvents
                FROM DisruptionEvent e2
                WHERE
                e2.EventDate <= '".$d2."' AND e2.EventRecoveryDate >= '".$d1."'
                AND EXISTS (
                    SELECT *
                    FROM ImpactsCompany ic2
                    JOIN Company  c2 ON c2.CompanyID  = ic2.AffectedCompanyID
                    JOIN Location l2 ON l2.LocationID = c2.LocationID
                    WHERE ic2.EventID = e2.EventID
                    $regionalfiliter
                )
            ) AS total
            WHERE
                de.EventDate <= '".$d2."' AND de.EventRecoveryDate >= '".$d1."'
                $regionalfiliter1
            GROUP BY
                l.CountryName,
                total.TotalEvents
            ORDER BY
                HighImpactEvents DESC";
        $result2=mysqli_query($conn,$sql2);
        $countryName = array();
        $highimpacts = array();
        $totalevents = array();
        $percenthigh = array();
        while ($row1 = mysqli_fetch_array($result2)) {
            $countryName[] = $row1['CountryName'];
            $highimpacts[] = $row1['HighImpactEvents'];
            $totalevents = $row1['TotalEvents'];
            $percenthigh[] = $row1['PercentHigh'];
        }
        $output['CountryName'] = $countryName;
        $output['HighImpactEvents'] = $highimpacts;
        $output['TotalEvents'] = $totalevents;
        $output['PercentHigh'] = $percenthigh;
}
if (isset($regional) && $regional == "Continent") {
    if(isset($tier)) {
        $regionalfiliter = "AND ('".$tier."' = '' OR c2.TierLevel   = '".$tier."') ";
        $regionalfiliter1 = "AND ('".$tier."' = '' OR c.TierLevel   = '".$tier."') ";
    }
    else {
        $regionalfiliter = "AND ('' = '' OR c2.TierLevel   = '') ";
        $regionalfiliter1 = "AND ('' = '' OR c.TierLevel   = '') ";
    }
    $sql3 = "SELECT
        l.ContinentName,
        COUNT(DISTINCT CASE WHEN ic.ImpactLevel = 'High' THEN de.EventID END) AS HighImpactEvents,
        total.TotalEvents,
        (COUNT(DISTINCT CASE WHEN ic.ImpactLevel = 'High' THEN de.EventID END) * 100 / total.TotalEvents) AS PercentHigh
        FROM DisruptionEvent de
        JOIN ImpactsCompany ic ON ic.EventID = de.EventID
        JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
        JOIN Location l ON c.LocationID = l.LocationID
        CROSS JOIN (
        SELECT COUNT(DISTINCT e2.EventID) AS TotalEvents
        FROM DisruptionEvent e2
        WHERE
        e2.EventDate <= '".$d2."' AND e2.EventRecoveryDate >= '".$d1."'
        AND EXISTS (
            SELECT *
            FROM ImpactsCompany ic2
            JOIN Company  c2 ON c2.CompanyID  = ic2.AffectedCompanyID
            JOIN Location l2 ON l2.LocationID = c2.LocationID
            WHERE ic2.EventID = e2.EventID
            $regionalfiliter
            )
        ) AS total
        WHERE
            de.EventDate <= '".$d2."' AND de.EventRecoveryDate >= '".$d1."'
            $regionalfiliter1
        GROUP BY
            l.ContinentName,
            total.TotalEvents
        ORDER BY
            HighImpactEvents DESC";
        $result3=mysqli_query($conn,$sql3);
        $continentName = array();
        $highimpacts = array();
        $totalevents = array();
        $percenthigh = array();
        while ($row2 = mysqli_fetch_array($result3)) {
            $continentName[] = $row2['ContinentName'];
            $highimpacts[] = $row2['HighImpactEvents'];
            $totalevents = $row2['TotalEvents'];
            $percenthigh[] = $row2['PercentHigh'];
        }
        $output['CountryName'] = $continentName;
        $output['HighImpactEvents'] = $highimpacts;
        $output['TotalEvents'] = $totalevents;
        $output['PercentHigh'] = $percenthigh;
}

    $onornot = 0;
    if (isset($country)) {
        $onornot = 1;
    }
    if (isset($continent)) {
        $onornot = 2;
    }
    if (!empty($tier)) {
        $onornot = 3;
    }
    if (isset($tier) && isset($continent))  {
        $onornot = 4;
    }
    if (isset($tier) && isset($country))  {
        $onornot = 5;
    }
    // switch case for sql quieres based on user inputs 
    $filiter = "";
    $filiter1 = "";
    switch ($onornot){
        case 1:
            $filiter = "AND ('".$country."' = '' OR l.CountryName   = '".$country."') ";
            break;
        case 2:
            $filiter = "AND ('".$continent."' = '' OR l.ContinentName   = '".$continent."') ";
            break;
        case 3:
            $filiter = "AND ('".$tier."' = '' OR c.TierLevel   = '".$tier."') ";
            break;
        case 4:
            $filiter = "AND ('".$tier."' = '' OR c.TierLevel   = '".$tier."') ";
            $filiter1 = "AND ('".$continent."' = '' OR l.ContinentName   = '".$continent."')";
            break;
        case 5:
            $filiter = "AND ('".$tier."' = '' OR c.TierLevel   = '".$tier."') ";
            $filiter1 = "AND ('".$country."' = '' OR l.CountryName   = '".$country."')";
            break;
        default: 
            $filiter = "     
                AND ('' = '' OR l.CountryName   = '')         
                AND ('' = '' OR l.ContinentName = '')          
                AND ('' = '' OR c.TierLevel     = '') ";
                break;
    }
    $sql4 = "SELECT
                c.CompanyName,
                COALESCE(downstream.NumDownstream, 0) * COALESCE(impacts.HighImpactCount, 0) 
                    AS Criticality
            FROM Company c
            JOIN Location l ON l.LocationID = c.LocationID
            LEFT JOIN (
                SELECT 
                    UpstreamCompanyID,
                    COUNT(DISTINCT DownstreamCompanyID) 
                        AS NumDownstream
                FROM DependsOn
                GROUP BY UpstreamCompanyID
            ) AS downstream 
                ON downstream.UpstreamCompanyID = c.CompanyID
            LEFT JOIN (
                SELECT 
                    AffectedCompanyID,
                    SUM(CASE WHEN ImpactLevel = 'High' THEN 1 ELSE 0 END) 
                        AS HighImpactCount
                FROM ImpactsCompany
                GROUP BY AffectedCompanyID
            ) AS impacts 
                ON impacts.AffectedCompanyID = c.CompanyID
            WHERE 1 = 1
            $filiter
            $filiter1
            ORDER BY 
                Criticality DESC";
    $result4=mysqli_query($conn,$sql4);
    $companyNamecrit = array();
    $criticality = array();
        while ($row3 = mysqli_fetch_array($result4)) {
            $companyNamecrit[] = $row3['CompanyName'];
            $criticality[] = $row3['Criticality'];
        }
    $output['Company Criticality'] = $companyNamecrit;
    $output['Criticality'] = $criticality;

    $sql5 = "SELECT
                c.CompanyName AS Distributor,
                AVG(DATEDIFF(s.ActualDate, s.PromisedDate)) AS AvgDelayDays
            FROM Shipping s
            JOIN InventoryTransaction t 
                ON t.TransactionID = s.TransactionID
            JOIN Distributor d 
                ON d.CompanyID = s.DistributorID
            JOIN Company c 
                ON c.CompanyID = d.CompanyID
            JOIN Location l 
                ON l.LocationID = c.LocationID
            WHERE t.Type = 'Shipping'
            $filiter
            $filiter1
            GROUP BY 
                c.CompanyID, 
                c.CompanyName
            ORDER BY 
                AvgDelayDays DESC";
    $result5=mysqli_query($conn,$sql5);
    $companyNamedisdel = array();
    $avgdel = array();
        while ($row4 = mysqli_fetch_array($result5)) {
            $companyNamedisdel[] = $row4['Distributor'];
            $avgdel[] = $row4['AvgDelayDays'];
        }
    $output['Distributors Avg'] = $companyNamedisdel;
    $output['AvgDelayDyas'] = $avgdel;

    $sql6 = "SELECT
        c.CompanyName AS Distributor,
        SUM(s.Quantity) AS TotalShipmentVolume
    FROM Shipping s
    JOIN InventoryTransaction t 
        ON t.TransactionID = s.TransactionID
    JOIN Distributor d 
        ON d.CompanyID = s.DistributorID
    JOIN Company c 
        ON c.CompanyID = d.CompanyID
    JOIN Location l 
        ON l.LocationID = c.LocationID
    WHERE t.Type = 'Shipping'
            $filiter
            $filiter1
    GROUP BY 
        c.CompanyID, 
        c.CompanyName
    ORDER BY 
        TotalShipmentVolume DESC";
    $result6=mysqli_query($conn,$sql6);
    $companyNamedisvol = array();
    $totalvol = array();
        while ($row5 = mysqli_fetch_array($result6)) {
            $companyNamedisvol[] = $row5['Distributor'];
            $totalvol[] = $row5['TotalShipmentVolume'];
        }
    $output['Distributors Volume'] = $companyNamedisvol;
    $output['TotalShipmentVolume'] = $totalvol;

    $sql10 = "SELECT
                c.CompanyName,
                fr.Quarter,
                fr.RepYear,
                fr.HealthScore
            FROM Company c
            JOIN FinancialReport fr 
                ON fr.CompanyID = c.CompanyID
            JOIN Location l 
                ON l.LocationID = c.LocationID
            WHERE 
                (
                    (fr.RepYear = '".$d1."' AND fr.Quarter >= '".$quarter1."')
                    OR (fr.RepYear > '".$d1."' AND fr.RepYear < '".$d2."')
                    OR (fr.RepYear = '".$d2."' AND fr.Quarter <= '".$quarter2."')
                )
                $filiter
                $filiter1
            ORDER BY c.CompanyName, fr.RepYear, fr.Quarter";
    $result10=mysqli_query($conn,$sql10);
    $companyNameFinreg = array();
    $quarterfinreg = array();
    $yearfinreg = array();
    $healthscorefinreg = array();
        while ($row10 = mysqli_fetch_array($result10)) {
            $companyNameFinreg[] = $row10['CompanyName'];
            $quarterfinreg[] = $row10['Quarter'];
            $yearfinreg[] = $row10['RepYear'];
            $healthscorefinreg[] = $row10['HealthScore'];
        }
    $output['Company Name Fin Reg'] = $companyNameFinreg;
    $output['Quater Fin Reg'] = $quarterfinreg;
    $output['Year Fin Reg'] = $yearfinreg;
    $output['Health Score Fin Reg'] = $healthscorefinreg;


if(isset($companyName) ) {
    $sql8 = "SELECT
                e.EventId,
                dc.CategoryName
            FROM DisruptionEvent AS e
            JOIN DisruptionCategory AS dc
            ON dc.CategoryID = e.CategoryID
            JOIN ImpactsCompany AS ic
            ON ic.EventId = e.EventId
            JOIN Company AS c
            ON c.CompanyId = ic.AffectedCompanyID
            WHERE
                ('".$companyName."' = '' OR c.CompanyName = '".$companyName."')
            ORDER BY e.EventDate ASC, e.EventId";
    $result8=mysqli_query($conn,$sql8);
    $categoryName1 = array();
    $eventID = array();
    while ($row7 = mysqli_fetch_array($result8)) {
        $categoryName1[] = $row7['CategoryName'];
        $eventID[] = $row7['EventId'];    
    }
    $output['EventID'] = $eventID;
    $output['CategoryName'] = $categoryName1;
}

if(isset($categoryName) && isset($eventDate)) {
    $date113 = new DateTime($eventDate);
    $d3 = $date113->format("Y-m-d");
    $sql9 = "SELECT DISTINCT
                c.CompanyName
            FROM DisruptionEvent AS e
            JOIN DisruptionCategory AS dc
            ON dc.CategoryID = e.CategoryID
            JOIN ImpactsCompany AS ic
            ON ic.EventId = e.EventId
            JOIN Company AS c
            ON c.CompanyId = ic.AffectedCompanyID
            WHERE
                ('".$eventDate."' = '' OR e.EventDate = '".$eventDate."' )
                AND ('".$categoryName."' = '' OR dc.CategoryName = '".$categoryName."')
            ORDER BY c.CompanyName";
    $result9=mysqli_query($conn,$sql9);
    $companyName = array();
    while ($row8 = mysqli_fetch_array($result9)) {
        $companyName[] = $row8['CompanyName'];   
    }
    $output['CompanyName'] = $companyName; 
}
echo json_encode($output);
$conn->close();
?>
