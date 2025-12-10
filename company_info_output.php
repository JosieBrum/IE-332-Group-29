<?php
// Database connection
$servername = "mydb.itap.purdue.edu";
$username = "g1151939";
$password = "Group29jjmsio";
$database = $username;

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tmp= $_GET['q'];
$tmp=explode('|',$tmp);
//get company name 
$companyName = $tmp[2];
//https://stackoverflow.com/questions/2579458/how-do-i-deep-copy-a-datetime-object
//https://www.php.net/manual/en/datetime.createfromformat.php
//https://www.php.net/manual/en/datetime.modify.php
//https://www.geeksforgeeks.org/php/php-datetime-createfromformat-function
//https://www.geeksforgeeks.org/php/how-to-copy-a-datetime-object-in-php
//https://www.digittrix.com/scripts/php-date-and-time-functions-ultimate-guide-with-examples
date_default_timezone_set('America/New_York'); 
if (!empty($tmp[0]) && !empty($tmp[1])) {
    $date1 = DateTime::createFromFormat('Y-m-d', $tmp[0]);
    $date2 = DateTime::createFromFormat('Y-m-d',$tmp[1]);
    $date3 = clone $date2;
    $date4 = clone $date2;
    $date5 = new DateTime();
    $date6 = clone $date5;
    $date7 = clone $date5;

}
//dates setup 
$datebefore = $date3->modify('-1 year');
$datebefore1 = $date6->modify('-1 year');
$year1 = $datebefore1->format('Y');

$year2 = $date7->format('Y');
$month2 = intval($date7->format('m'));

$date1_f = $date1->format('Y-m-d');
$date2_f = $date2->format('Y-m-d');
$date3_f = $datebefore->format('Y-m-d');
$date5_f = $date5->format('Y-m-d');

//figuring out the quaters for fin health 
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
// listof companies for checking 
$sql0 = "
    SELECT DISTINCT
        Company.CompanyName 
    FROM Company";
 
// Canpany Nane,Location, Manufacturer. 
$sql1 = "
    SELECT 
        Company.CompanyName, 
        Location.CountryName, 
        Location.ContinentName, 
        Location.City,
        Company.Type,
        Company.TierLevel,
        Manufacturer.FactoryCapacity
    FROM Company
    JOIN Location 
        ON Company.LocationID = Location.LocationID
    LEFT JOIN Manufacturer 
        ON Company.CompanyID = Manufacturer.CompanyID
    WHERE Company.CompanyName = '".$companyName."'
";

//  Products 
$sql2 = " SELECT ProductName 
            FROM Product 
            WHERE productid in (SELECT ProductID FROM SuppliesProduct Where SupplierID = (SELECT CompanyID FROM Company where CompanyName = '".$companyName."'))
";

// catageory perentages 
$sql3 = "SELECT 
    s.Category,
    (s.CategoryCount/t.TotalCategoryCount)*100 as PER
FROM (
SELECT Category, COUNT(Category) AS CategoryCount
            FROM Product
            WHERE ProductID IN (
                SELECT ProductID 
                FROM SuppliesProduct
                WHERE SupplierID = (
                    SELECT CompanyID 
                    FROM Company 
                    WHERE CompanyName = '".$companyName."'
                )
            )
            GROUP BY Category
    ) AS s
CROSS JOIN(
        SELECT SUM(CategoryCount) AS TotalCategoryCount
        FROM (
            SELECT Category, COUNT(Category) AS CategoryCount
            FROM Product
            WHERE ProductID IN (
                SELECT ProductID 
                FROM SuppliesProduct
                WHERE SupplierID = (
                    SELECT CompanyID 
                    FROM Company 
                    WHERE CompanyName = '".$companyName."'
                )
            )
            GROUP BY Category
        ) AS s
    ) AS t ";

// Distributors routes  
$sql4 = "SELECT F.CompanyName AS FromCompany, T.CompanyName As ToCompany
    FROM OperatesLogistics
    JOIN Company F ON F.CompanyID = OperatesLogistics.FromCompanyID
    JOIN Company T ON T.CompanyID = OperatesLogistics.ToCompanyID 
    WHERE DistributorID = (
    SELECT CompanyID
    FROM Company
    WHERE CompanyName = '".$companyName."'
    AND TYPE = 'Distributor' )
";

