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

$tmp= $_GET['q'];
$tmp=explode('|',$tmp);
$companyName = $tmp[2];
date_default_timezone_set('America/New_York'); 
if (!empty($tmp[0]) && !empty($tmp[1])) {
    $date1 = DateTime::createFromFormat('Y-m-d', $tmp[0]);
    $date2 = DateTime::createFromFormat('Y-m-d',$tmp[1]);
}
else {
    $date1 = new DateTime();
    $date2 = $date1->modify('-1 year');
    $date1 = new DateTime();

}
$year1 = $date1->format('Y');
$month1 = intval($date1->format('m'));
$year2 = $date2->format('Y');
$month2 = intval($date2->format('m'));

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

 
// Query 1 For Canpany Nane,Location, Manufacturer. 
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

// Query 3 for Products 
$sql2 = " SELECT ProductName 
            FROM Product 
            WHERE productid in (SELECT ProductID FROM SuppliesProduct Where SupplierID = (SELECT CompanyID FROM Company where CompanyName = '".$companyName."'))
";

$sql3 = "Select category, count(Category) 
        FROM Product 
        WHERE productid in 
        (select productId from SuppliesProduct 
        where supplierid = (select companyid from Company where companyname = '".$companyName."')) 
        Group By Category
";

// Query 4 for Distributors routes  
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

$sql5 = "Select Quarter, RepYear, HealthScore From Company Inner Join FinancialReport On Company.CompanyID = FinancialReport.CompanyID 
WHERE ((FinancialReport.RepYear = '".$year1."' and FinancialReport.Quarter>= '".$quarter1."') AND CompanyName = '".$companyName."') ";

$sql6 = "Select Quarter, RepYear, HealthScore From Company Inner Join FinancialReport On Company.CompanyID = FinancialReport.CompanyID 
WHERE ((FinancialReport.RepYear = '".$year2."' and FinancialReport.Quarter<= '".$quarter2."') AND CompanyName = '".$companyName."') ";

$sql7 = "Select Quarter, RepYear, HealthScore From Company Inner Join FinancialReport On Company.CompanyID = FinancialReport.CompanyID 
WHERE (FinancialReport.RepYear > '".$year1."' and FinancialReport.RepYear < '".$year2."' AND CompanyName = '".$companyName."') ";

$sql8 = "Select Quarter, RepYear, HealthScore From Company Inner Join FinancialReport On Company.CompanyID = FinancialReport.CompanyID 
WHERE (FinancialReport.RepYear = '".$year2."' and FinancialReport.Quarter>= '".$quarter1."' and FinancialReport.Quarter<= '".$quarter2."' AND CompanyName = '".$companyName."') ";

// run quieres 
$result1=mysqli_query($conn,$sql1);
$result2=mysqli_query($conn,$sql2);
$result3=mysqli_query($conn,$sql3);
$result4=mysqli_query($conn,$sql4);
$result5=mysqli_query($conn,$sql5);
$result6=mysqli_query($conn,$sql6);
$result7=mysqli_query($conn,$sql7);
$result8=mysqli_query($conn,$sql8);


// Display results
$output = array();
if (!$result1 || $result1->num_rows == 0) {
    echo json_encode("Company Name Not Found.");
    exit();
}
else {
    $preput = array();
    while ($row = mysqli_fetch_array($result1)) {
        $basic = array(
            $row["CompanyName"],
            $row["TierLevel"],
            $row["Type"],
            $row["City"],
            $row["CountryName"],
            $row["ContinentName"],
        );
        $preput[] = $basic;
        if($row["Type"] == "Manufacturer") {
            $preput[] = $row["FactoryCapacity"];
            $products = array();
            while ($row2 = mysqli_fetch_array($result2)) {
                $products[] = $row2["ProductName"];
            }
            $preput[] = $products;
            $countcategories = array();
            $category = array();
            $percentages = array();
            while ($row3 = $result3->fetch_assoc()) {
                $category[] = $row3['category'];
                $countcategories[] = $row3['count(Category)'];
            }
            $preput[] = $category;
            foreach ($countcategories as $value) {
                $percentages[] = ($value/array_sum($countcategories))*100;
            }
            $preput[] = $percentages;
        }
        $route = array();
        if($row['Type'] == 'Distributor'){
            while ($row4 = $result4->fetch_assoc()) {
                $quarters[] = $row8["RepYear"] . "(" . $row8["Quarter"] . ")";
            }
            $preput[] = $route;
        }
        $quarters = array();
        $scores = array();
        if($year1 == $year2){
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
        $preput[] = $quarters;
        $preput[] = $scores;
        $output[] = $preput;
    }
}
echo json_encode($output);
$conn->close();
?>