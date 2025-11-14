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
$tmp=explode(',',$tmp);
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
$year2 = $date2->format('Y');
$peviousyear = min($year1, $year2) -1; 


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
    WHERE Company.CompanyName LIKE '".$companyName."'
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


// run quieres 
$result1=mysqli_query($conn,$sql1);
$result2=mysqli_query($conn,$sql2);
$result3=mysqli_query($conn,$sql3);



// Display results
$output = array();
if (!$result1 || $result1->num_rows == 0) {
    echo json_encode("Company Name Not Found.");
    exit();
}
else {
    while ($row = mysqli_fetch_array($result1)) {
        $output[] = $row["CompanyName"];
        $output[] =  $row['TierLevel']; 
        $output[] =  $row['Type']; 
        $output[] =  $row["City"];
        $output[] =  $row["CountryName"];
        $output[] = $row["ContinentName"];
        //$products = array();
        if($row["Type"] == "Manufacturer") {
            $output[] = $row["FactoryCapacity"];
            while ($row2 = mysqli_fetch_array($result2)) {
                $output[] =  $row2["ProductName"];
            }
            $countcatagories = array();
            while ($row3 = $result3->fetch_assoc()) {
                $output[] = $row3['category'];
                $countcatagories[] = $row3['count(Category)'];
            }
            foreach ($countcatagories as $value)
            $output[] = ($value/array_sum($countcatagories))*100;
        }
    }
}
echo json_encode($output);
$conn->close();
?>