//Finhealth
$sql5 = "Select Quarter, RepYear, HealthScore From Company Inner Join FinancialReport On Company.CompanyID = FinancialReport.CompanyID 
WHERE ((FinancialReport.RepYear = '".$year1."' and FinancialReport.Quarter>= '".$quarter2."') AND CompanyName = '".$companyName."') ";

$sql6 = "Select Quarter, RepYear, HealthScore From Company Inner Join FinancialReport On Company.CompanyID = FinancialReport.CompanyID 
WHERE ((FinancialReport.RepYear = '".$year2."' and FinancialReport.Quarter<= '".$quarter2."') AND CompanyName = '".$companyName."') ";

$sql7 = "Select Quarter, RepYear, HealthScore From Company Inner Join FinancialReport On Company.CompanyID = FinancialReport.CompanyID 
WHERE (FinancialReport.RepYear > '".$year1."' and FinancialReport.RepYear < '".$year2."' AND CompanyName = '".$companyName."') ";

$sql8 = "Select Quarter, RepYear, HealthScore From Company Inner Join FinancialReport On Company.CompanyID = FinancialReport.CompanyID 
WHERE (FinancialReport.RepYear = '".$year2."' and FinancialReport.Quarter>= '".$quarter2."' and FinancialReport.Quarter<= '".$quarter2."' AND CompanyName = '".$companyName."') ";

$sql18 = "SELECT HealthScore
FROM Company
INNER JOIN FinancialReport ON Company.CompanyID = FinancialReport.CompanyID
WHERE FinancialReport.RepYear = '".$date5_f."'
AND FinancialReport.Quarter = 'Q4'
AND CompanyName = '".$companyName."' ";

// who depends on them 
$sql9 = "SELECT C2.CompanyName
FROM Company c
JOIN DependsOn D ON D.UpstreamCompanyID = c.CompanyID
JOIN Company C2 ON C2.CompanyID = D.DownstreamCompanyID
WHERE c.CompanyName = '".$companyName."'
ORDER BY c.CompanyName ";

//Who they depend on 
$sql10 = "SELECT C2.CompanyName
FROM Company c
JOIN DependsOn D ON D.DownstreamCompanyID = c.CompanyID
JOIN Company C2 ON C2.CompanyID = D.UpstreamCompanyID
WHERE c.CompanyName = '".$companyName."'
ORDER BY c.CompanyName ";

// avg and std of delay
$sql11= "SELECT 
    AVG(DATEDIFF(s.ActualDate, s.PromisedDate)) AS avg_delay_days,
    STDDEV(DATEDIFF(s.ActualDate, s.PromisedDate)) AS std_delay_days
FROM 
    Shipping AS s
    JOIN Company AS c ON s.SourceCompanyID = c.CompanyID
WHERE  
    c.CompanyName = '".$companyName."'
    AND s.ActualDate BETWEEN '".$date1_f."' AND '".$date2_f."'";

// percentage disruption events
$sql12 = "SELECT
    dc.CategoryName,
    COUNT(*) * 100.0 / total.TotalEvents AS Percent
FROM ImpactsCompany ic
JOIN DisruptionEvent de ON ic.EventID = de.EventID
JOIN DisruptionCategory dc ON de.CategoryID = dc.CategoryID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN (
    SELECT COUNT(*) AS TotalEvents
    FROM ImpactsCompany ic
    JOIN DisruptionEvent de ON ic.EventID = de.EventID
    JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
    WHERE c.CompanyName = '".$companyName."'
      AND de.EventDate BETWEEN '".$date1_f."' AND '".$date2_f."'
) AS total
WHERE
    c.CompanyName = '".$companyName."'
    AND de.EventDate BETWEEN '".$date1_f."' AND '".$date2_f."'
GROUP BY
    dc.CategoryID,
    dc.CategoryName,
    total.TotalEvents
ORDER BY 
	Percent DESC,
	dc.CategoryID ASC;";

//list of disruption events 
$sql17 = "SELECT
    de.EventDate,
    dc.CategoryName
FROM ImpactsCompany ic
JOIN DisruptionEvent de ON ic.EventID = de.EventID
JOIN DisruptionCategory dc ON de.CategoryID = dc.CategoryID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
WHERE 
    c.CompanyName = '".$companyName."'
    AND de.EventDate BETWEEN '".$date1_f."' AND '".$date2_f."'
ORDER BY 
    de.EventDate ASC";

