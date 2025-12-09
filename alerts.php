<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Database connection
$servername = "mydb.itap.purdue.edu";
$username   = "g1151939";
$password   = "Group29jjmsio";
$database   = $username;

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql1 = "SELECT
    e.EventId,
    e.EventDate,
    e.EventRecoveryDate,
    dc.CategoryName AS EventType
FROM DisruptionEvent AS e
JOIN DisruptionCategory AS dc
    ON dc.CategoryID = e.CategoryID
WHERE
    e.EventRecoveryDate IS NULL
    OR e.EventRecoveryDate >= CURRENT_DATE
    OR e.EventDate >= CURRENT_DATE
ORDER BY e.EventDate DESC ";

$result1 = mysqli_query($conn, $sql1);

$Output = array();
$eventID = array();
$eventDate = array();
$EventRecoveryDate = array();
$EventType = array();

while ($row1 = $result1->fetch_assoc()) {
    $eventID[] = $row1['EventId'];
    $eventDate[] = $row1['EventDate'];
    $EventRecoveryDate[] = $row1['EventRecoveryDate'];
    $EventType[] = $row1['EventType'];
}
$Output['Event ID'] = $eventID;
$Output['Event Date'] = $eventDate;
$Output['Event Recovery Date'] = $EventRecoveryDate;
$Output['Event Type'] = $EventType;

echo json_encode($Output);
?>


