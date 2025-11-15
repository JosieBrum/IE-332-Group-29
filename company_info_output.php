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

// run quieres 
$result1=mysqli_query($conn,$sql1);
$result2=mysqli_query($conn,$sql2);
$result3=mysqli_query($conn,$sql3);
$result4=mysqli_query($conn,$sql4);


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
                $route[] = $row4["FromCompany"] . " to " . $row4["ToCompany"];
            }
            $preput[] = $route;
        }
        $output[] = $preput;
    }
}
echo json_encode($output);
$conn->close();
?>

?>