// on-time delivery rate
$sql13 = "SELECT 
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
    JOIN Company AS c ON s.SourceCompanyID = c.CompanyID
WHERE 
    c.CompanyName = '".$companyName."'
    AND s.ActualDate BETWEEN '".$date1_f."' AND '".$date2_f."' ";


$sql19 = "SELECT 
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
    c.CompanyName = '".$companyName."'
    AND s.ActualDate BETWEEN '".$date1_f."' AND '".$date2_f."'"; 

//shipping table
$sql14 = "SELECT
    t.TransactionID,
    'Shipping' AS TransactionType,
    s.ActualDate AS TransactionDate,
    p.ProductName,
    src.CompanyName AS SourceCompanyName,
	rrc.CompanyName AS DestinationCompanyName,
    s.Quantity
FROM InventoryTransaction AS t
JOIN Shipping AS s
    ON t.TransactionID = s.TransactionID
JOIN Company AS src
    ON s.SourceCompanyID = src.CompanyID
JOIN Company AS rrc
    ON s.DestinationCompanyID = rrc.CompanyID
JOIN Product AS p
    ON s.ProductID = p.ProductID
WHERE t.Type = 'Shipping'
  AND s.ActualDate BETWEEN '".$date1_f."' AND '".$date2_f."'
  AND src.CompanyName = '".$companyName."'
ORDER BY s.ActualDate, t.TransactionID";

//reciving table
$sql15 = "SELECT
    r.ReceivedDate AS TransactionDate,
    p.ProductName,
    src.CompanyName As SourceCompanyName,
    rc.CompanyName AS DestinationCompanyName,
    r.QuantityReceived AS Quantity
FROM InventoryTransaction AS t
JOIN Receiving AS r
    ON t.TransactionID = r.TransactionID
JOIN Shipping AS s
    ON r.ShipmentID = s.ShipmentID
JOIN Product AS p
    ON s.ProductID = p.ProductID
JOIN Company AS src
    ON s.SourceCompanyID = src.CompanyID
JOIN Company AS rc
    ON r.ReceiverCompanyID = rc.CompanyID
WHERE t.Type = 'Receiving'
  AND r.ReceivedDate BETWEEN '".$date1_f."' AND '".$date2_f."'
  AND rc.CompanyName = '".$companyName."'
ORDER BY r.ReceivedDate, t.TransactionID;";

//adjustment table
$sql16 = "SELECT 
    a.AdjustmentDate AS TransactionDate,
    p.ProductName,
    c.CompanyName AS SourceCompanyName,
    a.QuantityChange AS Quantity,
    a.Reason
FROM InventoryTransaction AS t
JOIN InventoryAdjustment AS a
    ON t.TransactionID = a.TransactionID
JOIN Company AS c
    ON a.CompanyID = c.CompanyID
JOIN Product AS p
    ON a.ProductID = p.ProductID
WHERE t.Type = 'Adjustment'
  AND a.AdjustmentDate BETWEEN '".$date1_f."' AND '".$date2_f."'
  AND c.CompanyName = '".$companyName."'
ORDER BY a.AdjustmentDate, t.TransactionID";

$sql20 = "SELECT 
    AVG(DATEDIFF(s.ActualDate, s.PromisedDate)) AS avg_delay_days,
    STDDEV(DATEDIFF(s.ActualDate, s.PromisedDate)) AS std_delay_days
FROM 
    Shipping AS s
    JOIN Distributor AS d ON d.CompanyID = s.DistributorID
    JOIN Company AS c ON c.CompanyID = d.CompanyID
WHERE  
    c.CompanyName = '".$companyName."'
    AND s.ActualDate BETWEEN '".$date1_f."' AND '".$date2_f."'";

// run quieres 
$result0=mysqli_query($conn,$sql0);
$result1=mysqli_query($conn,$sql1);
$result2=mysqli_query($conn,$sql2);
$result3=mysqli_query($conn,$sql3);
$result4=mysqli_query($conn,$sql4);
$result5=mysqli_query($conn,$sql5);
$result6=mysqli_query($conn,$sql6);
$result7=mysqli_query($conn,$sql7);
$result8=mysqli_query($conn,$sql8);
$result9=mysqli_query($conn,$sql9);
$result10=mysqli_query($conn,$sql10);
$result11=mysqli_query($conn,$sql11);
$result12=mysqli_query($conn,$sql12);
$result17=mysqli_query($conn,$sql17);
$result13=mysqli_query($conn,$sql13);
$result14=mysqli_query($conn,$sql14);
$result15=mysqli_query($conn,$sql15);
$result16=mysqli_query($conn,$sql16);
$result18=mysqli_query($conn,$sql18);
$result19=mysqli_query($conn,$sql19);
$result20=mysqli_query($conn,$sql20);

