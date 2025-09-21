<?php 

session_start();

// Connect to MySQL database
$servername = "sci-project.lboro.ac.uk";
$dbusername = "team018";
$dbpassword = "nkAfiVuTsC4Yw9LvLEgP";
$dbname = "team018";

$conn = mysqli_connect($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    die("". mysqli_connect_error());
}

$projectID = $_POST['projectListID'];

$query = "
SELECT employeeID
FROM projectEmployeeTable
WHERE projectID = $projectID

UNION

SELECT teamEmployeeTable.EmployeeID
FROM teamEmployeeTable
INNER JOIN projectTeamTable
ON teamEmployeeTable.teamID = projectTeamTable.teamID
WHERE projectTeamTable.projectID = $projectID
";

$result = mysqli_query($conn, $query);

$employeeIDs = array(); // Create an empty array to hold your data

while($row = mysqli_fetch_assoc($result)) {
    $employeeIDs[] = $row['employeeID']; // Add each employeeID to the array
}

// Query to select employee details based on employeeIDs
$query = "
SELECT employeeID, employeeFirstName, employeeSurname
FROM employeeTable
WHERE employeeID IN (" . implode(',', $employeeIDs) . ")
";

$result = mysqli_query($conn, $query);

$employeeDetails = array(); // Create an empty array to hold employee details

while($row = mysqli_fetch_assoc($result)) {
    $employeeDetails[] = $row; // Add each row to the array
}

$json = json_encode($employeeDetails); // Convert the array to a JSON string

echo $json; // Output the JSON string

mysqli_close($conn);

?>