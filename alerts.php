<?php

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

$Output = [];
$eventID = [];
$eventDate = [];
$EventRecoveryDate = [];
$EventType = [];

while ($row1 = mysqli_fetch_array($result1)) {
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