$companyNameListALL = [];
$capcity = [];
$products = [];
$categories = [];
$percentages_catagory = [];
$route = [];
$quarters = [];
$scores = [];
$recentscore = [];
$whodependsonthem = [];
$dependson = [];
$std = [];
$avg = [];
$disruptioncategory = [];
$disruption_percentage = [];
$disruptioncategorylist = [];
$disruptiondate = [];
$ontimerate = [];
$DateR = [];
$ProductNameR = [];
$SourceCompanyNameR = [];
$DestinationCompanyNameR = [];
$QuantityR = [];
$DateS = [];
$ProductNameS = [];
$SourceCompanyNameS = [];
$DestinationCompanyNameS = [];
$QuantityS = [];
$DateA = [];
$ProductNameA = [];
$SourceCompanyNameA = [];
$DestinationCompanyNameA = [];
$QuantityA = [];

// Display results
//https://www.geeksforgeeks.org/php/associative-arrays-in-php
//https://www.ionos.com/digitalguide/websites/web-development/php-arrays
//https://www.w3schools.com/PHP/php_arrays_associative.asp
while ($row = mysqli_fetch_array($result1)) {
    // generation information about a company
    $basic = array(
        $row["CompanyName"],
        $row["TierLevel"],
        $row["Type"],
        $row["City"],
        $row["CountryName"],
        $row["ContinentName"],
    );
    $result = [
        "Company Name" => $basic[0], 
        "Tier" => $basic[1],
        "Type" => $basic[2], 
        "City" => $basic[3],
        "Country" => $basic[4],
        "Continent" => $basic[5]
    ];
    if($row["Type"] == "Manufacturer") {
        // factory capacity 
        $capcity[] = $row["FactoryCapacity"];
        $result["Factory Capacity"] = $capcity;
        //products list
        while ($row2 = mysqli_fetch_array($result2)) {
            $products[] = $row2["ProductName"];
        }
        //product categories as percentages
        $result["Products"] = $products;
        while ($row3 = $result3->fetch_assoc()) {
            $categories[] = $row3['Category'];
            $percentages_catagory[] = $row3['PER'];
        }
        $result["Categories Products"] = $categories;
        $result["Categories Products Percentages"] = $percentages_catagory;
        //on time delivery rate
        while ($row13 = $result13->fetch_assoc()) {
            $ontimerate[] = $row13["on_time_delivery_rate_percent"];
        }
        $result["On Time Delivery Rate Percent"] = $ontimerate;
        //avg and std of delay 
        while ($row11 = $result11->fetch_assoc()) {
            $avg[] = $row11["avg_delay_days"];
            $std[] = $row11["std_delay_days"];
        }
        $result["Average of Delay"] = $avg;
        $result["Standard Deviation of Delay"] = $std;
        //shipping
        while ($row14 = $result14->fetch_assoc()) {
            $DateS[] = $row14["TransactionDate"];
            $ProductNameS[] = $row14["ProductName"];
            $SourceCompanyNameS[] = $row14["SourceCompanyName"];
            $DestinationCompanyNameS[] = $row14["DestinationCompanyName"];
            $QuantityS[] = $row14["Quantity"];
        }
        $result["Shipping: Date"] = $DateS;
        $result["Shipping: Product Name"] = $ProductNameS;
        $result["Shipping: Soruce Company Name"] = $SourceCompanyNameS;
        $result["Shipping: Destination Company Name"] = $DestinationCompanyNameS;
        $result["Shipping: Quantity"] = $QuantityS;
    }
    if($row['Type'] == 'Distributor'){
        //routes 
        while ($row4 = $result4->fetch_assoc()) {
            $route[] = $row4["FromCompany"] . " to " . $row4["ToCompany"];
        }
        $result["Routes"] = $route;
        //on time delivery rate
        while ($row19 = $result19->fetch_assoc()) {
            $ontimerate[] = $row19["on_time_delivery_rate_percent"];
        }
        $result["On Time Delivery Rate Percent"] = $ontimerate;
        //avg and std of delay 
        while ($row20 = $result20->fetch_assoc()) {
            $avg[] = $row20["avg_delay_days"];
            $std[] = $row20["std_delay_days"];
        }
        $result["Average of Delay"] = $avg;
        $result["Standard Deviation of Delay"] = $std;
        //shipping
    }
    if($row['Type'] == 'Retailer' || $row['Type'] == 'Manufacturer' ) {
        //receving 
        while ($row15 = $result15->fetch_assoc()) {
            $DateR[] = $row15["TransactionDate"];
            $ProductNameR[] = $row15["ProductName"];
            $SourceCompanyNameR[] = $row15["SourceCompanyName"];
            $DestinationCompanyNameR[] = $row15["DestinationCompanyName"];
            $QuantityR[] = $row15["Quantity"];
        }
        $result["Receiving: Date"] = $DateR;
        $result["Receiving: Product Name"] = $ProductNameR;
        $result["Receiving: Soruce Company Name"] = $SourceCompanyNameR;
        $result["Receiving: Destination Company Name"] = $DestinationCompanyNameR;
        $result["Receiving: Quantity"] = $QuantityR; 
    }
}
if($year1 == $year2){
    //financial health a year from today 
    while ($row8 = $result8->fetch_assoc()) {
        $quarters[] = $row8["RepYear"] . "(" . $row8["Quarter"] . ")";
        $scores[] = $row8["HealthScore"];
    }
}
else {
    while ($row5 = $result5->fetch_assoc()) {
        $quarters[] = $row5["RepYear"] . "(" . $row5["Quarter"] . ")";
        $scores[] = $row5["HealthScore"];
    }
    while ($row7 = $result7->fetch_assoc()) {
        $quarters[] = $row7["RepYear"] . "(" . $row7["Quarter"] . ")";
        $scores[] = $row7["HealthScore"];
    }  
    while ($row6 = $result6->fetch_assoc()) {
        $quarters[] = $row6["RepYear"] . "(" . $row6["Quarter"] . ")";
        $scores[] = $row6["HealthScore"];
    }   
}
//most recent financial health status 
while ($row18 = $result18->fetch_assoc()) {
        $recentscore[] = $row18["HealthScore"];
}
// who depends on them 
while ($row9 = $result9->fetch_assoc()) {
    $whodependsonthem[] = $row9["CompanyName"];
}
//who they depend on 
while ($row10 = $result10->fetch_assoc()) {
    $dependson[] = $row10["CompanyName"];
}
$result["Quarters"] = $quarters;
$result["Scores"] = $scores;
$result["Most Recent Financial Health Score"] = $recentscore;
$result["Who depends on them"] = $whodependsonthem;
$result["Who they depend on"] = $dependson;
//disruption catagories as percents 
while ($row12 = $result12->fetch_assoc()) {
    $disruptioncategory[] = $row12["CategoryName"];
    $disruption_percentage[] = $row12["Percent"];
}
// list of disruption events 
while ($row17 = $result17->fetch_assoc()) {
    $disruptioncategorylist[] = $row17["CategoryName"];
    $disruptiondate[] = $row17["EventDate"];
}
$result["Disruption Category"] = $disruptioncategory;
$result["Disruption Percentage"] = $disruption_percentage;
$result["Disruption Category List"] = $disruptioncategorylist;
$result["Disruption Event Date"] = $disruptiondate;
//adjustments 
while ($row16 = $result16->fetch_assoc()) {
    $DateA[] = $row16["TransactionDate"];
    $ProductNameA[] = $row16["ProductName"];
    $SourceCompanyNameA[] = $row16["SourceCompanyName"];
    $DestinationCompanyNameA[] = $row16["Reason"];
    $QuantityA[] = $row16["Quantity"];
}
$result["Adjustment: Date"] = $DateA;
$result["Adjustment: Product Name"] = $ProductNameA;
$result["Adjustment: Soruce Company Name"] = $SourceCompanyNameA;
$result["Adjustment: Reason"] = $DestinationCompanyNameA;
$result["Adjustment: Quantity"] = $QuantityA;
while ($row0 = mysqli_fetch_array($result0)) {
    $companyNameListALL[] = $row0["CompanyName"];
}
//lsit of all companies for checking 
$result["Company List All:"] = $companyNameListALL;
echo json_encode($result);
$conn->close();
?>